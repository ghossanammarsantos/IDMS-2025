<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SurveyInController extends Controller
{
    /** List Survey IN */
    public function index()
    {
        $survey_list = DB::table('surveyin')->orderByDesc('survey_time')->get();
        $tarif_list  = DB::table('tarif_depo')->get();
        $gate_in     = DB::table('gate_in')->get();

        return view('admin.surveyin.index', compact('survey_list', 'tarif_list', 'gate_in'));
    }


    /** Form create Survey IN */
    public function create()
    {
        // Jasa
        $tarif_list          = DB::table('tarif_depo')->get();
        $tarif_lolo_list     = DB::table('tarif_depo')->where('grup', 'LOLO')->get();
        $tarif_wash_list     = DB::table('tarif_depo')->where('grup', 'WASH')->get();
        $tarif_sweeping_list = DB::table('tarif_depo')->where('grup', 'SWEEPING')->get(); // dipakai di Blade

        // CEDEX
        $component = DB::table('cedex')->where('grup', 'Component')->get();
        $damage    = DB::table('cedex')->where('grup', 'Damage')->get();
        $repair    = DB::table('cedex')->where('grup', 'Repair')->get();

        // Kontainer yang boleh disurvey (OPEN+IN) dan belum ada di surveyin
        $selected = DB::table('surveyin')->pluck('no_container')->toArray();
        $gate_in  = DB::table('ann_import')
            ->where('status_surveyin', 'OPEN')
            ->where('status_gatein', 'IN')
            ->whereNotIn('no_container', $selected)
            ->get();

        // Yard (untuk dropdown)
        $blocks  = DB::table('yard')->select('block')->distinct()->get();
        $maxSlot = DB::table('yard')->max('slot');
        $maxRow  = DB::table('yard')->max('row2');

        // Occupancy (opsional, untuk JS filter)
        $occupiedSlots = DB::table('surveyin')
            ->select('block', 'slot')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot);

        $occupiedRows = DB::table('surveyin')
            ->select('block', 'slot', 'row2')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot . '_' . $i->row2);

        $occupiedTiers = DB::table('surveyin')
            ->select('block', 'slot', 'row2', 'tier')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot . '_' . $i->row2 . '_' . $i->tier);

        return view('admin.surveyin.create', compact(
            'tarif_list',
            'tarif_lolo_list',
            'tarif_wash_list',
            'tarif_sweeping_list',
            'gate_in',
            'component',
            'damage',
            'repair',
            'blocks',
            'maxSlot',
            'maxRow',
            'occupiedTiers',
            'occupiedSlots',
            'occupiedRows'
        ));
    }

    /** Legacy store (tidak dipakai form ini, dibiarkan untuk kompatibilitas lama) */
    public function store(Request $request)
    {
        $no_container = $request->input('no_container');
        $status_survey = $request->input('status_survey');

        $ok = DB::table('survey')->insert([
            'no_container'    => $no_container,
            'consignee'       => $request->input('consignee'),
            'ukuran_container' => $request->input('ukuran_container'),
            'no_bldo'         => $request->input('no_bldo'),
            'no_truck'        => $request->input('no_truck'),
            'driver'          => $request->input('driver'),
            'status_survey'   => $status_survey,
            'set_time'        => DB::raw('SYSTIMESTAMP'),
            'created_at'      => DB::raw('SYSTIMESTAMP'),
            'updated_at'      => DB::raw('SYSTIMESTAMP'),
        ]);

        if ($ok) {
            DB::table('ann_import')->where('no_container', $no_container)
                ->update(['status_survey' => 'CLOSE']);
            return redirect()->route('annimport.index')->with('success', 'Data Survey berhasil ditambahkan dan status kontainer diperbarui');
        }
        return back()->with('error', 'Gagal menambahkan data Survey');
    }

    /** Simpan Survey IN (route: surveyin.store_detail) */
    public function store_detail(Request $request)
    {
        // Validasi minimal
        // $request->validate([
        //     'no_container'      => 'required',
        //     'status_container'  => 'required|in:AV,DM',
        //     'grade_container'   => 'nullable|in:A,B,C,D,E',
        //     'bukti_photo.*'     => 'array',
        //     'bukti_photo.*.*'   => 'file|mimes:jpg,jpeg,png|max:4096',
        //     'foto_surat_jalan'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        // ]);

        // Buat kode EIR & EOR berbasis kode terakhir (tanpa query tanggal) → aman Oracle
        $kode_survey = $this->generateRunningCode('EIRI', 'surveyin', 'kode_survey');
        $eor_code    = $this->generateRunningCode('EOR',  'eor',      'eor_code');

        $no_container = $request->input('no_container');

        // Ambil data dari gate_in + ann_import (no_bldo, dll)
        $data_container = DB::table('gate_in')
            ->join('ann_import', 'gate_in.no_container', '=', 'ann_import.no_container')
            ->select(
                'gate_in.no_container',
                'gate_in.jenis_container',
                'gate_in.size_type',
                'ann_import.no_bldo',
                'gate_in.gatein_time',
                'gate_in.pic_gatein'
            )
            ->where('gate_in.no_container', $no_container)
            ->first();

        if (!$data_container) {
            // Beri hint apakah ada di salah satu tabel saja
            $existAnn    = DB::table('ann_import')->where('no_container', $no_container)->exists();
            $existGateIn = DB::table('gate_in')->where('no_container', $no_container)->exists();
            $hint = $existAnn && !$existGateIn
                ? 'No. ini ada di ann_import namun belum Gate IN. Buat Gate IN dulu.'
                : 'Pastikan data ada di gate_in dan ann_import.';
            return back()->with('error', 'Data container tidak ditemukan. ' . $hint)->withInput();
        }

        // Data umum
        $status_container = $request->input('status_container'); // AV / DM
        $grade_container  = $request->input('grade_container');  // A/B/C/D/E
        $kegiatan = array_merge(
            $request->input('kegiatan1') ?? [],
            $request->input('kegiatan2') ?? [],
            $request->input('kegiatan3') ?? []
        );
        $kegiatanString = implode(', ', $kegiatan);
        $pic = Auth::check() ? Auth::user()->name : 'SYSTEM';

        // Lokasi / teknis dari Blade
        $block        = $request->input('block');
        $slot         = $request->input('slot');
        $row          = $request->input('row2');
        $tier         = $request->input('tier');
        $namaTrucking = $request->input('nama_trucking');
        $no_truck     = $request->input('no_truck');
        $driver       = $request->input('driver');
        $sizze        = $request->input('sizze');
        $tare         = $request->input('tare');
        $payload      = $request->input('payload');
        $max_gross    = $request->input('max_gross');
        $tanggal_in_depo = $request->input('tanggal_in_depo');

        // ubah ke format Oracle timestamp
        if ($tanggal_in_depo) {
            try {
                // ubah "2025-10-07T13:37" → "2025-10-07 13:37:00"
                $formatted = Carbon::parse($tanggal_in_depo)->format('Y-m-d H:i:s');

                // buat ekspresi SQL aman untuk Oracle
                $tanggal_in_depo_sql = DB::raw("TO_TIMESTAMP('{$formatted}', 'YYYY-MM-DD HH24:MI:SS')");
            } catch (\Exception $e) {
                $tanggal_in_depo_sql = null; // fallback kalau parsing gagal
            }
        } else {
            $tanggal_in_depo_sql = null;
        }

        // Upload files
        $bukti_photo_paths = [];
        $path_surat_jalan  = null;

        // Foto surat jalan
        if ($request->hasFile('foto_surat_jalan')) {
            $file = $request->file('foto_surat_jalan');
            $name = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $dest = public_path('surat_jalan_photo');
            if (!is_dir($dest)) @mkdir($dest, 0777, true);
            $file->move($dest, $name);
            $path_surat_jalan = 'surat_jalan_photo/' . $name;
        }

        if ($request->hasFile('bukti_photo')) {
            foreach ($request->file('bukti_photo') as $posisi => $files) {
                if (is_null($files)) {
                    continue;
                }

                // Jika single file (karena Blade tanpa []), bungkus jadi array
                $files = is_array($files) ? $files : [$files];

                $paths = [];
                foreach ($files as $f) {
                    if (!$f || !$f->isValid()) continue;

                    $name = (string) \Illuminate\Support\Str::uuid() . '.' . strtolower($f->getClientOriginalExtension());
                    $dest = public_path("surveyin_photo/{$kode_survey}/{$posisi}");
                    if (!is_dir($dest)) @mkdir($dest, 0777, true);

                    $f->move($dest, $name);
                    $paths[] = "surveyin_photo/{$kode_survey}/{$posisi}/{$name}";
                }

                if (!empty($paths)) {
                    $bukti_photo_paths[$posisi] = $paths;
                }
            }
        }

        DB::beginTransaction();
        try {
            // Insert SURVEYIN
            DB::table('surveyin')->insert([
                'kode_survey'      => $kode_survey,
                'no_container'     => $data_container->no_container,
                'jenis_container'  => $data_container->jenis_container,
                'size_type'        => $data_container->size_type,
                'gatein_time'      => $data_container->gatein_time,
                'pic_gatein'       => $data_container->pic_gatein,
                'status_wo'        => 'OPEN',
                'status_container' => $status_container,
                'grade_container'  => $grade_container,
                'no_bldo'          => $data_container->no_bldo,
                'no_truck'         => $no_truck,
                'driver'           => $driver,
                'kegiatan'         => $kegiatanString,
                'survey_time'      => DB::raw('SYSTIMESTAMP'),
                'pic'              => $pic,
                'bukti_photo'      => json_encode($bukti_photo_paths),
                // Yard
                'block'            => $block,
                'slot'             => $slot,
                'row2'             => $row,
                'tier'             => $tier,
                // Ekstra teknis
                'nama_trucking'    => $namaTrucking,
                'foto_surat_jalan' => $path_surat_jalan,
                'sizze'            => $sizze,
                'tare'             => $tare,
                'payload'          => $payload,
                'maxgross'         => $max_gross,
                'tanggal_in_depo'  => $tanggal_in_depo_sql,
            ]);

            // Update ann_import → CLOSE + timestamp
            DB::table('ann_import')
                ->where('no_container', $no_container)
                ->update([
                    'status_surveyin' => 'CLOSE',
                    'surveyin_time'   => DB::raw('SYSTIMESTAMP'),
                ]);

            // Jika DM → buat EOR header (+ detail bila ada input)
            if ($status_container === 'DM') {
                $this->insertIntoEorTable($data_container, $eor_code, $kode_survey, $pic);

                // Detail hanya jika ada isian wajib
                if ($request->filled('component') || $request->filled('damage') || $request->filled('repair')) {
                    $this->insertIntoEorDetailTable($request, $eor_code, $kode_survey, $data_container->no_container);
                }
            }

            DB::commit();
            return redirect()->route('surveyin.index')->with('success', 'Data Survey berhasil ditambahkan');
        } catch (\Throwable $e) {
            return json_encode([
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            // DB::rollBack();
            // return back()->with('error', 'Gagal menambahkan data Survey: ' . $e->getMessage())->withInput();
        }
    }

    /** Buat EOR header */
    private function insertIntoEorTable($data_container, string $eor_code, string $kode_survey, string $pic_survey): void
    {
        DB::table('eor')->insert([
            'eor_code'     => $eor_code,
            'kode_survey'  => $kode_survey,
            'no_container' => $data_container->no_container,
            'size_type'    => $data_container->size_type,
            'gatein_time'  => $data_container->gatein_time,
            'pic_gatein'   => $data_container->pic_gatein,
            'no_bldo'      => $data_container->no_bldo,
            'survey_time'  => DB::raw('SYSTIMESTAMP'),
            'pic_survey'   => $pic_survey,
            // Kolom lain (vessel, voyage, estimate_date) akan diisi di modul EOR lanjutan
        ]);
    }

    /** Buat EOR detail (opsional bila ada isian) */
    private function insertIntoEorDetailTable(Request $request, string $eor_code, string $kode_survey, string $no_container): void
    {
        $material = (float) ($request->input('material_cost') ?? 0);
        $labour   = (float) ($request->input('labour_cost') ?? 0);
        $qty      = (float) ($request->input('qty') ?? 0);
        $total    = ($material + $labour) * $qty;

        DB::table('eor_detail')->insert([
            'eor_code'     => $eor_code,
            'kode_survey'  => $kode_survey,
            'no_container' => $no_container,
            'component'    => $request->input('component'),
            'location'     => $request->input('location'),
            'damage'       => $request->input('damage'),
            'repair'       => $request->input('repair'),
            'size_repair'  => $request->input('size_repair'),
            'qty'          => $qty,
            'manhour'      => $request->input('manhour'),
            'wh'           => $request->input('wh'),
            'labour_cost'  => $labour,
            'material_cost' => $material,
            'total_cost'   => $total,
        ]);
    }


    public function cetak_eir(string $kode_survey)
    {
        $survey = DB::table('surveyin as s')
            ->leftJoin('ann_import as a', 's.no_container', '=', 'a.no_container')
            ->where('s.kode_survey', $kode_survey)
            ->select('s.*', 'a.ex_vessel', 'a.customer_code')   // <— perbaikan di sini
            // ->selectRaw('s.*, a.ex_vessel, a.customer_code')  // alternatif pakai raw
            ->first();

        if (!$survey) {
            return redirect()
                ->route('surveyin.index')
                ->with('error', 'Data Survey tidak ditemukan');
        }

        return view('admin.surveyin.cetak_eir', compact('survey'));
    }


    public function show($id) {}


    public function edit($kode_survey)
    {
        // Anggap $id = kode_survey
        $survey = DB::table('surveyin')->where('kode_survey', $kode_survey)->first();
        if (!$survey) {
            return redirect()->route('surveyin.index')->with('error', 'Data Survey tidak ditemukan');
        }
        // Dataset dropdown (sama seperti create)
        $tarif_list          = DB::table('tarif_depo')->get();
        $tarif_lolo_list     = DB::table('tarif_depo')->where('grup', 'LOLO')->get();
        $tarif_wash_list     = DB::table('tarif_depo')->where('grup', 'WASH')->get();
        $tarif_sweeping_list = DB::table('tarif_depo')->where('grup', 'SWEEPING')->get();

        $component = DB::table('cedex')->where('grup', 'Component')->get();
        $damage    = DB::table('cedex')->where('grup', 'Damage')->get();
        $repair    = DB::table('cedex')->where('grup', 'Repair')->get();

        // Gate in helper (untuk tampilan info saja)
        $gate_in  = DB::table('ann_import')
            ->join('gate_in', 'ann_import.no_container', '=', 'gate_in.no_container')
            ->select('ann_import.no_container', 'ann_import.no_bldo', 'gate_in.size_type')
            ->get();

        // Yard
        $blocks  = DB::table('yard')->select('block')->distinct()->get();
        $maxSlot = DB::table('yard')->max('slot');
        $maxRow  = DB::table('yard')->max('row2');

        $occupiedSlots = DB::table('surveyin')->select('block', 'slot')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot);
        $occupiedRows  = DB::table('surveyin')->select('block', 'slot', 'row2')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot . '_' . $i->row2);
        $occupiedTiers = DB::table('surveyin')->select('block', 'slot', 'row2', 'tier')->distinct()->get()
            ->keyBy(fn($i) => $i->block . '_' . $i->slot . '_' . $i->row2 . '_' . $i->tier);

        // Prefill kegiatan (string → array)
        $kegiatanSelected = array_map('trim', array_filter(explode(',', (string) $survey->kegiatan)));

        return view('admin.surveyin.edit', compact(
            'survey',
            'tarif_list',
            'tarif_lolo_list',
            'tarif_wash_list',
            'tarif_sweeping_list',
            'gate_in',
            'component',
            'damage',
            'repair',
            'blocks',
            'maxSlot',
            'maxRow',
            'occupiedTiers',
            'occupiedSlots',
            'occupiedRows',
            'kegiatanSelected'
        ));
    }

    public function update(Request $request, $kode_survey)
    {
        // Anggap $id = kode_survey
        $survey = DB::table('surveyin')->where('kode_survey', $kode_survey)->first();
        if (!$survey) {
            return redirect()->route('surveyin.index')->with('error', 'Data Survey tidak ditemukan');
        }
        // dd($request->all(), $survey);
        // // Validasi (semua field surveyin)
        // $validated = $request->validate([
        //     'status_container' => 'required|in:AV,DM',
        //     'grade_container'  => 'nullable|in:A,B,C,D,E',

        //     // Bisa diisi saat edit kalau sebelumnya NULL
        //     'no_bldo'          => 'nullable|string',

        //     // Kegiatan
        //     'kegiatan1'        => 'array',
        //     'kegiatan1.*'      => 'string',
        //     'kegiatan2'        => 'array',
        //     'kegiatan2.*'      => 'string',
        //     'kegiatan3'        => 'array',
        //     'kegiatan3.*'      => 'string',

        //     // Yard
        //     'block'            => 'nullable|string',
        //     'slot'             => 'nullable|string',
        //     'row2'             => 'nullable|string',
        //     'tier'             => 'nullable|string',

        //     // Teknis
        //     'nama_trucking'    => 'nullable|string',
        //     'no_truck'         => 'nullable|string',
        //     'driver'           => 'nullable|string',
        //     'sizze'            => 'nullable|string',
        //     'tare'             => 'nullable|string',
        //     'payload'          => 'nullable|string',
        //     'max_gross'        => 'nullable|string',

        //     // Waktu
        //     'tanggal_in_depo'  => 'nullable|date',

        //     // Files
        //     'foto_surat_jalan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',

        //     // bukti_photo[posisi][] (multi file per posisi)
        //     'bukti_photo'      => 'array',
        //     'bukti_photo.*'    => 'array',
        //     'bukti_photo.*.*'  => 'file|mimes:jpg,jpeg,png|max:4096',

        //     // hapus foto lama
        //     'bukti_remove'     => 'array',
        //     'bukti_remove.*'   => 'array',
        // ]);

        $nullIfEmpty = fn($v) => ($v === '' ? null : $v);

        // Gabung kegiatan 1+2+3
        $kegiatan = array_merge(
            $request->input('kegiatan1', []),
            $request->input('kegiatan2', []),
            $request->input('kegiatan3', [])
        );
        $kegiatanString = implode(', ', $kegiatan);

        $tanggal_in_depo_sql = null;
        if ($request->filled('tanggal_in_depo')) {
            try {
                $fmt = \Illuminate\Support\Carbon::createFromFormat('Y-m-d\TH:i', $request->input('tanggal_in_depo'))
                    ->format('Y-m-d H:i:s');
                $tanggal_in_depo_sql = DB::raw("TO_TIMESTAMP('{$fmt}', 'YYYY-MM-DD HH24:MI:SS')");
            } catch (\Throwable $e) {
                // biarkan null → tidak menimpa
                $tanggal_in_depo_sql = null;
            }
        }

        // Foto surat jalan (replace jika upload baru)
        $pathSuratJalan = $survey->foto_surat_jalan;
        if ($request->hasFile('foto_surat_jalan')) {
            $file = $request->file('foto_surat_jalan');
            $name = Str::random(40) . '.' . strtolower($file->getClientOriginalExtension());
            $dest = public_path('surat_jalan_photo');
            if (!is_dir($dest)) @mkdir($dest, 0777, true);
            $file->move($dest, $name);
            $pathSuratJalan = 'surat_jalan_photo/' . $name;
        }

        // Merge bukti_photo lama + hapus terpilih + tambah baru
        $currentBukti = json_decode($survey->bukti_photo ?? '[]', true);
        if (!is_array($currentBukti)) $currentBukti = [];

        // Hapus yang ditandai
        $toRemove = $request->input('bukti_remove', []);
        foreach ($toRemove as $pos => $paths) {
            if (isset($currentBukti[$pos])) {
                $currentBukti[$pos] = array_values(array_diff($currentBukti[$pos], $paths));
                // (opsional) hapus file fisik:
                // foreach ($paths as $p) { @unlink(public_path($p)); }
                if (empty($currentBukti[$pos])) unset($currentBukti[$pos]);
            }
        }

        // Tambah file baru
        if ($request->hasFile('bukti_photo')) {
            foreach ($request->file('bukti_photo') as $posisi => $files) {
                if (is_null($files)) continue;
                $files = is_array($files) ? $files : [$files];
                foreach ($files as $f) {
                    if (!$f || !$f->isValid()) continue;
                    $name = (string) Str::uuid() . '.' . strtolower($f->getClientOriginalExtension());
                    $dest = public_path("surveyin_photo/{$kode_survey}/{$posisi}");
                    if (!is_dir($dest)) @mkdir($dest, 0777, true);
                    $f->move($dest, $name);
                    $currentBukti[$posisi][] = "surveyin_photo/{$kode_survey}/{$posisi}/{$name}";
                }
            }
        }
        // Rapikan index & buang posisi kosong
        foreach ($currentBukti as $pos => $arr) {
            $arr = array_values(array_filter($arr));
            if (empty($arr)) unset($currentBukti[$pos]);
            else $currentBukti[$pos] = $arr;
        }

        DB::beginTransaction();
        try {
            DB::table('surveyin')->where('kode_survey', $kode_survey)->update([
                'status_container'  => $request->input('status_container'),
                'grade_container'   => $nullIfEmpty($request->input('grade_container')),
                'kegiatan'          => $kegiatanString,

                // Teknis
                'no_truck'          => $nullIfEmpty($request->input('no_truck')),
                'driver'            => $nullIfEmpty($request->input('driver')),
                'nama_trucking'     => $nullIfEmpty($request->input('nama_trucking')),
                'sizze'             => $nullIfEmpty($request->input('sizze')),
                'tare'              => $nullIfEmpty($request->input('tare')),
                'payload'           => $nullIfEmpty($request->input('payload')),
                'maxgross'          => $nullIfEmpty($request->input('max_gross')),
                'foto_surat_jalan'  => $pathSuratJalan,
                'tanggal_in_depo'   => $tanggal_in_depo_sql ?? $survey->tanggal_in_depo,

                // Yard
                'block'             => $nullIfEmpty($request->input('block')),
                'slot'              => $nullIfEmpty($request->input('slot')),
                'row2'              => $nullIfEmpty($request->input('row2')),
                'tier'              => $nullIfEmpty($request->input('tier')),

                // no_bldo → update jika dikirim (agar bisa isi ketika sebelumnya NULL)
                'no_bldo'           => $request->has('no_bldo')
                    ? $nullIfEmpty($request->input('no_bldo'))
                    : $survey->no_bldo,

                'bukti_photo'       => json_encode($currentBukti),
                'pic'               => (Auth::check() ? Auth::user()->name : 'SYSTEM'),
            ]);

            DB::commit();
            return redirect()->route('surveyin.index')->with('success', 'Data Survey berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            // return json_encode([
            //     'data' => $request->all(),
            //     'error' => $e->getMessage()
            // ]);
            return back()->with('error', 'Gagal update Survey: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id) {}

    /** Helper: generate kode running EIRI/EOR berbasis record terakhir */
    private function generateRunningCode(string $prefix, string $table, string $codeColumn): string
    {
        $latest = DB::table($table)->select($codeColumn)->orderByDesc($codeColumn)->first();
        $today  = date('ymd');

        if (!$latest || empty($latest->{$codeColumn})) {
            return $prefix . '-' . $today . '001';
        }

        $code = $latest->{$codeColumn};            // contoh: EIRI-250930007
        $datePart = substr($code, 5, 6);           // 250930
        $seqPart  = (int) substr($code, -3);       // 7

        $nextSeq = ($datePart === $today) ? ($seqPart + 1) : 1;

        return $prefix . '-' . $today . sprintf('%03d', $nextSeq);
    }
}
