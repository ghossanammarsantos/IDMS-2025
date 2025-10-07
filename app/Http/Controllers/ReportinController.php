<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyInPerDayExport;

class ReportinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // =========================
        // 1) Report Stock In (size_type, total, teus)
        // =========================
        $reportin = DB::table('gate_in')
            ->select('gate_in.size_type', DB::raw('COUNT(gate_in.no_container) as total'))
            ->leftJoin('gate_out', 'gate_in.no_container', '=', 'gate_out.no_container')
            ->leftJoin('surveyout', 'gate_in.no_bldo', '=', 'surveyout.no_bldo')
            ->whereNull('gate_out.no_container')
            ->whereNull('surveyout.no_bldo')
            ->groupBy('gate_in.size_type')
            ->get();

        // Hitung TEUs sederhana (20GP = 1 TEU, 40HC = 2 TEUs; lainnya 0 / sesuaikan jika perlu)
        $reportin->each(function ($item) {
            $item->teus = ($item->size_type === '20GP')
                ? $item->total * 1
                : (($item->size_type === '40HC') ? $item->total * 2 : 0);
        });

        // =========================
        // 2) Report Stock by Customer (consignee, 20GP, 40HC, TEUs)
        // =========================
        $reportsCustomer = DB::table('surveyin')
            ->join('ann_import', 'ann_import.no_container', '=', 'surveyin.no_container')
            ->leftJoin('gate_out', 'surveyin.no_container', '=', 'gate_out.no_container')
            ->leftJoin('surveyout', 'surveyin.no_bldo', '=', 'surveyout.no_bldo')
            ->whereNull('gate_out.no_container')
            ->whereNull('surveyout.no_bldo')
            ->select(
                'ann_import.consignee',
                'surveyin.size_type',
                DB::raw('COUNT(DISTINCT surveyin.no_container) as jumlah_container')
            )
            ->groupBy('ann_import.consignee', 'surveyin.size_type')
            ->get();

        $reportsCustomerTeus = $reportsCustomer->groupBy('consignee')->map(function ($items, $consignee) {
            $result = [
                'consignee' => $consignee,
                'jumlah_20gp' => 0,
                'jumlah_40hc' => 0,
                'teus' => 0,
            ];
            foreach ($items as $item) {
                if ($item->size_type === '20GP') {
                    $result['jumlah_20gp'] += $item->jumlah_container;
                    $result['teus'] += $item->jumlah_container * 1;
                } elseif ($item->size_type === '40HC') {
                    $result['jumlah_40hc'] += $item->jumlah_container;
                    $result['teus'] += $item->jumlah_container * 2;
                }
            }
            return (object) $result;
        })->values();

        // =========================
        // 3) Container Masuk per Hari (Detail Survey In)
        // =========================
        // Daftar kolom eksplisit dari surveyin (sesuai skema yang kamu kirim)
        $surveyinColumns = [
            'NO_CONTAINER',
            'JENIS_CONTAINER',
            'SIZE_TYPE',
            'SURVEY_TIME',
            'STATUS_WO',
            'KEGIATAN1',
            'PIC',
            'STATUS_CONTAINER',
            'GRADE_CONTAINER',
            'PIC_GATEIN',
            'GATEIN_TIME',
            'STATUS_GATEIN',
            'STATUS_GATEOUT',
            'GATEOUT_TIME',
            'NO_TRUCK',
            'DRIVER',
            'NO_BLDO',
            'KEGIATAN2',
            'KEGIATAN',
            'KODE_SURVEY',
            'BUKTI_PHOTO',
            'BLOCK',
            'SLOT',
            'ROW2',
            'TIER',
            'PAYLOAD',
            'TARE',
            'MAXGROSS',
            'SIZZE',
            'FOTO_SURAT_JALAN',
        ];

        // Build select dengan alias lowercase agar konsisten dipakai di Blade
        $selectColumns = array_map(function ($c) {
            return DB::raw("surveyin.$c as " . Str::lower($c));
        }, $surveyinColumns);

        // Ambil data surveyin yang belum gate out & belum surveyout
        $surveyInRaw = DB::table('surveyin')
            ->leftJoin('gate_out', 'surveyin.no_container', '=', 'gate_out.no_container')
            ->leftJoin('surveyout', 'surveyin.no_bldo', '=', 'surveyout.no_bldo')
            ->whereNull('gate_out.no_container')
            ->whereNull('surveyout.no_bldo')
            ->select($selectColumns)
            // Urutan: coba berdasarkan SURVEY_TIME lalu GATEIN_TIME (jika string, urutan lexicographic)
            ->orderBy('surveyin.SURVEY_TIME', 'desc')
            ->orderBy('surveyin.GATEIN_TIME', 'desc')
            ->get();

        // Group by tanggal (pakai SURVEY_TIME, fallback ke GATEIN_TIME)
        $surveyInPerDay = $surveyInRaw->groupBy(function ($row) {
            $ts = $row->survey_time ?? $row->gatein_time ?? null;

            if (!$ts) {
                return 'Tanpa Tanggal';
            }

            try {
                // Carbon::parse akan mencoba string/timestamp
                return Carbon::parse($ts)->format('Y-m-d');
            } catch (\Throwable $e) {
                // Jika bukan format tanggal valid
                return 'Tanpa Tanggal';
            }
        });

        // Simpan daftar kolom (lowercase) untuk header tabel di Blade
        $surveyinColumnsLower = array_map(fn($c) => Str::lower($c), $surveyinColumns);

        // =========================
        // Return view
        // =========================
        return view('admin.reportin.index', compact(
            'reportin',
            'reportsCustomer',
            'reportsCustomerTeus',
            'surveyInPerDay',
            'surveyinColumnsLower',
        ));
    }

    protected function buildSurveyInBaseQuery(): array
    {
        $surveyinColumns = [
            'NO_CONTAINER',
            'JENIS_CONTAINER',
            'SIZE_TYPE',
            'SURVEY_TIME',
            'STATUS_WO',
            'KEGIATAN1',
            'PIC',
            'STATUS_CONTAINER',
            'GRADE_CONTAINER',
            'PIC_GATEIN',
            'GATEIN_TIME',
            'STATUS_GATEIN',
            'STATUS_GATEOUT',
            'GATEOUT_TIME',
            'NO_TRUCK',
            'DRIVER',
            'NO_BLDO',
            'KEGIATAN2',
            'KEGIATAN',
            'KODE_SURVEY',
            'BUKTI_PHOTO',
            'BLOCK',
            'SLOT',
            'ROW2',
            'TIER',
            'PAYLOAD',
            'TARE',
            'MAXGROSS',
            'SIZZE',
            'FOTO_SURAT_JALAN',
        ];

        $selectColumns = array_map(function ($c) {
            return DB::raw("surveyin.$c as " . Str::lower($c));
        }, $surveyinColumns);

        $builder = DB::table('surveyin')
            ->leftJoin('gate_out', 'surveyin.no_container', '=', 'gate_out.no_container')
            ->leftJoin('surveyout', 'surveyin.no_bldo', '=', 'surveyout.no_bldo')
            ->whereNull('gate_out.no_container')
            ->whereNull('surveyout.no_bldo')
            ->select($selectColumns)
            ->orderBy('surveyin.SURVEY_TIME', 'desc')
            ->orderBy('surveyin.GATEIN_TIME', 'desc');

        $columnsLower = array_map(fn($c) => Str::lower($c), $surveyinColumns);

        return [$builder, $columnsLower];
    }

    /**
     * Export Excel untuk Container Masuk per Hari (Detail Survey In)
     * Parameter:
     *   - date (optional, format Y-m-d). Jika tidak dikirim, export seluruh data (semua tanggal).
     *   - all=1 (optional) untuk paksa export semua data.
     */
    public function exportSurveyInPerDay(Request $request)
    {
        [$builder, $columnsLower] = $this->buildSurveyInBaseQuery();

        $date = $request->query('date'); // ex: 2025-10-05
        $exportModeAll = $request->boolean('all', false);

        // Ambil semua baris (kita filter per tanggal secara aman dengan Carbon, mengingat kolom bisa string)
        $rows = $builder->get();

        // Filter per tanggal jika date dikirim dan bukan export semua
        $sheetTitle = 'Survey In - Semua Tanggal';
        $fileSuffix = 'all';

        if (!$exportModeAll && !empty($date)) {
            $rows = $rows->filter(function ($row) use ($date) {
                $ts = $row->survey_time ?? $row->gatein_time ?? null;
                if (!$ts) return false;
                try {
                    return Carbon::parse($ts)->format('Y-m-d') === $date;
                } catch (\Throwable $e) {
                    return false;
                }
            })->values();

            $sheetTitle = 'Survey In - ' . Carbon::parse($date)->format('d M Y');
            $fileSuffix = $date;
        }

        // Jika tidak ada data, tetap kirim file kosong dengan heading
        $export = new SurveyInPerDayExport($rows, $columnsLower, $sheetTitle);

        $filename = 'container_masuk_per_hari_' . $fileSuffix . '.xlsx';
        return Excel::download($export, $filename);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
