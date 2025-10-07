<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/billing/index');
    }


    public function searchWorkOrder($nomor_wo)
    {
        $keyword = trim($nomor_wo);

        if (!$keyword) {
            return response()->json([], 400); // Bad Request jika keyword kosong
        }

        // Cari work order berdasarkan nomor work order
        $searchResults = DB::table('wo_detail')
        ->join('work_order_new', 'wo_detail.nomor_wo', '=', 'work_order_new.nomor_wo')
        ->leftJoin('ann_import', 'wo_detail.no_container', '=', 'ann_import.no_container') 
        ->leftJoin('surveyin', 'wo_detail.no_container', '=', 'surveyin.no_container')
        ->select('wo_detail.*', 'work_order_new.nama_customer', 'ann_import.no_bldo', 'surveyin.status_container') // Pilih kolom dari ann_import dan surveyin
        ->where('wo_detail.nomor_wo', 'like', "%$keyword%")
        ->get();

        
        if ($searchResults->isEmpty()) {
            return view('admin.billing.WOunavailable');
        }

        
        foreach ($searchResults as $result) {
            if ($result->status_container === 'DM') {
                $result->dm_details = DB::table('eor_detail')
                    ->where('no_container', $result->no_container)
                    ->get();
            } else {
                $result->dm_details = [];
            }
        }

        $isPaid = DB::table('payments')
            ->where('nomor_wo', $keyword)
            ->exists();

        if ($isPaid) {
            return view('admin.billing.WorkOrderPay', compact('searchResults'));
        } else {
            return view('admin.billing.searchWorkOrder', compact('searchResults'));
        }
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

    public function getTarifByKegiatan(Request $request)
    {
        $kegiatan = $request->input('kegiatan');

        // Validasi kegiatan
        if (!$kegiatan) {
            return response()->json([], 400); // Bad Request jika kegiatan kosong
        }

        // Ambil tarif dari tabel tarif_depo berdasarkan nama kegiatan
        $tarif = DB::table('tarif_depo')
            ->where('nama_jasa', $kegiatan)
            ->value('tarif');

        return response()->json($tarif);
    }


    public function savePayment(Request $request)
    {
        // Generate nomor pembayaran
        $nomorPayment = $this->generateNomorPayment();
        $created_at = Carbon::now()->setTimezone('Asia/Jakarta');
        $updated_at = Carbon::now()->setTimezone('Asia/Jakarta');

        // Retrieve the request data
        $nomor_wo = $request->input('nomor_wo');
        $nama_customer = $request->input('nama_customer');
        $totalTarif = floatval(str_replace(',', '', $request->input('total_tarif')));
        $totalPayment = floatval(str_replace(',', '', $request->input('total_payment')));
        $diskon = $request->input('diskon');
        $jmlDiskon = $request->input('hidden_jml_diskon',"%");
        $sisaBayar = $request->input('hidden_sisa_bayar');
        $paymentMethod = $request->input('payment_method');
        $jumlahContainer = $request->input('jumlah_container');

        // Prepare data to insert
        $data = [
            'nomor_payment' => $nomorPayment,
            'nomor_wo' => $nomor_wo,
            'nama_customer' => $nama_customer,
            'total_payment' => $totalPayment,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'diskon' => $diskon,
            'jml_diskon' => $jmlDiskon,
            'sisa_bayar' => $sisaBayar,
            'payment_method' => $paymentMethod,
            'total_tarif' => $totalTarif,
            'jumlah_container' => $jumlahContainer,
            'status' => 'PAID',
        ];

        // Insert data into the payments table
        $payment = DB::table('payments')->insert($data);

        if ($payment) {
            // Update the status of the work order
            DB::table('wo_detail')
                ->where('nomor_wo', $nomor_wo)
                ->update(['status_bayar' => 'PAYMENT']);

            return redirect()->route('payment.index')->with('success', 'Pembayaran berhasil disimpan.');
        } else {
            return redirect()->route('billing.index')->with('error', 'Gagal menyimpan pembayaran.');
        }
    }


    private function generateNomorPayment()
    {
        $lastPayment = DB::table('payments')->orderBy('created_at', 'desc')->first();
        $lastSequence = $lastPayment ? intval(substr($lastPayment->nomor_payment, -3)) + 1 : 1;
        $generateKode = date('ym') . sprintf('%03d', $lastSequence);
        return 'PAY-' . $generateKode;
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
