<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;

class AnnImportUpload implements ToCollection, WithHeadingRow, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    public int $inserted = 0;
    /** @var array<int, array{row:int, container:string, message:string}> */
    public array $failuresBag = [];

    public function chunkSize(): int
    {
        return 500; // sesuaikan kebutuhan
    }

    public function onError(\Throwable $e)
    {
        // Error global (jarang terjadi); dicatat generik
        $this->failuresBag[] = [
            'row' => 0,
            'container' => '-',
            'message' => 'Error: ' . $e->getMessage(),
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failuresBag[] = [
                'row' => $failure->row(),
                'container' => (string)($failure->values()['no_container'] ?? '-'),
                'message' => implode('; ', $failure->errors()),
            ];
        }
    }

    public function collection(Collection $rows)
    {
        $now = now();

        // >>> Tambahkan HELPER di sini <<<
        $asExcelDisplay = function ($value) {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('d/m/Y');
            }
            if (is_numeric($value)) {
                try {
                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                    return $dt ? $dt->format('d/m/Y') : (string)$value;
                } catch (\Throwable $e) {
                    return (string)$value;
                }
            }
            return trim((string)$value);
        };

        $parseDate = function ($value) {
            if (empty($value)) return null;

            if (is_numeric($value)) {
                try {
                    $dt = ExcelDate::excelToDateTimeObject($value);
                    return $dt ? $dt->format('Y-m-d') : null;
                } catch (\Throwable $e) {
                    return null;
                }
            }

            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        };

        foreach ($rows as $index => $row) {
            // Normalisasi key header ke snake_case sesuai template
            $data = [
                'customer_code'    => trim((string)($row['customer_code'] ?? '')),
                'no_container'     => trim((string)($row['no_container'] ?? '')),
                'no_bldo'          => trim((string)($row['no_bldo'] ?? '')),
                'size_type'        => trim((string)($row['size_type'] ?? '')),
                'ex_vessel'        => trim((string)($row['ex_vessel'] ?? '')),
                'tanggal_berthing' => $parseDate($row['tanggal_berthing'] ?? null),
                'consignee'        => trim((string)($row['consignee'] ?? '')),
                'remarks'          => $asExcelDisplay($row['remarks'] ?? ''),
                'voyage'           => trim((string)($row['voyage'] ?? '')),
            ];

            // Validasi minimal per baris
            $validator = Validator::make($data, [
                'no_container' => ['required', 'string', 'max:20'],
            ], [
                'no_container.required' => 'No. Container wajib diisi.',
            ]);

            if ($validator->fails()) {
                $this->failuresBag[] = [
                    'row' => $index + 2, // +2 karena WithHeadingRow menganggap row pertama = header
                    'container' => $data['no_container'] ?: '(kosong)',
                    'message' => implode('; ', $validator->errors()->all()),
                ];
                continue;
            }

            // Skip duplikat
            $exists = DB::table('ann_import')->where('no_container', $data['no_container'])->exists();
            if ($exists) {
                $this->failuresBag[] = [
                    'row' => $index + 2,
                    'container' => $data['no_container'],
                    'message' => 'No. Container sudah ada (duplikat).',
                ];
                continue;
            }

            try {
                DB::table('ann_import')->insert([
                    'customer_code'    => $data['customer_code'] ?: null,
                    'no_container'     => $data['no_container'],
                    'no_bldo'          => $data['no_bldo'] ?: null,
                    'size_type'        => $data['size_type'] ?: null,
                    'ex_vessel'        => $data['ex_vessel'] ?: null,
                    'voyage'           => $data['voyage'] ?: null,
                    'tanggal_berthing' => $data['tanggal_berthing'],
                    'consignee'        => $data['consignee'] ?: null,
                    'remarks'          => $data['remarks'] ?: null,
                    'status_surveyin'    => 'OPEN',
                    'surveyin_time'      => null,
                    'set_time'         => $now,
                ]);

                $this->inserted++;
            } catch (\Throwable $e) {
                $this->failuresBag[] = [
                    'row' => $index + 2,
                    'container' => $data['no_container'],
                    'message' => 'Gagal insert: ' . $e->getMessage(),
                ];
            }
        }
    }
}
