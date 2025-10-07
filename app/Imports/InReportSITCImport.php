<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InReportSITCImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'In Report SITC' => new SheetImport(),
        ];
    }
}

class SheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            DB::table('ann_import')->insert([
                'customer_code' => $row['CustomerCode'],
                'no_container' => $row['Container'],
                'ukuran_container' => $row['SizeType'],
                'ex_vessel' => $row['ExVesselName'],
                'consignee' => $row['Consignee'],
                'remarks' => $row['Remarks'],
                'set_time' => now(),
            ]);
        }
    }
}
