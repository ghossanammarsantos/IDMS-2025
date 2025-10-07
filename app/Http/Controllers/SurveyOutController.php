<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SurveyOutController extends Controller
{
    /** List Survey OUT */
    public function index()
    {
        // NOTE: "AI"."OUT_TIME" di-select sebagai out_time (Oracle-friendly)
        $surveyout_list = DB::table('surveyout as so')
            ->leftJoin('ann_import as ai', 'so.no_container', '=', 'ai.no_container')
            ->select(
                'so.*',
                'ai.ex_vessel',
                'ai.customer_code',
                'ai.status_surveyout',
                'ai.status_gateout',
                DB::raw('"AI"."OUT_TIME" AS out_time')
            )
            ->orderByDesc('so.surveyout_time')
            ->get();

        return view('admin.surveyout.index', compact('surveyout_list'));
    }

    /** Form create Survey OUT (tanpa nomor_payment; filter by ann_import flags) */
    public function create()
    {
        // Kandidat:
        // - sudah Survey IN (ada di si)
        // - ann_import.status_survey = 'CLOSE'
        // - ann_import.status_surveyout IS NULL
        // - belum ada di surveyout untuk kombinasi (no_container + no_bldo)
        $datasurveyout = DB::table('surveyin as si')
            ->join('ann_import as ai', 'si.no_container', '=', 'ai.no_container')
            ->leftJoin('gate_in as gi', 'si.no_container', '=', 'gi.no_container')
            ->leftJoin('surveyout as so', function ($join) {
                $join->on('si.no_container', '=', 'so.no_container')
                    ->on('si.no_bldo',     '=', 'so.no_bldo');
            })
            ->where('ai.status_surveyin', 'CLOSE')
            ->whereNull('ai.status_surveyout')
            // ->whereNull('ai.status_gateout') // aktifkan jika perlu
            ->whereNull('so.no_container')
            ->select(
                'si.no_container',
                'si.kode_survey',
                'si.size_type',
                'si.jenis_container',
                'si.no_bldo',
                'si.survey_time',
                'gi.gatein_time',
                'gi.pic_gatein',
                'ai.ex_vessel',
                'ai.customer_code',
                'si.sizze',
                'si.payload',
                'si.tare'
            )
            ->orderByDesc('si.survey_time')
            ->get();

        return view('admin.surveyout.create', compact('datasurveyout'));
    }

    /** Simpan Survey OUT (tanpa payment) */
    public function store(Request $request)
    {
        $request->validate([
            'no_container'      => 'required',
            'status_container'  => 'required|in:AV,DM',
            'grade_container'   => 'nullable|in:A,B,C,D,E',
            'mode_keluar'       => 'required|in:WO,NOTA',
            'bukti_photo.*'     => 'file|mimes:jpg,jpeg,png|max:4096',
        ]);

        $no_container   = $request->input('no_container');
        $status_cont    = $request->input('status_container'); // AV/DM
        $grade_cont     = $request->input('grade_container');  // A..E
        $mode_keluar    = $request->input('mode_keluar');      // WO/NOTA
        $no_truck       = $request->input('no_truck');
        $driver         = $request->input('driver');
        $pic_surveyout  = $request->filled('pic_surveyout')
            ? $request->input('pic_surveyout')
            : (Auth::check() ? Auth::user()->name : 'SYSTEM');

        // (Opsional) field tally tambahan — aktifkan jika tabel surveyout punya kolomnya
        $sender_code        = $request->input('sender_code');
        $movement           = $request->input('movement'); // BOKINGAN/REPO
        $ef                 = $request->input('ef');       // EMPTY/FULL
        $no_booking         = $request->input('no_booking');
        $vessel_code        = $request->input('vessel_code');
        $voyage             = $request->input('voyage');
        $remark             = $request->input('remark');
        $shipper            = $request->input('shipper');
        $seal               = $request->input('seal');
        $sizze              = $request->input('sizze');
        $payload            = $request->input('payload');
        $tare               = $request->input('tare');

        // Ambil referensi dari Survey IN + Gate In + Ann Import
        $data = DB::table('surveyin as si')
            ->leftJoin('gate_in as gi', 'si.no_container', '=', 'gi.no_container')
            ->leftJoin('ann_import as ai', 'si.no_container', '=', 'ai.no_container')
            ->select(
                'si.no_container',
                'si.kode_survey',
                'si.size_type',
                'si.jenis_container',
                'si.survey_time',
                'si.pic as pic_surveyin',
                'si.no_bldo',
                'gi.gatein_time',
                'gi.pic_gatein',
                'ai.ex_vessel',
                'ai.customer_code',
                'ai.status_surveyin',
                'ai.status_surveyout'
            )
            ->where('si.no_container', $no_container)
            ->first();

        if (!$data) {
            return back()->with('error', 'Data Survey IN untuk kontainer ini tidak ditemukan')->withInput();
        }

        // Validasi rule kandidat (harus sama seperti create())
        if (($data->status_surveyin ?? null) !== 'CLOSE' || !is_null($data->status_surveyout)) {
            return back()->with('error', 'Kontainer ini belum memenuhi syarat Survey OUT (cek status di ann_import).')->withInput();
        }

        // Cegah duplikasi kombinasi (no_container + no_bldo)
        $exists = DB::table('surveyout')
            ->where('no_container', $data->no_container)
            ->where('no_bldo', $data->no_bldo)
            ->exists();
        if ($exists) {
            return back()->with('error', 'Survey OUT untuk kombinasi kontainer & BL/DO ini sudah ada.')->withInput();
        }

        // Upload foto (opsional)
        $bukti_photo_paths = [];
        if ($request->hasFile('bukti_photo')) {
            foreach ($request->file('bukti_photo') as $file) {
                if (!$file || !$file->isValid()) continue;
                $name = (string) Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
                $dest = public_path("surveyout_photo/{$data->no_container}");
                if (!is_dir($dest)) @mkdir($dest, 0777, true);
                $file->move($dest, $name);
                $bukti_photo_paths[] = "surveyout_photo/{$data->no_container}/{$name}";
            }
        }

        DB::beginTransaction();
        try {
            $kode_surveyout = $this->generateRunningCode('EIRO', 'surveyout', 'kode_surveyout');

            // Insert SURVEYOUT — gunakan SYSTIMESTAMP (Oracle) untuk surveyout_time
            DB::table('surveyout')->insert([
                'kode_surveyout'  => $kode_surveyout,
                'no_container'    => $data->no_container,
                'jenis_container' => $data->jenis_container,
                'size_type'       => $data->size_type,

                'surveyin_time'   => $data->survey_time,
                'status_wo'       => 'CLOSE',
                'status_container' => $status_cont,
                'grade_container' => $grade_cont,

                'pic_gatein'      => $data->pic_gatein,
                'pic_surveyin'    => $data->pic_surveyin,
                'gatein_time'     => $data->gatein_time,
                'surveyout_time'  => DB::raw('SYSTIMESTAMP'),

                'no_truck'        => $no_truck,
                'driver'          => $driver,
                'no_bldo'         => $data->no_bldo,
                'kode_surveyin'   => $data->kode_survey,
                'bukti_photo'     => json_encode($bukti_photo_paths),

                // Status keluar (WO/NOTA)
                'mode_keluar'     => $mode_keluar,
                'pic_surveyout'   => $pic_surveyout,

                // // jejak untuk cetak
                // 'ex_vessel'       => $data->ex_vessel ?? null,
                // 'customer_code'   => $data->customer_code ?? null,

                // ===== (Opsional) SIMPAN FIELD TALLY KE TABEL SURVEYOUT (aktifkan jika kolom tersedia) =====
                'sender_code'        => $sender_code,
                'movement'           => $movement,
                'ef'                 => $ef,
                'no_booking'         => $no_booking,
                'vessel_code'        => $vessel_code,
                'voyage'             => $voyage,
                'remark'             => $remark,
                'shipper'            => $shipper,
                'seal'               => $seal,
                'sizze'              => $sizze,
                'payload'            => $payload,
                'tare'               => $tare,
            ]);

            // Tutup WO di Survey IN
            DB::table('surveyin')
                ->where('no_container', $data->no_container)
                ->update(['status_wo' => 'CLOSE']);

            // Update ANN_IMPORT: status_surveyout/ gateout + OUT_TIME (Oracle)
            DB::table('ann_import')
                ->where('no_container', $data->no_container)
                ->update([
                    'status_surveyout' => 'CLOSE',
                    'status_gateout'   => 'OPEN',
                    'out_time'         => DB::raw('SYSTIMESTAMP'),
                ]);

            DB::commit();
            return redirect()->route('surveyout.index')->with('success', 'Survey OUT berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal simpan Survey OUT: ' . $e->getMessage())->withInput();
        }
    }

    /** Cetak EIRO (tetap kompatibel) */
    public function cetak_eiro($kode_surveyout)
    {
        $surveyout = DB::table('surveyout')
            ->leftJoin('ann_import', 'surveyout.no_container', '=', 'ann_import.no_container')
            ->select('surveyout.*', 'ann_import.ex_vessel', 'ann_import.customer_code')
            ->where('surveyout.kode_surveyout', $kode_surveyout)
            ->first();

        if (!$surveyout) {
            return redirect()->route('surveyout.index')->with('error', 'Data EIRO tidak ditemukan');
        }

        $barcodeeirin = $this->generateBarcodeHTML($surveyout->kode_surveyout);
        return view('admin.surveyout.cetak_eiro', compact('surveyout', 'barcodeeirin'));
    }

    /** Helper: barcode HTML placeholder */
    private function generateBarcodeHTML($kode_surveyout)
    {
        return '<barcode code="' . e($kode_surveyout) . '" type="C128" />';
    }

    /** Helper: running code EIRO- yymmdd + seq */
    private function generateRunningCode(string $prefix, string $table, string $codeColumn): string
    {
        $latest = DB::table($table)->select($codeColumn)->orderByDesc($codeColumn)->first();
        $today  = date('ymd');

        if (!$latest || empty($latest->{$codeColumn})) {
            return $prefix . '-' . $today . '001';
        }

        $code = $latest->{$codeColumn};   // contoh: EIRO-250930007
        $datePart = substr($code, 5, 6);  // 250930
        $seqPart  = (int) substr($code, -3);

        $nextSeq = ($datePart === $today) ? ($seqPart + 1) : 1;
        return $prefix . '-' . $today . sprintf('%03d', $nextSeq);
    }
}
