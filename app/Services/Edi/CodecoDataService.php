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

    private static $annSource = null; // cache meta ANN_*

    public function __construct($connection = 'oracle', $schema = 'C##IDMSTEMP2025', $tz = 'Asia/Jakarta')
    {
        $this->connection = $connection;
        $this->schema     = strtoupper($schema);
        $this->tz         = $tz;
    }

    /**
     * Ambil data kontainer unik untuk HARI INI (atau $dateYmd jika diisi, format YYYY-MM-DD).
     * Bisa difilter per-customer shipping line (CUSTOMER_CODE), jika $customerCode diisi.
     */
    public function fetchToday(string $event, ?string $dateYmd = null, ?string $customerCode = null)
    {
        $base  = $dateYmd ? Carbon::parse($dateYmd . ' 00:00:00', $this->tz) : now($this->tz)->startOfDay();
        $start = $base;
        $end   = (clone $base)->addDay();

        $this->resolveAnnSource();

        return $event === EdiEvent::IN
            ? $this->fetchGateInBetween($start, $end, $customerCode)
            : $this->fetchGateOutBetween($start, $end, $customerCode);
    }

    /** Tentukan sumber ANN_* dan ketersediaan kolomnya. */
    private function resolveAnnSource(): void
    {
        if (self::$annSource !== null) return;

        $owner = $this->schema;
        $table = $this->tableExists('ANN_REPORT') ? 'ANN_REPORT'
            : ($this->tableExists('ANN_IMPORT') ? 'ANN_IMPORT' : null);

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
        $sql = "SELECT 1 FROM ALL_TABLES WHERE OWNER = ? AND TABLE_NAME = ?";
        return !empty(DB::connection($this->connection)->select($sql, [$this->schema, strtoupper($table)]));
    }

    private function columnExists(string $table, string $column): bool
    {
        $sql = "SELECT 1 FROM ALL_TAB_COLUMNS WHERE OWNER = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
        return !empty(DB::connection($this->connection)->select($sql, [$this->schema, strtoupper($table), strtoupper($column)]));
    }

    private function annSubquery(): string
    {
        $s = $this->schema;
        $as = self::$annSource;
        $cols = [
            "NO_CONTAINER",
            $as['has_size_type']     ? "SIZE_TYPE"     : "NULL AS SIZE_TYPE",
            $as['has_consignee']     ? "CONSIGNEE"     : "NULL AS CONSIGNEE",
            $as['has_customer_code'] ? "CUSTOMER_CODE" : "NULL AS CUSTOMER_CODE",
            $as['has_voyage']        ? "VOYAGE"        : "NULL AS VOYAGE",
            $as['has_no_bldo']       ? "NO_BLDO"       : "NULL AS NO_BLDO",
        ];
        return "
            (
              SELECT " . implode(", ", $cols) . ",
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}." . $as['table'] . "
              WHERE NO_CONTAINER IS NOT NULL
            ) ar
        ";
    }

    private function surveySubquery(): string
    {
        $s = $this->schema;
        return "
            (
              SELECT NO_CONTAINER, SIZE_TYPE, PAYLOAD, TARE, MAXGROSS, STATUS_CONTAINER, GRADE_CONTAINER,
                     ROW_NUMBER() OVER (PARTITION BY NO_CONTAINER ORDER BY ROWID DESC) rn
              FROM {$s}.SURVEYIN
              WHERE NO_CONTAINER IS NOT NULL
            ) sv
        ";
    }

    private function fetchGateInBetween(Carbon $start, Carbon $end, ?string $customerCode = null)
    {
        $s = $this->schema;

        $query = DB::connection($this->connection)
            ->table(DB::raw("{$s}.GATE_IN g"))
            ->leftJoin(DB::raw($this->annSubquery()), function ($j) {
                $j->on(DB::raw('ar.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('ar.rn'), '=', 1);
            })
            ->leftJoin(DB::raw($this->surveySubquery()), function ($j) {
                $j->on(DB::raw('sv.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('sv.rn'), '=', 1);
            })
            ->whereRaw("g.GATEIN_TIME >= TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$start->format('Y-m-d H:i:s')])
            ->whereRaw("g.GATEIN_TIME <  TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$end->format('Y-m-d H:i:s')]);

        if ($customerCode) {
            $query->whereRaw("NVL(ar.CUSTOMER_CODE, 'UNKNOWN') = ?", [strtoupper($customerCode)]);
        }

        return $query->selectRaw("
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

    private function fetchGateOutBetween(Carbon $start, Carbon $end, ?string $customerCode = null)
    {
        $s = $this->schema;

        $query = DB::connection($this->connection)
            ->table(DB::raw("{$s}.GATE_OUT g"))
            ->leftJoin(DB::raw($this->annSubquery()), function ($j) {
                $j->on(DB::raw('ar.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('ar.rn'), '=', 1);
            })
            ->leftJoin(DB::raw($this->surveySubquery()), function ($j) {
                $j->on(DB::raw('sv.NO_CONTAINER'), '=', DB::raw('g.NO_CONTAINER'))
                    ->where(DB::raw('sv.rn'), '=', 1);
            })
            ->whereRaw("g.GATEOUT_TIME >= TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$start->format('Y-m-d H:i:s')])
            ->whereRaw("g.GATEOUT_TIME <  TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')", [$end->format('Y-m-d H:i:s')]);

        if ($customerCode) {
            $query->whereRaw("NVL(ar.CUSTOMER_CODE, 'UNKNOWN') = ?", [strtoupper($customerCode)]);
        }

        return $query->selectRaw("
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

    /** Ambil CUSTOMER_CODE pertama yang terisi dari kumpulan rows. */
    public function inferCustomerCode($rows, $fallback = null)
    {
        foreach ($rows as $r) {
            if (!empty($r->customer_code)) return $r->customer_code;
        }
        return $fallback;
    }

    /** Ambil VOYAGE pertama yang terisi dari kumpulan rows. */
    public function inferVoyage($rows, $fallback = null)
    {
        foreach ($rows as $r) {
            if (!empty($r->voyage)) return $r->voyage;
        }
        return $fallback;
    }
}
