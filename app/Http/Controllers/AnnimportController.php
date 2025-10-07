<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InReportSITCImport;
use App\Imports\AnnImportUpload;
use Illuminate\Validation\Rule;

class AnnimportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $annimport_list = DB::table('ann_import')->get();
        $container = DB::table('container')->get();

        return view('admin/annimport/index', compact(
            'annimport_list',
            'container'
        ));
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
        $customer_code = $request->input('customer_code');
        $no_container = $request->input('no_container');
        $no_bldo = $request->input('no_bldo');
        $size_type = $request->input('size_type');
        $ex_vessel = $request->input('ex_vessel');
        $voyage = $request->input('voyage');
        $tanggal_berthing = $request->input('tanggal_berthing');
        $consignee = $request->input('consignee');
        $remarks = $request->input('remarks');

        $success = DB::table('ann_import')->insert([
            'customer_code' => $customer_code,
            'no_container' => $no_container,
            'no_bldo' => $no_bldo,
            'size_type' => $size_type,
            'ex_vessel' => $ex_vessel,
            'voyage' => $voyage,
            'tanggal_berthing' => $tanggal_berthing,
            'consignee' => $consignee,
            'remarks' => $remarks,
            'status_surveyin' => "OPEN",
            'surveyin_time' => null,
            'set_time' => now(),
        ]);

        if ($success) {
            return redirect()->route('annimport.index')->with('success', 'Data Announcement Import berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Announcement Import');
        }
    }

    /**
     * Import data from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:51200',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes'    => 'Format harus .xlsx atau .xls.',
            'excel_file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File tidak valid atau gagal diupload.');
            }

            $import = new AnnImportUpload();
            Excel::import($import, $file);

            $summary = [
                'sukses' => $import->inserted,
                'gagal'  => count($import->failuresBag),
                'detail' => $import->failuresBag,
            ];

            return redirect()
                ->route('annimport.index')
                ->with('import_summary', $summary)
                ->with('success', "Import selesai. Berhasil: {$summary['sukses']}, Gagal: {$summary['gagal']}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request)
    {
        DB::table('ann_import')
            ->where('no_container', $request->no_container)
            ->update(['status_surveyin' => $request->status_survey]);

        // Redirect atau response sesuai kebutuhan aplikasi Anda
        return redirect()->route('annimport.index')->with('success', 'Status Survey berhasil diupdate');
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

    public function update(Request $request, $no_container)
    {
        $validated = $request->validate([
            'customer_code'    => ['nullable', 'string', 'max:50'],
            'no_container'     => [
                'required',
                'string',
                'max:20',
                Rule::unique('ann_import', 'no_container')->ignore($no_container, 'no_container'),
            ],
            'no_bldo'          => ['nullable', 'string', 'max:50'],
            'consignee'        => ['nullable', 'string', 'max:100'],
            'size_type'        => ['nullable', 'string', 'max:20'],
            'ex_vessel'        => ['nullable', 'string', 'max:100'],
            'tanggal_berthing' => ['nullable', 'date'],
            'remarks'          => ['nullable', 'string', 'max:255'],
            'status_surveyin'    => ['required', 'in:OPEN,CLOSE'],
        ]);

        // nilai lama yang dipakai untuk WHERE (lebih akurat dari URL param)
        $original = $request->input('original_no_container', $no_container);

        // pastikan data ada
        $exists = DB::table('ann_import')->where('no_container', $original)->first();
        if (!$exists) {
            return redirect()->route('annimport.index')->with('error', 'Data tidak ditemukan.');
        }

        $payload = [
            'customer_code'    => $validated['customer_code'] ?? null,
            'no_container'     => $validated['no_container'],
            'no_bldo'          => $validated['no_bldo'] ?? null,
            'consignee'        => $validated['consignee'] ?? null,
            'size_type'        => $validated['size_type'] ?? null,
            'ex_vessel'        => $validated['ex_vessel'] ?? null,
            'tanggal_berthing' => $validated['tanggal_berthing'] ?? null,
            'remarks'          => $validated['remarks'] ?? null,
            'status_surveyin'    => $validated['status_surveyin'],
            'set_time'         => now(),
        ];

        $affected = DB::table('ann_import')
            ->where('no_container', $original) // target baris dengan nilai lama
            ->update($payload);

        // $affected bisa 0 kalau nilainya tidak berubah—tetap anggap sukses
        return redirect()
            ->route('annimport.index')
            ->with('success', $affected ? 'Data berhasil diperbarui.' : 'Tidak ada perubahan data.');
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
