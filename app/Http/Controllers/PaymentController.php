<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentList = DB::table('payments')->get();
        return view('admin.payment.index', compact('paymentList'));
    }


    public function cetak_inv($nomor_wo)
    {
        // Ambil data dari tabel PAYMENTS berdasarkan NOMOR_WO menggunakan Query Builder
        $payment = DB::table('payments')->where('nomor_wo', $nomor_wo)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Nomor WO tidak ditemukan.');
        }

        // Ambil detail work order beserta join tabel tambahan
        $woDetails = DB::table('wo_detail')
        ->join('work_order_new', 'wo_detail.nomor_wo', '=', 'work_order_new.nomor_wo')
        ->leftJoin('ann_import', 'wo_detail.no_container', '=', 'ann_import.no_container') 
        ->leftJoin('surveyin', 'wo_detail.no_container', '=', 'surveyin.no_container')
        ->select('wo_detail.*', 'work_order_new.nama_customer', 'ann_import.no_bldo', 'surveyin.status_container')
        ->where('wo_detail.nomor_wo', $nomor_wo)
        ->get();

        $total_biaya = 0;

        foreach ($woDetails as $detail) {
            // Tambahkan tarif dasar ke total biaya
            $total_biaya += $detail->tarif;

            // Jika status_container adalah 'DM', tambahkan biaya dari dm_details
            if ($detail->status_container === 'DM') {
                $detail->dm_details = DB::table('eor_detail')
                    ->where('no_container', $detail->no_container)
                    ->get();

                foreach ($detail->dm_details as $dm) {
                    $total_biaya += $dm->total_cost;
                }
            } else {
                $detail->dm_details = [];
            }
        }

// Kirim total biaya ke view
return view('admin.payment.cetak_inv', compact('payment', 'woDetails', 'total_biaya'));


        // Hitung total biaya
        $total_biaya = $woDetails->sum('tarif');

        // Kirim data ke view
        return view('admin.payment.cetak_inv', compact('payment', 'woDetails', 'total_biaya'));
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
