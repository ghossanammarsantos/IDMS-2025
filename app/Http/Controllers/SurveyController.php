<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $survey_list = DB::table('survey')->get();
        $tarif_list = DB::table('tarif_depo')->get();
        $gate_in = DB::table('gate_in')->get();
        return view('admin.survey.index', compact('survey_list', 'tarif_list', 'gate_in'));

        // return view('admin.survey.index', compact('surveydetail_list'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $survey_list = DB::table('survey')->get();
        $tarif_list = DB::table('tarif_depo')->get();
        // Ambil semua nomor kontainer yang sudah dipilih
        $selected_containers = DB::table('survey')->pluck('no_container')->toArray();

        // Ambil semua nomor kontainer yang belum dipilih
        $gate_in = DB::table('gate_in')
            ->whereNotIn('no_container', $selected_containers)
            ->get();
        return view('admin.survey.create', compact('survey_list', 'tarif_list', 'gate_in'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $no_container = $request->input('no_container');
        $status_survey = $request->input('status_survey');
        $set_time = Carbon::now()->setTimezone('Asia/Jakarta');

        $success = DB::table('ann_import')->insert([
            'no_container' => $no_container,
            'jenis_container' => $request->jenis_container,
            'ukuran_container' => $request->ukuran_container,
            'status_survey' => $status_survey,
            'set_time' => $set_time,
        ]);

        if ($success) {
            return redirect()->route('annimport.index')->with('success', 'Data Announcement Import berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Announcement Import');
        }
    }

    public function store_detail(Request $request)
{
    $no_container = $request->input('no_container');
    $data_container = DB::table('gate_in')
        ->selectRaw('ROWIDTOCHAR(rowid) as "rowid", no_container, jenis_container, ukuran_container, gatein_time, pic_gatein')
        ->where('no_container', $no_container)->first(); // Menggunakan first() karena hanya satu container yang diambil
    // dd($data_container);
    $jenis_container = $data_container->jenis_container;
    $ukuran_container = $data_container->ukuran_container;
    $gatein_time = $data_container->gatein_time;
    $pic_gatein = $data_container->pic_gatein;
    $status_container = $request->input('status_container');
    $grade_container = $request->input('grade_container');
    $data_kegiatan = $request->input('kegiatan');
    $status_gate = $request->input('status_gate');
    $pic = $request->input('pic'); 
    $survey_time = Carbon::now()->setTimezone('Asia/Jakarta');

    // Menyimpan nilai kegiatan dalam bentuk array jika ada, atau mengosongkan jika tidak ada
    $kegiatan = !empty($data_kegiatan) ? implode('-', $data_kegiatan) : null;

    $success = DB::table('survey')->insert([
        'no_container' => $no_container,
        'jenis_container' => $jenis_container,
        'ukuran_container' => $ukuran_container,
        'gatein_time' => $gatein_time,
        'pic_gatein' => $pic_gatein,
        'status_wo' => 'OPEN',
        'status_container' => $status_container,
        'grade_container' => $grade_container,
        'kegiatan' => $kegiatan,
        'survey_time' => $survey_time,
        'pic' => $pic,
    ]);

    if ($success) {
        // Jika penyimpanan survey berhasil, lanjutkan menyimpan data detail survey
        $data_survey = DB::table('survey')
            ->selectRaw('ROWIDTOCHAR(rowid) as "rowid", rownum, no_container')
            ->orderBy('rownum', 'DESC')
            ->first(); // Menggunakan first() karena hanya satu survey yang diambil

        if (is_array($request->status)) {
            $success_detail = [];
        
            foreach ($request->status as $row) {
                $success_detail[] = DB::table('survey_detail')->insert([
                    'rowid_survey' => $data_survey->rowid,
                    'keadaan' => $row,
                    'set_time' => now(),
                    'no_container' => $no_container,
                ]);
            }
        }
        return redirect()->route('survey.index')->with('success', 'Data Survey berhasil ditambahkan');
    } else {
        return redirect()->back()->with('error', 'Gagal menambahkan data Survey');
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
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');


            if ($file->isValid()) {
                $data = \Excel::toArray([], $file);

                $urutan = 1;
                if (!empty($data)) {
                    foreach ($data[0] as $row) {
                        if ($urutan > 1) {
                            // dd($row);
                            DB::table('ann_import')->insert([
                                'no_container' => $row[0],
                                'jenis_container' => $row[1],
                                'ukuran_container' => $row[2],
                                'status_survey' => $row[3],
                                'set_time' => now(),
                            ]);
                        }
                        $urutan++;
                    }
                }
                return redirect()->route('annimport.index')->with('success', 'Data from Excel imported successfully.');
            } else {
                return redirect()->back()->with('error', 'Invalid file or file not found.');
            }
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'An error occurred while importing data from Excel.');
        }
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
