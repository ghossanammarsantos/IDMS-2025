<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\Edi\CodecoBuilder;
use Carbon\Carbon;

class GenerateCodeco extends Command
{
    protected $signature = 'edi:codeco:generate 
        {--event=OUT : IN/OUT}
        {--date= : Tanggal (Y-m-d), default: hari ini WIB}
        {--sender=TBMIDBTM}
        {--recipient=HMM}
        {--carrier=HMM}
        {--voyage=1}
        {--port=IDBTM}
    ';

    protected $description = 'Generate file EDIFACT CODECO (D.95B) dari data DB untuk tanggal/event tertentu';

    /* =======================
     * Helpers: ISO & Validasi
     * ======================= */

    // Normalisasi size/type operasional (20GP, 40HC, 40GP, 40DV, 45HC, ...) ke ISO 4-digit
    private function isoFromSizeType(?string $st): string
    {
        $st = strtoupper(trim((string) $st));
        if ($st === '') return '45G1'; // default aman (mis. 40HC umum)

        // Jika sudah 4-karakter alfanumerik, biarkan
        if (preg_match('/^[A-Z0-9]{4}$/', $st)) return $st;

        // Mapping umum
        $map = [
            '20GP' => '22G1',
            '20DV' => '22G1',
            '40GP' => '42G1',
            '40DV' => '42G1',
            '40HC' => '45G1',
            '45HC' => 'L5G1',
        ];
        if (isset($map[$st])) return $map[$st];

        // Fallback sederhana utk {20|40}{GP|DV}
        if (preg_match('/^(20|40)(GP|DV)$/', $st, $m)) {
            return ($m[1] === '40' ? '42' : '22') . 'G1';
        }

        return $st; // biarkan apa adanya bila tak dikenali
    }

    // Validasi ringan nomor kontainer (4 huruf + 7 digit)
    private function isContainerFormatOk(?string $no): bool
    {
        return (bool) preg_match('/^[A-Z]{4}\d{7}$/', strtoupper((string) $no));
    }

    // Hitung gross weight: prioritas MAXGROSS -> (PAYLOAD+TARE) -> gross_weight langsung
    private function computeGrossWeight($gross = null, $payload = null, $tare = null, $maxgross = null): ?int
    {
        if (is_numeric($maxgross) && (float)$maxgross > 0) {
            return (int) round((float) $maxgross);
        }
        if (is_numeric($payload) && is_numeric($tare)) {
            $sum = (float) $payload + (float) $tare;
            if ($sum > 0) return (int) round($sum);
        }
        if (is_numeric($gross) && (float)$gross > 0) {
            return (int) round((float) $gross);
        }
        return null;
    }

    public function handle(CodecoBuilder $builder): int
    {
        $tz    = 'Asia/Jakarta';
        $day   = $this->option('date') ? Carbon::parse($this->option('date'), $tz) : now($tz);
        $event = strtoupper($this->option('event')) === 'IN' ? 'IN' : 'OUT';

        // Header EDIFACT (ref mengikuti pola contoh Word/file)
        $now   = now($tz);
        $icRef = 'O' . $now->format('ymdHi');   // O2505091344
        $msgRf = $icRef . '001';                // O2505091344001
        $docNo = $now->format('ymdHis');        // 250509134413

        // default voyage dari CLI; nanti akan dioverride oleh ANN_IMPORT jika tersedia
        $headerVoy = $this->option('voyage');

        $header = [
            'sender'          => $this->option('sender'),
            'recipient'       => $this->option('recipient'),
            'carrier'         => $this->option('carrier'),
            'voyage'          => $headerVoy,
            'created_at'      => $now,
            'interchange_ref' => $icRef,
            'message_ref'     => $msgRf,
            'document_no'     => $docNo,
        ];

        /* ============================
         * 1) AMBIL DATA DARI ORACLE
         * ============================ */
        $connection = 'oracle';
        $schema     = 'C##IDMS2024';
        $dateStr    = $day->format('Y-m-d');

        if ($event === 'IN') {
            // GATE IN: join SURVEYIN utk weight & status/grade; join ANN_IMPORT utk consignee & voyage
            $rows = DB::connection($connection)
                ->table(DB::raw("$schema.GATE_IN g"))
                ->leftJoin(DB::raw("$schema.SURVEYIN s"), DB::raw("s.NO_CONTAINER"), '=', DB::raw("g.NO_CONTAINER"))
                ->leftJoin(DB::raw("$schema.ANN_IMPORT a"), DB::raw("a.NO_BLDO"), '=', DB::raw("g.NO_BLDO"))
                ->whereRaw("TRUNC(g.GATEIN_TIME) = TO_DATE(?, 'YYYY-MM-DD')", [$dateStr])
                ->selectRaw("
                    g.NO_CONTAINER          AS container_no,
                    g.SIZE_TYPE             AS iso,           -- dari GATE_IN
                    NULL                    AS fe_status,
                    g.NO_BLDO               AS booking_no,
                    g.GATEIN_TIME           AS gate_time,
                    NULL                    AS gross_weight,  -- tidak ada di GATE_IN
                    s.PAYLOAD               AS payload,       -- dari SURVEYIN
                    s.TARE                  AS tare,          -- dari SURVEYIN
                    s.MAXGROSS              AS maxgross,      -- dari SURVEYIN
                    s.STATUS_CONTAINER      AS status_container,
                    s.GRADE_CONTAINER       AS grade_container,
                    a.CONSIGNEE             AS consignee,     -- dari ANN_IMPORT
                    a.VOYAGE                AS voyage         -- dari ANN_IMPORT
                ")
                ->orderBy(DB::raw("g.GATEIN_TIME"))
                ->get();
        } else {
            // GATE OUT: ISO & weight & status/grade via SURVEYIN; consignee & voyage via ANN_IMPORT
            $rows = DB::connection($connection)
                ->table(DB::raw("$schema.GATE_OUT g"))
                ->leftJoin(DB::raw("$schema.SURVEYIN s"), DB::raw("s.NO_CONTAINER"), '=', DB::raw("g.NO_CONTAINER"))
                ->leftJoin(DB::raw("$schema.ANN_IMPORT a"), DB::raw("a.NO_BLDO"), '=', DB::raw("g.NO_BLDO"))
                ->whereRaw("TRUNC(g.GATEOUT_TIME) = TO_DATE(?, 'YYYY-MM-DD')", [$dateStr])
                ->selectRaw("
                    g.NO_CONTAINER          AS container_no,
                    s.SIZE_TYPE             AS iso,           -- via SURVEYIN
                    NULL                    AS fe_status,
                    g.NO_BLDO               AS booking_no,
                    g.GATEOUT_TIME          AS gate_time,
                    NULL                    AS gross_weight,  -- tidak ada di GATE_OUT
                    s.PAYLOAD               AS payload,       -- dari SURVEYIN
                    s.TARE                  AS tare,          -- dari SURVEYIN
                    s.MAXGROSS              AS maxgross,      -- dari SURVEYIN
                    s.STATUS_CONTAINER      AS status_container,
                    s.GRADE_CONTAINER       AS grade_container,
                    a.CONSIGNEE             AS consignee,     -- dari ANN_IMPORT
                    a.VOYAGE                AS voyage         -- dari ANN_IMPORT
                ")
                ->orderBy(DB::raw("g.GATEOUT_TIME"))
                ->get();
        }

        /* Override voyage header dari ANN_IMPORT (ambil yang pertama tidak kosong) */
        foreach ($rows as $r0) {
            if (!empty($r0->voyage)) {
                $header['voyage'] = $r0->voyage;
                break;
            }
        }

        /* ============================
         * 2) MAPPING KE BUILDER
         * ============================ */
        $port  = strtoupper($this->option('port') ?: 'IDBTM');
        $depot = strtoupper($this->option('sender') ?: 'TBMIDBTM');

        $containers = [];
        foreach ($rows as $r) {
            // Status EDIFACT (F/E): default Full=4 (belum ada sumber FE khusus)
            $status = '4';

            // ISO: normalisasi dari size/type operasional -> ISO 4-digit
            $rawIso = $r->iso ? trim($r->iso) : '';
            $iso    = $this->isoFromSizeType($rawIso);

            // Container number: uppercase + validasi ringan
            $cn = strtoupper((string) $r->container_no);
            if (!$this->isContainerFormatOk($cn)) {
                $this->warn("Format nomor kontainer tidak sesuai 4L+7D: {$cn}");
            }

            // Gross weight untuk MEA: maxgross -> payload+tare -> gross_weight
            $gross = $this->computeGrossWeight($r->gross_weight ?? null, $r->payload ?? null, $r->tare ?? null, $r->maxgross ?? null);

            $eventDt = Carbon::parse($r->gate_time, $tz);

            $containers[] = [
                'container_no'     => $cn,
                'iso'              => $iso,
                'status'           => $status,
                'booking_no'       => $r->booking_no ?: '',
                'event_dt'         => $eventDt,
                'port_code'        => $port,
                'depot_code'       => $depot,
                'gross_weight'     => $gross, // => MEA bila tidak null
                'status_container' => $r->status_container ? strtoupper($r->status_container) : '',
                'grade_container'  => $r->grade_container ? strtoupper($r->grade_container) : '',
                'voyage'           => $r->voyage ?: '',       // untuk TDT per-container
                'consignee'        => $r->consignee ?: null,  // NAD+CZ jika ada
            ];
        }

        /* ============================
         * 3) BANGUN & SIMPAN FILE
         * ============================ */
        $edi  = $builder->build($header, $containers);
        $gate = $event === 'IN' ? 'GATEIN' : 'GATEOUT';
        $fname = sprintf('%s_%s_%s.txt', $header['sender'], $gate, $now->format('ymdHi'));

        Storage::disk('local')->put("edi/{$fname}", $edi);
        $full = storage_path("app/edi/{$fname}");

        $this->info("Generated: {$full}");
        $this->info("Containers: " . count($containers));
        return Command::SUCCESS;
    }
}
