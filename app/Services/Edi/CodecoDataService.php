<?php

namespace App\Services\Edi;

use App\Support\EdiEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CodecoDataService
{
    private $connection;
    private $schema;
    private $tz;

    // cache simple untuk deteksi sumber ANN_*
    private static $annSource = null; // ['table' => 'ANN_REPORT'|'ANN_IMPORT', 'has_customer_code' => bool, dst...]

    public function __construct($connection = 'oracle', $schema = 'C##IDMSTEMP2025', $tz = 'Asia/Jakarta')
    {
        $this->connection = $connection;
        $this->schema     = strtoupper($schema);
        $this->tz         = $tz;
    }

    /** Ambil data kontainer unik untuk HARI INI (atau $dateYmd jika diisi, format YYYY-MM-DD). */
    public function fetchToday($event, $dateYmd = null)
    {
        $base  = $dateYmd ? Carbon::parse($dateYmd . ' 00:00:00', $this->tz) : now($this->tz)->startOfDay();
        $start = $base;                   // inklusif
        $end   = (clone $base)->addDay(); // eksklusif

        $this->resolveAnnSource(); // pastikan terdeteksi sekali

        return $event === EdiEvent::IN
            ? $this->fetchGateInBetween($start, $end)
            : $this->fetchGateOutBetween($start, $end);
    }

    /** Tentukan sumber ANN_* yang tersedia + kolom-kolomnya. */
    private function resolveAnnSource(): void
    {
        if (self::$annSource !== null) return;

        $owner = $this->schema;

        // pilih ANN_REPORT jika ada; jika tidak, ANN_IMPORT
        $table = $this->tableExists('ANN_REPORT') ? 'ANN_REPORT' : ($this->tableExists('ANN_IMPORT') ? 'ANN_IMPORT' : null);

        if ($table === null) {
            throw new \RuntimeException("Neither {$owner}.ANN_REPORT nor {$owner}.ANN_IMPORT exists / accessible.");
        }

        self::$annSource = [
            'table'             => $table,
            'has_customer_code' => $this->columnExists($table, 'CUSTOMER_CODE'),
            'has_size_type'     => $this->columnExists($table, 'SIZE_TYPE'),
            'has_no_bldo'       => $this->columnExists($table, 'NO_BLDO'),
            'has_consignee'     => $this->columnExists($table, 'CONSIGNEE'),
            'has_voyage'        => $this->columnExists($table, 'VOYAGE'),
        ];
    }

    private function tableExists(string $table): bool
    {
        $owner = $this->schema;
        $table = strtoupper($table);
        $sql   = "SELECT 1 FROM ALL_TABLES WHERE OWNER = ? AND TABLE_NAME = ?";
        $rows  = DB::connection($this->connection)->select($sql, [$owner, $table]);
        return !empty($rows);
    }

    private function columnExists(string $table, string $column): bool
    {
        $owner  = $this->schema;
        $table  = strtoupper($table);
        $column = strtoupper($column);
        $sql    = "SELECT 1 FROM ALL_TAB_COLUMNS WHERE OWNER = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
        $rows   = DB::connection($this->connection)->select($sql, [$owner, $table, $column]);
        return !empty($rows);
    }

    private function fetchGateInBetween(Carbon $start, Carbon $end)
    {
        $s  = $this->schema;
        $as = self::$annSource;

        $cols = [
            "NO_CONTAINER",
            $as['has_size_type']     ? "SIZE_TYPE"     : "NULL AS SIZE_TYPE",
            $as['has_consignee']     ? "CONSIGNEE"     : "NULL AS CONSIGNEE",
            $as['has_customer_code'] ? "CUSTOMER_CODE" : "NULL AS CUSTOMER_CODE",
            $as['has_voyage']        ? "VOYAGE"        : "NULL AS VOYAGE",
            $as['has_no_bldo']       ? "NO_BLDO"       : "NULL AS NO_BLDO",
        ];
        $subAnn = DB::raw("
            (
              SELECT " . implode(", ", $cols) . ",
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}." . $as['table'] . "
              WHERE NO_CONTAINER IS NOT NULL
            ) ar
        ");

        $subSurvey = DB::raw("
            (
              SELECT NO_CONTAINER, SIZE_TYPE, PAYLOAD, TARE, MAXGROSS, STATUS_CONTAINER, GRADE_CONTAINER,
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}.SURVEYIN
              WHERE NO_CONTAINER IS NOT NULL
            ) sv
        ");

        return DB::connection($this->connection)
            ->table(DB::raw("{$s}.GATE_IN g"))
            ->leftJoin($subAnn, function ($j) {
                $j->on(DB::raw('ar.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('ar.rn'), '=', 1);
            })
            ->leftJoin($subSurvey, function ($j) {
                $j->on(DB::raw('sv.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('sv.rn'), '=', 1);
            })
            ->whereRaw("g.GATEIN_TIME >= TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$start->format('Y-m-d H:i:s')])
            ->whereRaw("g.GATEIN_TIME <  TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$end->format('Y-m-d H:i:s')])
            ->selectRaw("
                ar.CUSTOMER_CODE          AS customer_code,
                ar.VOYAGE                 AS voyage,

                COALESCE(ar.NO_CONTAINER, g.NO_CONTAINER) AS container_no,
                COALESCE(ar.SIZE_TYPE, sv.SIZE_TYPE)      AS iso,
                COALESCE(ar.NO_BLDO, g.NO_BLDO)           AS booking_no,

                g.GATEIN_TIME             AS gate_time,
                sv.PAYLOAD                AS payload,
                sv.TARE                   AS tare,
                sv.MAXGROSS               AS maxgross,
                sv.STATUS_CONTAINER       AS status_container,
                sv.GRADE_CONTAINER        AS grade_container,
                ar.CONSIGNEE              AS consignee
            ")
            ->orderBy(DB::raw("g.GATEIN_TIME"))
            ->get();
    }

    private function fetchGateOutBetween(Carbon $start, Carbon $end)
    {
        $s  = $this->schema;
        $as = self::$annSource;

        $cols = [
            "NO_CONTAINER",
            $as['has_size_type']     ? "SIZE_TYPE"     : "NULL AS SIZE_TYPE",
            $as['has_consignee']     ? "CONSIGNEE"     : "NULL AS CONSIGNEE",
            $as['has_customer_code'] ? "CUSTOMER_CODE" : "NULL AS CUSTOMER_CODE",
            $as['has_voyage']        ? "VOYAGE"        : "NULL AS VOYAGE",
            $as['has_no_bldo']       ? "NO_BLDO"       : "NULL AS NO_BLDO",
        ];
        $subAnn = DB::raw("
            (
              SELECT " . implode(", ", $cols) . ",
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}." . $as['table'] . "
              WHERE NO_CONTAINER IS NOT NULL
            ) ar
        ");

        $subSurvey = DB::raw("
            (
              SELECT NO_CONTAINER, SIZE_TYPE, PAYLOAD, TARE, MAXGROSS, STATUS_CONTAINER, GRADE_CONTAINER,
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}.SURVEYIN
              WHERE NO_CONTAINER IS NOT NULL
            ) sv
        ");

        return DB::connection($this->connection)
            ->table(DB::raw("{$s}.GATE_OUT g"))
            ->leftJoin($subAnn, function ($j) {
                $j->on(DB::raw('ar.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('ar.rn'), '=', 1);
            })
            ->leftJoin($subSurvey, function ($j) {
                $j->on(DB::raw('sv.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('sv.rn'), '=', 1);
            })
            ->whereRaw("g.GATEOUT_TIME >= TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$start->format('Y-m-d H:i:s')])
            ->whereRaw("g.GATEOUT_TIME <  TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$end->format('Y-m-d H:i:s')])
            ->selectRaw("
                ar.CUSTOMER_CODE          AS customer_code,
                ar.VOYAGE                 AS voyage,

                COALESCE(ar.NO_CONTAINER, g.NO_CONTAINER) AS container_no,
                COALESCE(ar.SIZE_TYPE, sv.SIZE_TYPE)      AS iso,
                COALESCE(ar.NO_BLDO, g.NO_BLDO)           AS booking_no,

                g.GATEOUT_TIME            AS gate_time,
                sv.PAYLOAD                AS payload,
                sv.TARE                   AS tare,
                sv.MAXGROSS               AS maxgross,
                sv.STATUS_CONTAINER       AS status_container,
                sv.GRADE_CONTAINER        AS grade_container,
                ar.CONSIGNEE              AS consignee
            ")
            ->orderBy(DB::raw("g.GATEOUT_TIME"))
            ->get();
    }

    public function inferCustomerCode($rows, $fallback = null)
    {
        foreach ($rows as $r) {
            if (!empty($r->customer_code)) return $r->customer_code;
        }
        return $fallback;
    }

    public function inferVoyage($rows, $fallback = null)
    {
        foreach ($rows as $r) {
            if (!empty($r->voyage)) return $r->voyage;
        }
        return $fallback;
    }
}
