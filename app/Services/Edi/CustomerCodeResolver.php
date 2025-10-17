<?php

namespace App\Services\Edi;

use Illuminate\Support\Facades\DB;

class CustomerCodeResolver
{
    /**
     * Ambil customer_code terakhir dari EDI_DISPATCHES untuk container tertentu.
     * Mengembalikan string UPPERCASE atau null jika tidak ada.
     */
    public function lastCustomerFromDispatch(string $containerNo): ?string
    {
        $sql = "
            SELECT CUSTOMER_CODE
            FROM EDI_DISPATCHES
            WHERE CONTAINER_NO = :cn
              AND CUSTOMER_CODE IS NOT NULL
              AND CUSTOMER_CODE <> 'UNKNOWN'
            ORDER BY COALESCE(SENT_AT, UPDATED_AT, CREATED_AT) DESC
            FETCH NEXT 1 ROWS ONLY
        ";

        $row = DB::selectOne($sql, ['cn' => strtoupper($containerNo)]);
        return $row ? strtoupper((string) $row->customer_code) : null;
    }

    /**
     * Batch lookup: untuk sekumpulan container_no kembalikan map [container_no => customer_code].
     * Menghindari N+1 query saat post-process hasil fetch IN/OUT.
     */
    public function lastCustomerBatch(array $containerNos): array
    {
        if (empty($containerNos)) return [];

        // Hilangkan duplikat & uppercase semua
        $containerNos = array_values(array_unique(array_map('strtoupper', $containerNos)));

        // Oracle: gunakan IN list + ROW_NUMBER agar 1 baris per container
        $placeholders = implode(',', array_fill(0, count($containerNos), '?'));
        $sql = "
            SELECT CONTAINER_NO, CUSTOMER_CODE
            FROM (
              SELECT
                CONTAINER_NO,
                UPPER(CUSTOMER_CODE) AS CUSTOMER_CODE,
                ROW_NUMBER() OVER (
                  PARTITION BY CONTAINER_NO
                  ORDER BY COALESCE(SENT_AT, UPDATED_AT, CREATED_AT) DESC
                ) AS rn
              FROM EDI_DISPATCHES
              WHERE CONTAINER_NO IN ($placeholders)
                AND CUSTOMER_CODE IS NOT NULL
                AND CUSTOMER_CODE <> 'UNKNOWN'
            )
            WHERE rn = 1
        ";

        $rows = DB::select($sql, $containerNos);

        $map = [];
        foreach ($rows as $r) {
            $map[strtoupper($r->container_no)] = strtoupper($r->customer_code);
        }
        return $map;
    }
}
