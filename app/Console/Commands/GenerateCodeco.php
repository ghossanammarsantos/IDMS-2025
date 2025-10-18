<?php

namespace App\Console\Commands;

use App\Support\EdiEvent;
use App\Services\Edi\CodecoBuilder;
use App\Services\Edi\CodecoDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateCodeco extends Command
{
    protected $signature = 'edi:codeco:generate
        {--event=IN : IN atau OUT}
        {--date= : Tanggal (YYYY-MM-DD). Jika kosong, pakai hari ini}
        {--customer= : Filter satu shipping line (CUSTOMER_CODE), contoh HMM}
        {--split-by-customer : Paksa generate banyak file, satu per shipping line}
        {--voyage=}
        {--sender=}
        {--recipient=}
        {--carrier=}';

    protected $description = 'Generate EDI CODECO sesuai dokumen; split per shipping line dan upload SFTP untuk HMM';

    public function handle(CodecoBuilder $builder, CodecoDataService $data)
    {
        $tz       = 'Asia/Jakarta';
        $event    = EdiEvent::fromOption($this->option('event'));   // biasanya "IN" / "OUT"
        $eventStr = strtoupper((string) $event);                    // pastikan string untuk builder & nama file

        $dateOpt  = $this->option('date');          // nullable
        $customer = $this->option('customer');      // nullable
        $split    = (bool) $this->option('split-by-customer'); // (opsional; tidak dipakai saat ini)

        // Waktu pembuatan (untuk UNB/BGM dan penamaan file)
        $now   = now($tz);
        $icRef = 'O' . $now->format('ymdHi');   // OYYMMDDHHMM
        $msgRf = $icRef . '001';
        // DOCNO 13-digit ala contoh (YYMMDDHHMM + 3 digit unik)
        $docNo = $now->format('ymdHi') . '403';

        $dateYmd = $dateOpt ? Carbon::parse($dateOpt, $tz)->format('Y-m-d') : null;

        // Ambil rows (boleh difilter 1 customer; kalau null → ambil semua)
        $rows = $data->fetchToday($eventStr, $dateYmd, $customer);

        if ($rows->isEmpty()) {
            $this->warn(
                "Tidak ada data untuk {$eventStr} pada " .
                    ($dateOpt ?: $now->format('Y-m-d')) .
                    ($customer ? " (customer={$customer})" : "")
            );
            return 0;
        }

        // Penentuan grouping: jika --customer diisi → 1 group; jika tidak → group by customer_code
        $groups = $customer
            ? collect([strtoupper($customer) => $rows])
            : $rows->groupBy(function ($r) {
                return strtoupper($r->customer_code ?: 'UNKNOWN');
            });

        $totalFiles = 0;
        $totalIn    = 0;
        $totalOut   = 0;

        foreach ($groups as $custCode => $group) {
            $custCodeUp     = strtoupper($custCode);
            $voyageFromData = $data->inferVoyage($group, $this->option('voyage'));

            // Header untuk builder (override manual via options bila diisi)
            $header = [
                'customer_code'   => $custCodeUp === 'UNKNOWN' ? null : $custCodeUp,
                'voyage'          => $voyageFromData,
                'created_at'      => $now,
                'interchange_ref' => $icRef,
                'message_ref'     => $msgRf,
                'document_no'     => $docNo,
                'sender'          => $this->option('sender'),
                'recipient'       => $this->option('recipient'),
                'carrier'         => $this->option('carrier'),
            ];

            // Map containers → array of arrays (sesuai builder)
            $containers = $group->map(function ($r) use ($tz) {
                return [
                    'container_no'     => strtoupper((string) $r->container_no),
                    'iso'              => strtoupper((string) $r->iso),
                    'booking_no'       => (string) ($r->booking_no ?? ''),
                    'event_dt'         => $r->gate_time instanceof Carbon
                        ? $r->gate_time
                        : Carbon::parse($r->gate_time, $tz),

                    // default; builder dapat override untuk profil tertentu (mis. SIT → IDBAT/TBMIDBATM)
                    'port_code'        => 'IDBTM',
                    'depot_code'       => 'TBMIDBTM',

                    'gross_weight'     => $r->maxgross ?? $r->payload ?? null,
                    'payload'          => $r->payload ?? null,
                    'tare'             => $r->tare ?? null,
                    'maxgross'         => $r->maxgross ?? null,

                    'status_container' => $r->status_container ?? null,
                    'grade_container'  => $r->grade_container ?? null,

                    'voyage'           => $r->voyage ?? null,
                    'consignee'        => $r->consignee ?? null,
                ];
            })->values()->all();

            // >>> Perbaikan utama: panggil builder DENGAN argumen event <<<
            $ediText = $builder->build($header, $containers, $eventStr);

            $ediText = $builder->build($header, $containers, $event);

            // ==== Penamaan file lokal ====
            // Khusus SIT: pakai awalan TBMIDBAT_ (bukan TBMIDBTM_)
            // Selain SIT tetap TBMIDBTM_
            $custSlug   = $custCodeUp === 'UNKNOWN' ? 'UNKNOWN' : preg_replace('/[^A-Z0-9]+/', '', $custCodeUp);
            $prefixTerm = ($custCodeUp === 'SIT') ? 'TBMIDBAT' : 'TBMIDBTM';
            $localName  = $prefixTerm . '_CODECO_' . $custSlug . '_' . $event . '_' . $now->format('ymdHi') . '.txt';
            $outfile    = storage_path('app/edi/' . $localName);
            @mkdir(dirname($outfile), 0775, true);
            file_put_contents($outfile, $ediText);

            $this->info("Generated: {$outfile}");
            $this->line("Customer : {$custCodeUp}");
            $this->line(($eventStr === 'IN' ? "Gate IN  : " : "Gate OUT : ") . count($containers));

            // =========================
            // Upload ke SFTP khusus HMM
            // =========================
            if ($custCodeUp === 'HMM') {
                try {
                    $remoteName = $localName;

                    // contoh jika ingin subfolder harian:
                    // $folder = $now->format('Ymd');
                    // if (!Storage::disk('sftp_hmm')->exists($folder)) {
                    //     Storage::disk('sftp_hmm')->makeDirectory($folder);
                    // }
                    // $remoteName = $folder . '/' . $localName;

                    $ok = Storage::disk('sftp_hmm')->put($remoteName, $ediText);

                    if ($ok) {
                        $this->info("Uploaded to SFTP (HMM): {$remoteName}");
                    } else {
                        $this->error("Upload to SFTP (HMM) FAILED: {$remoteName}");
                    }
                } catch (\Throwable $e) {
                    $this->error("SFTP (HMM) error: " . $e->getMessage());
                }
            }

            // Akumulasi rekap
            if ($eventStr === 'IN') {
                $totalIn += count($containers);
            } else {
                $totalOut += count($containers);
            }
            $totalFiles++;
        }

        $this->line(str_repeat('-', 40));
        $this->line("Total files : {$totalFiles}");
        if ($eventStr === 'IN') {
            $this->line("Total Gate IN  : {$totalIn}");
        } else {
            $this->line("Total Gate OUT : {$totalOut}");
        }

        return 0;
    }
}
