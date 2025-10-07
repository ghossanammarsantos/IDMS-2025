<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DeliveryorderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryorder_list = DB::table('delivery_order')->get();
        // ->select('work_order.nomor_wo', 'work_order.tgl_wo', 'customer.nama_customer', 'work_order.jenis_pekerjaan', 'work_order.ket_pekerjaan', 'work_order.status_ab', 'work_order.status_gd', 'work_order.status_wo')
        // ->join('customer', 'customer.kode_customer', '=', 'work_order.kode_customer')
        // ->where('work_order.tgl_wo', '=', '2024-01-03')
        // ->where('work_order.status_gd', '=', 'OPEN')
        
            

        $customer = DB::table('customer_new')->get();
        $kapal = DB::table('kapal_new')->get();
        $container = DB::table('container')->get();
        $containerlist = DB::table('survey')->get();
        $gudang = DB::table('gudang')->get();
        $tarif = DB::table('tarif_depo')->get();

        
        return view('admin/deliveryorder/index', compact(
            'deliveryorder_list',
            'customer',
            'kapal',
            'container',
            'containerlist',
            'gudang',
            'tarif'
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
    public function storedetail(Request $request)
    {
        foreach($request->no_container as $row){
            $data_container = DB::table('survey')
                ->where('no_container', $row)
                ->get();

            $success = DB::table('wo_detail')->insert([
                'no_container' => $data_container[0]->no_container,
                'no_workorder' => $request->no_workorder,
                'jenis_container' => $data_container[0]->jenis_container,
                'ukuran_container' => $data_container[0]->ukuran_container,
                'kegiatan' => $data_container[0]->kegiatan,
                'tarif' => $data_container[0]->tarif,
            ]);

        }

        // Redirect ke halaman yang sesuai setelah menyimpan detail pekerjaan
        return redirect()->route('workorder.index')->with('success', 'Detail pekerjaan berhasil disimpan.');
    }


    public function store(Request $request)
    {
        // Generate nomor WO
        $data_order = DB::table('work_order_new')->orderBy('set_time', 'desc')->first();

        $last_sequence = 1;
        if ($data_order) {
            $last_sequence = intval(substr($data_order->nomor_wo, -3)) + 1;
        }

        $generate_kode = date('ymd') . sprintf('%03d', $last_sequence);
        $nomor_wo = 'WO-' . $generate_kode;


        // Simpan data ke database
        $success = DB::table('work_order_new')->insert([
            'nomor_wo' => $nomor_wo,
            'tgl_wo' => $request->tgl_wo,
            'nama_customer' => $request->nama_customer,
            'nama_kapal' => $request->nama_kapal,
            'jenis_container' => $request->jenis_container,
            'nama_gudang' => $request->nama_gudang,
            'set_time' => now(),
        ]);

        if ($success) {
            return redirect()->route('workorder.index')->with('success', 'Data Work Order berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Work Order');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($no_container)
    {
        $no_container = explode(',',$no_container);
        $items = array();
        foreach($no_container as $row){
            $data_container = DB::table('survey')
            ->where('no_container', $row)
            ->get();
            $items[] = $data_container;
        }
        echo json_encode($items);
    }

    public function showdetail($nomor_wo)
    {
        $data_detail = DB::table('wo_detail')
        ->where('no_workorder', $nomor_wo)
        ->get();

        $items = array();
        foreach ($data_detail as $detail) {
            $items[] = array($detail);
        }
        echo json_encode($items);

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
