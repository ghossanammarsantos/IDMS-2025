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
        {--voyage=}
        {--sender=}
        {--recipient=}
        {--carrier=}';

    protected $description = 'Generate EDI CODECO (format sesuai dokumen, sumber ANN_REPORT/ANN_IMPORT, anti-duplikasi)';

    public function handle(CodecoBuilder $builder, CodecoDataService $data)
    {
        $tz     = 'Asia/Jakarta';
        $event  = EdiEvent::fromOption($this->option('event'));
        $date   = $this->option('date'); // nullable

        // Waktu pembuatan
        $now   = now($tz);
        $icRef = 'O' . $now->format('ymdHi');   // OYYMMDDHHMM (contoh: O2505091630)
        $msgRf = $icRef . '001';                // message ref (UNH)
        // DOCNO 13-digit ala contoh: YYMMDDHHMM + 3-digit unik (pakai 403 sebagai contoh)
        $docNo = $now->format('ymdHi') . '403';

        // Ambil data HARI INI (atau tanggal yg diberikan)
        $dateYmd = $date ? Carbon::parse($date, $tz)->format('Y-m-d') : null;
        $rows    = $data->fetchToday($event, $dateYmd);

        if ($rows->isEmpty()) {
            $this->warn("Tidak ada data untuk {$event} pada " . ($date ?: $now->format('Y-m-d')));
            return 0;
        }

        // Customer code & voyage
        $customerCode   = $data->inferCustomerCode($rows, null);       // untuk UNB/NAD MR/CF
        $voyageFromData = $data->inferVoyage($rows, $this->option('voyage'));

        // Header untuk builder (sesuai format dokumen)
        $header = [
            'customer_code'   => $customerCode,
            'voyage'          => $voyageFromData,
            'created_at'      => $now,
            'interchange_ref' => $icRef,
            'message_ref'     => $msgRf,
            'document_no'     => $docNo,
            'sender'          => $this->option('sender'),
            'recipient'       => $this->option('recipient'),
            'carrier'         => $this->option('carrier'),
        ];

        // Mapping baris kontainer → array of arrays sesuai builder
        $containers = $rows->map(function ($r) use ($tz) {
            return [
                'container_no'     => strtoupper((string) $r->container_no),
                'iso'              => strtoupper((string) $r->iso),
                'booking_no'       => (string) ($r->booking_no ?? ''),
                'event_dt'         => Carbon::parse($r->gate_time, $tz),

                // lokasi/depo (tetap sesuai dokumen)
                'port_code'        => 'IDBTM',
                'depot_code'       => 'TBMIDBTM',

                // measurement & attributes
                'gross_weight'     => $r->maxgross ?? $r->payload ?? null,
                'payload'          => $r->payload ?? null,
                'tare'             => $r->tare ?? null,
                'maxgross'         => $r->maxgross ?? null,

                'status_container' => $r->status_container ?? null,
                'grade_container'  => $r->grade_container ?? null,

                // voyage & consignee dari sumber ANN_*
                'voyage'           => $r->voyage ?? null,
                'consignee'        => $r->consignee ?? null,

                // ship_id opsional (kalau nanti ada kolom)
                // 'ship_id'          => $r->ship_id ?? null,
            ];
        })->values()->all();

        // Build EDI
        $ediText = $builder->build($header, $containers);

        // Simpan file
        $outfile = storage_path('app/edi/TBMIDBTM_CODECO_' . $now->format('ymdHi') . '.txt');
        @mkdir(dirname($outfile), 0775, true);
        file_put_contents($outfile, $ediText);

        // Hitung jumlah Gate IN & OUT untuk tanggal yang sama
        $dateForCount = $date ? Carbon::parse($date, $tz)->format('Y-m-d') : $now->format('Y-m-d');
        if ($event === EdiEvent::IN) {
            $countIn  = $rows->count();
            $countOut = $data->fetchToday(EdiEvent::OUT, $dateForCount)->count();
        } else {
            $countOut = $rows->count();
            $countIn  = $data->fetchToday(EdiEvent::IN, $dateForCount)->count();
        }

        $this->info("Generated: {$outfile}");
        $this->line("Gate IN : {$countIn}");
        $this->line("Gate OUT: {$countOut}");

        return 0;
    }
}
