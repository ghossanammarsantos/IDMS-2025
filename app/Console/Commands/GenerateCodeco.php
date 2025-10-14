<?php

namespace App\Console\Commands;

use App\Support\EdiEvent;
use App\Services\Edi\CodecoBuilder;
use App\Services\Edi\CodecoDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

    protected $description = 'Generate EDI CODECO sesuai dokumen; support split per shipping line (CUSTOMER_CODE)';

    public function handle(CodecoBuilder $builder, CodecoDataService $data)
    {
        $tz       = 'Asia/Jakarta';
        $event    = EdiEvent::fromOption($this->option('event'));
        $dateOpt  = $this->option('date');          // nullable
        $customer = $this->option('customer');      // nullable
        $split    = (bool) $this->option('split-by-customer');

        // Waktu pembuatan (untuk UNB/BGM dan penamaan file)
        $now   = now($tz);
        $icRef = 'O' . $now->format('ymdHi');   // OYYMMDDHHMM
        $msgRf = $icRef . '001';
        $docNo = $now->format('ymdHi') . '403'; // 13-digit ala contoh (YYMMDDHHMM + 3 digit)

        $dateYmd = $dateOpt ? Carbon::parse($dateOpt, $tz)->format('Y-m-d') : null;

        // Ambil rows (boleh difilter 1 customer; kalau null → ambil semua)
        $rows = $data->fetchToday($event, $dateYmd, $customer);

        if ($rows->isEmpty()) {
            $this->warn("Tidak ada data untuk {$event} pada " . ($dateOpt ?: $now->format('Y-m-d')) . ($customer ? " (customer={$customer})" : ""));
            return 0;
        }

        // Tentukan mode:
        // - Jika user set --customer, maka 1 file saja untuk customer tsb.
        // - Jika tidak set --customer, default: split per customer ditemukan (atau pakai --split-by-customer).
        $groups = [];
        if ($customer || $split || true) {
            // group by customer_code (NULL/empty dikelompokkan sebagai 'UNKNOWN')
            $groups = $rows->groupBy(function ($r) {
                return strtoupper($r->customer_code ?: 'UNKNOWN');
            });
        } else {
            // (opsi ini tidak akan terpakai karena kita selalu split; taruh untuk fleksibilitas)
            $groups = collect(['ALL' => $rows]);
        }

        $totalFiles = 0;
        $totalIn    = 0;
        $totalOut   = 0;

        foreach ($groups as $custCode => $group) {
            // Header khusus group
            $voyageFromData = $data->inferVoyage($group, $this->option('voyage'));
            $header = [
                'customer_code'   => $custCode === 'UNKNOWN' ? null : $custCode,
                'voyage'          => $voyageFromData,
                'created_at'      => $now,
                'interchange_ref' => $icRef,
                'message_ref'     => $msgRf,
                'document_no'     => $docNo,
                'sender'          => $this->option('sender'),
                'recipient'       => $this->option('recipient'),
                'carrier'         => $this->option('carrier'),
            ];

            // Map containers
            $containers = $group->map(function ($r) use ($tz) {
                return [
                    'container_no'     => strtoupper((string) $r->container_no),
                    'iso'              => strtoupper((string) $r->iso),
                    'booking_no'       => (string) ($r->booking_no ?? ''),
                    'event_dt'         => Carbon::parse($r->gate_time, $tz),

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

            // Build text
            $ediText = $builder->build($header, $containers);

            // Simpan file per shipping line
            $custSlug = $custCode === 'UNKNOWN' ? 'UNKNOWN' : preg_replace('/[^A-Z0-9]+/', '', strtoupper($custCode));
            $outfile  = storage_path('app/edi/TBMIDBTM_CODECO_' . $custSlug . '_' . $event . '_' . $now->format('ymdHi') . '.txt');
            @mkdir(dirname($outfile), 0775, true);
            file_put_contents($outfile, $ediText);

            $this->info("Generated: {$outfile}");
            $this->line("Customer : {$custCode}");
            if ($event === EdiEvent::IN) {
                $this->line("Gate IN  : " . count($containers));
                $totalIn += count($containers);
            } else {
                $this->line("Gate OUT : " . count($containers));
                $totalOut += count($containers);
            }

            $totalFiles++;
        }

        $this->line(str_repeat('-', 40));
        $this->line("Total files : {$totalFiles}");
        if ($event === EdiEvent::IN) {
            $this->line("Total Gate IN  : {$totalIn}");
        } else {
            $this->line("Total Gate OUT : {$totalOut}");
        }

        return 0;
    }
}
