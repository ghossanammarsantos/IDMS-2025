<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SurveyInTodayExport implements FromCollection, WithHeadings
{
    protected string $tz = 'Asia/Jakarta';

    /** @var array<string,string> [heading => actualColumn] */
    protected array $selectMap = [];

    public function __construct()
    {
        $this->selectMap = $this->resolveColumns();
    }

    public function headings(): array
    {
        // Header Excel pakai nama “target/heading” yang stabil
        return array_keys($this->selectMap);
    }

    public function collection()
    {
        $today = Carbon::now($this->tz)->toDateString();

        // kolom aktual yang benar-benar ada di DB
        $select = array_values($this->selectMap);

        $rows = DB::table('surveyin')
            ->whereDate('survey_time', $today) // akan jadi TRUNC("SURVEY_TIME") = :p
            ->orderBy('survey_time', 'asc')
            ->select($select)
            ->get();

        return collect($rows)->map(function ($row) {
            $arr = [];
            $record = (array) $row; // kunci bisa UPPERCASE

            foreach ($this->selectMap as $heading => $col) {
                $val = $record[$col] ?? $record[strtoupper($col)] ?? $record[strtolower($col)] ?? null;
                $arr[] = $this->formatIfDateLike($col, $val);
            }
            return $arr;
        });
    }

    protected function formatIfDateLike(string $col, $val)
    {
        if ($val === null) return null;
        // Format kolom waktu/tanggal (konvensi: *_TIME atau *_DATE)
        if (preg_match('/(_TIME|_DATE)$/i', $col)) {
            try {
                return Carbon::parse($val)->timezone($this->tz)->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                return $val;
            }
        }
        return $val;
    }

    /**
     * Deteksi kolom yang ada di SURVEYIN lalu ambil irisan dengan preferensi kita.
     * Sekaligus sediakan sinonim (misal SEAL bisa SEAL_NO, NO_SEAL, dst).
     *
     * @return array<string,string> [heading => actualColumn]
     */
    protected function resolveColumns(): array
    {
        // Ambil daftar kolom eksisting di tabel SURVEYIN (skema user saat ini)
        $existing = collect(DB::select("SELECT COLUMN_NAME FROM USER_TAB_COLUMNS WHERE TABLE_NAME = 'SURVEYIN'"))
            ->pluck('column_name')
            ->map(fn($c) => strtoupper($c))
            ->all();

        // Urutan kolom yg kita inginkan (heading Excel)
        $preferred = [
            'KODE_SURVEYIN',
            'NO_CONTAINER',
            'SIZE_TYPE',
            'STATUS_CONTAINER',
            'GRADE_CONTAINER',
            'PIC_GATEIN',
            'PIC_SURVEYIN',
            'GATEIN_TIME',
            'SURVEY_TIME',
            'NO_TRUCK',
            'DRIVER',
            'NO_BLDO',
            'EX_VESSEL',
            'CUSTOMER_CODE',
            'SENDER_CODE',
            'MOVEMENT',
            'EF',
            'NO_BOOKING',
            'VESSEL_CODE',
            'VOYAGE',
            'REMARK',
            'SHIPPER',
            // 'SEAL',  // ← ini yang bikin ORA-00904 kalau tidak ada
            'SIZZE', // ← sering typo / tidak ada; kita skip jika ga ketemu
            'PAYLOAD',
            'TARE',
        ];

        // Sinonim kolom (heading => list kandidat di DB)
        $synonyms = [
            'SEAL'  => ['SEAL', 'SEAL_NO', 'NO_SEAL', 'SEALNUMBER', 'SEAL_NUMBER'],
            'SIZZE' => ['SIZZE', 'SIZE'], // jika memang ada kolom SIZE
        ];

        $map = [];
        foreach ($preferred as $heading) {
            $candidates = $synonyms[$heading] ?? [$heading];
            foreach ($candidates as $cand) {
                if (in_array(strtoupper($cand), $existing, true)) {
                    $map[$heading] = strtoupper($cand);
                    break;
                }
            }
            // kalau tidak ketemu, kolom tsb otomatis dilewati (tidak diseleksi)
        }

        // Bonus: kalau kebetulan ada kolom SEAL atau SIZE (dari sinonim), ikutkan.
        foreach (['SEAL', 'SIZZE'] as $maybe) {
            if (!isset($map[$maybe]) && isset($synonyms[$maybe])) {
                foreach ($synonyms[$maybe] as $cand) {
                    if (in_array(strtoupper($cand), $existing, true)) {
                        $map[$maybe] = strtoupper($cand);
                        break;
                    }
                }
            }
        }

        return $map;
    }
}
