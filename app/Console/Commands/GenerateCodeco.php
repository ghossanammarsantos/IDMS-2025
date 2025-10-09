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

    public function handle(CodecoBuilder $builder): int
    {
        $tz  = 'Asia/Jakarta';
        $day = $this->option('date') ? Carbon::parse($this->option('date'), $tz) : now($tz);
        $event = strtoupper($this->option('event')) === 'IN' ? 'IN' : 'OUT';

        // Header EDIFACT (ref mengikuti pola contoh Word/file)
        $now   = now($tz);
        $icRef = 'O' . $now->format('ymdHi');        // O2505091630
        $msgRf = $icRef . '001';                     // O2505091630001
        $docNo = $now->format('ymdHis');             // 2505091630403

        $header = [
            'sender'          => $this->option('sender'),
            'recipient'       => $this->option('recipient'),
            'carrier'         => $this->option('carrier'),
            'voyage'          => $this->option('voyage'),
            'created_at'      => $now,
            'interchange_ref' => $icRef,
            'message_ref'     => $msgRf,
            'document_no'     => $docNo,
        ];

        // === 1) AMBIL DATA DARI DB ===
        // TODO: sesuaikan nama tabel & kolom di sini agar cocok dengan skema kamu.
        // Asumsi: tabel 'surveyin_details' menyimpan gate IN hari ini (ganti sesuai milikmu).
        $dateStr = $day->format('Y-m-d');
        $rows = DB::table('surveyin_details') // <-- GANTI dengan tabelmu
            ->whereDate('gate_time', $dateStr) // <-- GANTI kolom waktu gate in/out
            ->when($event === 'IN', fn($q) => $q->where('gate_direction', 'IN'))  // <-- GANTI jika perlu
            ->when($event === 'OUT', fn($q) => $q->where('gate_direction', 'OUT')) // <-- GANTI jika perlu
            ->selectRaw("
                container_no       as container_no,
                iso_code           as iso,          -- GANTI kolom iso
                status_full_empty  as fe_status,    -- 'F'/'E' atau 4/1 (GANTI)
                booking_no         as booking_no,   -- boleh null
                gate_time          as gate_time,    -- datetime
                gross_weight       as gross_weight, -- boleh null
                damage_code        as damage_code,  -- default '1' bila null
                feeder_voy         as feeder_voy,   -- boleh null
                vessel_call        as vessel_call,  -- boleh null
                consignee_name     as consignee     -- boleh null
            ")
            ->orderBy('gate_time')
            ->get();

        // === 2) MAPPING KE FORMAT BUILDER ===
        $port  = strtoupper($this->option('port'));         // ex: IDBTM
        $depot = strtoupper($this->option('sender'));       // ex: TBMIDBTM

        $containers = [];
        foreach ($rows as $r) {
            // Normalisasi status ke kode EDIFACT: 4 (Full) / 1 (Empty)
            $status = '4';
            if (isset($r->fe_status)) {
                $s = strtoupper((string)$r->fe_status);
                if (in_array($s, ['E', 'EMPTY', '1', '0'], true)) $status = '1';
                else $status = '4';
            }

            $eventDt = Carbon::parse($r->gate_time, $tz);

            $containers[] = [
                'container_no' => $r->container_no,
                'iso'          => $r->iso,
                'status'       => $status,
                'booking_no'   => $r->booking_no ?: '',
                'event_dt'     => $eventDt,
                'port_code'    => $port,
                'depot_code'   => $depot,
                'gross_weight' => $r->gross_weight ?: null,
                'damage_code'  => $r->damage_code ?: '1',
                'feeder_voy'   => $r->feeder_voy ?: '3',
                'vessel_call'  => $r->vessel_call ?: '',
                'consignee'    => $r->consignee ?: null,
            ];
        }

        // === 3) BANGUN EDIFACT ===
        $edi = $builder->build($header, $containers);

        // === 4) SIMPAN FILE ===
        $gate = $event === 'IN' ? 'GATEIN' : 'GATEOUT';
        $fname = sprintf('%s_%s_%s.txt', $header['sender'], $gate, $now->format('ymdHi'));

        Storage::disk('local')->put("edi/{$fname}", $edi);
        $full = storage_path("app/edi/{$fname}");

        $this->info("Generated: {$full}");
        $this->info("Containers: " . count($containers));
        return Command::SUCCESS;
    }
}
