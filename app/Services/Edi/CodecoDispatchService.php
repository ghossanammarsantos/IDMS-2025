<?php

namespace App\Services\Edi;

use App\Support\EdiEvent;
use App\Support\EdiType;
use App\Support\SftpRouter;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CodecoDispatchService
{
    private $data;
    private $builder;
    private $tz = 'Asia/Jakarta';

    public function __construct(CodecoDataService $data, CodecoBuilder $builder)
    {
        $this->data    = $data;
        $this->builder = $builder;
    }

    /** Normalisasi nilai event untuk disimpan ke DB (harus GATEIN/GATEOUT) */
    private function normEventDb(string $event): string
    {
        return strtoupper($event) === EdiEvent::IN ? 'GATEIN' : 'GATEOUT';
    }

    /** Entry point */
    public function run(string $event, Carbon $start, Carbon $end, ?string $onlyCustomer = null): array
    {
        // 1) Enqueue dari Oracle (idempoten)
        $enq = $this->enqueueFromOracleWindow($event, $start, $end, $onlyCustomer);

        // 2) Ambil baris pending/failed per-customer
        $byCustomer = $this->groupPendingByCustomer($event, $start, $end);

        $summary = [
            'window'      => [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')],
            'enqueued'    => $enq,
            'total'       => 0,
            'by_customer' => [],
        ];

        foreach ($byCustomer as $customer => $rows) {
            // $rows = array of stdClass; bungkus ke collection agar util bisa dipakai
            $rowsColl = collect($rows);
            $cust     = $customer ?: 'UNKNOWN';

            // Header EDI
            $now   = now($this->tz);
            $icRef = 'O' . $now->format('ymdHi');
            $msgRf = $icRef . '001';
            $docNo = $now->format('ymdHi') . '403';

            // >>> Gunakan inferVoyage agar tidak kosong di header TDT <<<
            $voyHdr = $this->data->inferVoyage($rowsColl, '');

            // Jangan override profil customer di builder (biarkan null)
            // supaya SIT → BAT/SITID & format khusus terpakai.
            $header = [
                'customer_code'   => strtoupper($cust),
                'sender'          => null,
                'recipient'       => null,
                'carrier'         => null,
                'voyage'          => $voyHdr,
                'created_at'      => $now,
                'interchange_ref' => $icRef,
                'message_ref'     => $msgRf,
                'document_no'     => $docNo,
            ];

            // Body containers — samakan dengan generate (sertakan voyage & consignee)
            $containers = $rowsColl->map(function ($m) {
                return [
                    'container_no'     => strtoupper((string)$m->container_no),
                    'iso'              => strtoupper((string)$m->iso),
                    'booking_no'       => (string)($m->booking_no ?? ''),
                    'event_dt'         => Carbon::parse($m->gate_time, $this->tz),

                    // untuk konsistensi (builder gunakan default jika tidak ada)
                    'port_code'        => 'IDBTM',
                    'depot_code'       => 'TBMIDBTM',

                    'gross_weight'     => $m->gross_weight ?? $m->payload ?? null,
                    'payload'          => $m->payload ?? null,
                    'tare'             => $m->tare ?? null,
                    'maxgross'         => $m->maxgross ?? null,

                    'status_container' => $m->status_container ?? null,
                    'grade_container'  => $m->grade_container ?? null,

                    'voyage'           => $m->voyage ?? null,
                    'consignee'        => $m->consignee ?? null,
                ];
            })->values()->all();

            // Build text (untuk file, tetap kirim IN/OUT)
            $ediText = $this->builder->build($header, $containers, strtoupper($event));

            // ==== Penamaan file lokal ====
            // Khusus SIT: pakai awalan TBMIDBAT_ (bukan TBMIDBTM_)
            $custSlug   = $cust === 'UNKNOWN' ? 'UNKNOWN' : preg_replace('/[^A-Z0-9]+/', '', strtoupper($cust));
            $prefixTerm = ($custSlug === 'SIT') ? 'TBMIDBAT' : 'TBMIDBTM';
            $localName  = $prefixTerm . '_CODECO_' . $custSlug . '_' . strtoupper($event) . '_' . $now->format('ymdHi') . '.txt';
            $outfile = storage_path('app/edi/' . $prefixTerm . '_CODECO_' . $cust . '_' . strtoupper($event) . '_' . $now->format('ymdHi') . '.txt');
            @mkdir(dirname($outfile), 0775, true);
            file_put_contents($outfile, $ediText);

            // Upload via disk yg dipetakan
            $disk     = SftpRouter::forCustomer($custSlug);
            $uploaded = false;
            if ($disk) {
                $uploaded = Storage::disk($disk)->put($localName, $ediText);
            }

            // Update hasil attempt per baris
            $count = 0;
            foreach ($rows as $r) {
                $count++;
                $this->markAttempt(
                    EdiType::CODECO,
                    $custSlug,
                    $event, // akan dinormalisasi ke GATEIN/GATEOUT
                    (string)$r->container_no,
                    $r->gate_time,
                    $uploaded ? 'SENT' : 'FAILED',
                    $uploaded ? null : 'UPLOAD FAILED'
                );
            }

            $summary['by_customer'][$custSlug] = [
                'count'    => $count,
                'file'     => $outfile,
                'uploaded' => (bool)$uploaded,
            ];
            $summary['total'] += $count;

            echo "Generated: {$outfile}\n";
            echo "Customer : {$custSlug}\n";
            echo "Count    : {$count}\n";
            echo "Uploaded : " . ($uploaded ? 'YES' : 'NO') . "\n";
        }

        return $summary;
    }

    /** Ambil data Oracle & upsert ke EDI_DISPATCHES */
    private function enqueueFromOracleWindow(string $event, Carbon $start, Carbon $end, ?string $customer): int
    {
        $rows   = $this->fetchBetween($event, $start, $end, $customer);
        $eventDb = $this->normEventDb($event);

        $n = 0;
        foreach ($rows as $r) {
            // hanya proses data yang sudah punya CUSTOMER_CODE
            $cust = strtoupper((string)($r->customer_code ?? ''));
            if ($cust === '') continue;

            $this->upsertDispatch([
                'jenis_edi'        => EdiType::CODECO,
                'customer_code'    => $cust,
                'event_type'       => $eventDb,
                'container_no'     => (string)$r->container_no,
                'gate_time'        => $r->gate_time,
                'iso'              => (string)$r->iso,
                'booking_no'       => (string)$r->booking_no,
                'voyage'           => (string)$r->voyage,
                'status_container' => (string)$r->status_container,
                'grade_container'  => (string)$r->grade_container,
                'gross_weight'     => $r->gross_weight ?? $r->payload ?? null,
                'consignee'        => (string)$r->consignee,
            ]);
            $n++;
        }
        return $n;
    }

    /** Ambil data Oracle (dipakai enqueue) */
    private function fetchBetween(string $event, Carbon $start, Carbon $end, ?string $customer): Collection
    {
        if (strtoupper($event) === EdiEvent::IN) {
            return $this->data->fetchInBetween($start, $end, $customer);
        }
        return $this->data->fetchOutBetween($start, $end, $customer);
    }

    /** Upsert idempoten (composite unique key) */
    private function upsertDispatch(array $m): void
    {
        $sql = "
            MERGE INTO EDI_DISPATCHES d
            USING (
              SELECT
                :p_edi        AS JENIS_EDI,
                :p_cust       AS CUSTOMER_CODE,
                :p_evt        AS EVENT_TYPE,
                :p_cn         AS CONTAINER_NO,
                TO_TIMESTAMP(:p_gt, 'YYYY-MM-DD HH24:MI:SS') AS GATE_TIME
              FROM DUAL
            ) s
            ON (
              d.JENIS_EDI = s.JENIS_EDI AND
              d.CUSTOMER_CODE = s.CUSTOMER_CODE AND
              d.EVENT_TYPE = s.EVENT_TYPE AND
              d.CONTAINER_NO = s.CONTAINER_NO AND
              d.GATE_TIME = s.GATE_TIME
            )
            WHEN MATCHED THEN
              UPDATE SET
                ISO = :p_iso,
                BOOKING_NO = :p_bkn,
                VOYAGE = :p_voy,
                STATUS_CONTAINER = :p_stc,
                GRADE_CONTAINER  = :p_grd,
                GROSS_WEIGHT     = :p_grw,
                CONSIGNEE        = :p_con,
                STATUS           = 'PENDING',
                UPDATED_AT       = SYSTIMESTAMP
            WHEN NOT MATCHED THEN
              INSERT (
                ID, JENIS_EDI, CUSTOMER_CODE, EVENT_TYPE,
                CONTAINER_NO, GATE_TIME, ISO, BOOKING_NO, VOYAGE,
                STATUS_CONTAINER, GRADE_CONTAINER, GROSS_WEIGHT, CONSIGNEE,
                STATUS, ATTEMPT_COUNT, CREATED_AT, UPDATED_AT
              ) VALUES (
                SEQ_EDI_DISPATCHES.NEXTVAL, :p_edi, :p_cust, :p_evt,
                :p_cn, TO_TIMESTAMP(:p_gt, 'YYYY-MM-DD HH24:MI:SS'), :p_iso, :p_bkn, :p_voy,
                :p_stc, :p_grd, :p_grw, :p_con,
                'PENDING', 0, SYSTIMESTAMP, SYSTIMESTAMP
              )
        ";

        $gt = is_string($m['gate_time'])
            ? $m['gate_time']
            : Carbon::parse($m['gate_time'])->format('Y-m-d H:i:s');

        DB::insert($sql, [
            'p_edi' => $m['jenis_edi'],
            'p_cust' => $m['customer_code'],
            'p_evt' => $m['event_type'],
            'p_cn'  => $m['container_no'],
            'p_gt'  => $gt,
            'p_iso' => $m['iso'],
            'p_bkn' => $m['booking_no'],
            'p_voy' => $m['voyage'],
            'p_stc' => $m['status_container'],
            'p_grd' => $m['grade_container'],
            'p_grw' => $m['gross_weight'],
            'p_con' => $m['consignee'],
        ]);
    }

    /** Ambil baris PENDING/FAILED pada window; kelompokkan per customer */
    private function groupPendingByCustomer(string $event, Carbon $start, Carbon $end): array
    {
        $evtDb = $this->normEventDb($event);

        $sql = "
            SELECT
                NVL(CUSTOMER_CODE, 'UNKNOWN') AS customer_code,
                CONTAINER_NO   AS container_no,
                TO_CHAR(GATE_TIME, 'YYYY-MM-DD HH24:MI:SS') AS gate_time,
                ISO, BOOKING_NO, VOYAGE,
                STATUS_CONTAINER, GRADE_CONTAINER, GROSS_WEIGHT,
                CONSIGNEE, STATUS, ATTEMPT_COUNT
            FROM EDI_DISPATCHES
            WHERE JENIS_EDI = :p_edi
              AND EVENT_TYPE = :p_evt
              AND GATE_TIME >= TO_TIMESTAMP(:p_start, 'YYYY-MM-DD HH24:MI:SS')
              AND GATE_TIME <  TO_TIMESTAMP(:p_end,   'YYYY-MM-DD HH24:MI:SS')
              AND STATUS IN ('PENDING','FAILED')
            ORDER BY GATE_TIME ASC
        ";

        $params = [
            'p_edi'   => EdiType::CODECO,
            'p_evt'   => $evtDb,
            'p_start' => $start->format('Y-m-d H:i:s'),
            'p_end'   => $end->format('Y-m-d H:i:s'),
        ];

        $rows = collect(DB::select($sql, $params));

        return $rows->groupBy(function ($r) {
            return strtoupper($r->customer_code);
        })->all();
    }

    /** Tandai hasil attempt */
    private function markAttempt($edi, $cust, $evt, $cn, $gateTime, $status, $err = null): void
    {
        $evtDb = $this->normEventDb($evt);

        $sql = "
            UPDATE EDI_DISPATCHES
               SET STATUS         = :p_status,
                   ATTEMPT_COUNT  = NVL(ATTEMPT_COUNT,0) + 1,
                   LAST_ATTEMPT_AT= SYSTIMESTAMP,
                   LAST_ERROR     = :p_err,
                   UPDATED_AT     = SYSTIMESTAMP,
                   SENT_AT        = CASE WHEN :p_status = 'SENT' THEN SYSTIMESTAMP ELSE SENT_AT END
             WHERE JENIS_EDI     = :p_edi
               AND CUSTOMER_CODE  = :p_cust
               AND EVENT_TYPE     = :p_evt
               AND CONTAINER_NO   = :p_cn
               AND GATE_TIME      = TO_TIMESTAMP(:p_gt, 'YYYY-MM-DD HH24:MI:SS')
        ";

        $gt = is_string($gateTime)
            ? $gateTime
            : Carbon::parse($gateTime)->format('Y-m-d H:i:s');

        DB::update($sql, [
            'p_status' => $status,
            'p_err'    => $err,
            'p_edi'    => $edi,
            'p_cust'   => $cust,
            'p_evt'    => $evtDb,
            'p_cn'     => $cn,
            'p_gt'     => $gt,
        ]);
    }
}
