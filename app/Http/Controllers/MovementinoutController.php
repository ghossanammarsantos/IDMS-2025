<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MovementinoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil data dari kedua tabel dan menggabungkannya
        $datalist = DB::table('surveyin')->get();
        $datain = DB::table('surveyin')
        ->get(['no_container']);
        $dataout = DB::table('surveyout')
        ->get(['no_container']);

        return view('admin.movementinout.index', compact(
            'datalist',
            'datain',
            'dataout'
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

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($kode_survey)
    {
        $data = DB::table('surveyin')
        ->leftJoin('gate_in', 'surveyin.no_container', '=', 'gate_in.no_container')
        ->leftJoin('surveyout', 'surveyin.no_container', '=', 'surveyout.no_container')
        ->leftJoin('gate_out', 'surveyin.no_container', '=', 'gate_out.no_container')
        ->select(
            'surveyin.survey_time as surveyin_time',
            'surveyin.pic as surveyin_pic',
            'surveyin.bukti_photo as surveyin_photos', // pastikan field ini benar
            'surveyin.foto_surat_jalan as foto_surat_jalan',
            'surveyout.surveyout_time as surveyout_time',
            'surveyout.pic_surveyout as surveyout_pic',
            'surveyout.bukti_photo as surveyout_photos', // pastikan field ini benar
            'gate_in.gatein_time',
            'gate_out.gateout_time'
        )
        ->where('surveyin.kode_survey', $kode_survey)
        ->first();

    // Handle case where photos are null or not strings
    $surveyinPhotos = !empty($data->surveyin_photos) ? json_decode($data->surveyin_photos, true) : [];
    $surveyoutPhotos = !empty($data->surveyout_photos) ? json_decode($data->surveyout_photos, true) : [];

    return view('admin.movementinout.show', compact('data', 'surveyinPhotos', 'surveyoutPhotos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($eor_code)
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
    public function update(Request $request, $eor_code)
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
