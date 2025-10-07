<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use TCPDF;
use Milon\Barcode\DNS1D;
use DB;

class WorkorderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workorder_list = DB::table('work_order_new')->get();
        $customer = DB::table('customer_new')->get();
        $kapal = DB::table('kapal_new')->get();
        $container = DB::table('container')->get();
        $containerInWoDetail = DB::table('wo_detail')->pluck('no_container')->toArray();

        $containerlist = DB::table('surveyin')
            ->where('status_wo', 'OPEN')
            ->whereNotIn('no_container', $containerInWoDetail)
            ->get();
        $gudang = DB::table('gudang')->get();
        $tarif = DB::table('tarif_depo')->get();

        
        return view('admin.workorder.index', compact(
            'workorder_list',
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
        $workorder_list = DB::table('work_order_new')->get();
        $customer = DB::table('customer_new')->get();
        $kapal = DB::table('kapal_new')->get();
        $container = DB::table('container')->get();
        $containerInWoDetail = DB::table('wo_detail')->pluck('no_container')->toArray();

        // Ambil daftar no_container dari tabel surveyin yang status_wo nya 'OPEN' dan tidak ada di daftar containerInWoDetail
        $containerlist = DB::table('surveyin')
            ->where('status_wo', 'OPEN')
            ->whereNotIn('no_container', $containerInWoDetail)
        ->get();
        $gudang = DB::table('gudang')->get();
        $tarif = DB::table('tarif_depo')->get();

        return view('admin.workorder.create', compact(
            'workorder_list',
            'customer',
            'kapal',
            'container',
            'containerlist',
            'gudang',
            'tarif'
        ));
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
            $data_container = DB::table('surveyin')
                ->where('no_container', $row)
                ->first(); // Menggunakan first() karena hanya mengambil satu record

            if($data_container) {
                // Ambil tarif dari tabel tarif berdasarkan kegiatan container
                $a = explode(',', $data_container->kegiatan);
                $total_tarif = 0;
                foreach($a as $b){
                    $tarif = DB::table('tarif_depo')
                    ->where('nama_jasa', ltrim($b) )
                    ->value('tarif');
                    $total_tarif = $total_tarif+$tarif;
                }
                /*$tarif = DB::table('tarif_depo')
                    ->where('nama_jasa', $data_container->kegiatan)
                    ->value('tarif');*/

                $success = DB::table('wo_detail')->insert([
                    'no_container' => $data_container->no_container,
                    'nomor_wo' => $request->nomor_wo,
                    'jenis_container' => $data_container->jenis_container,
                    'size_type' => $data_container->size_type,
                    'kegiatan' => $data_container->kegiatan,
                    'tarif' => $total_tarif, // Menggunakan tarif yang didapat dari tabel tarif
                ]);
            } else {
                // Tambahkan penanganan jika data tidak ditemukan
                return redirect()->back()->with('error', 'Data container dengan nomor ' . $row . ' tidak ditemukan.');
            }
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

        $set_time = Carbon::now()->setTimezone('Asia/Jakarta');

        $user = Auth::user();
        $author = $user->name;

        $success = DB::table('work_order_new')->insert([
            'nomor_wo' => $nomor_wo,
            'tgl_wo' => $request->tgl_wo,
            'nama_customer' => $request->nama_customer,
            'nama_kapal' => $request->nama_kapal,
            'no_do' => $request->input("no_do"),
            'voyage' => $request->input("voyage"),
            'shipper' => $request->input("shipper"),
            'author' => $author,
            'nama_gudang' => $request->nama_gudang,
            'set_time' => $set_time,
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
            $data_container = DB::table('surveyin')
            ->where('no_container', $row)
            ->get();
            $items[] = $data_container;
        }
        echo json_encode($items);
    }

    public function showdetail($nomor_wo)
    {
        $data_detail = DB::table('wo_detail')
        ->where('nomor_wo', $nomor_wo)
        ->get();

        $items = array();
        foreach ($data_detail as $detail) {
            $items[] = array($detail);
        }
        echo json_encode($items);

    }

    public function cetak_wo($nomor_wo)
    {

        // Ambil data detail Work Order dengan join ke tabel surveyin untuk mendapatkan status_container
        $data_detail = DB::table('wo_detail')
        ->join('surveyin', 'wo_detail.no_container', '=', 'surveyin.no_container')
        ->where('wo_detail.nomor_wo', $nomor_wo)
        ->select('wo_detail.*', 'surveyin.status_container')
        ->get();

        // Ambil data work_order termasuk tanggal dan nama pelanggan
        $work_order = DB::table('work_order_new')
        ->where('nomor_wo', $nomor_wo)
        ->first();
        
        // Mengirimkan data ke tampilan blade 'cetak_sj'
        return view('admin.workorder.cetak_wo', compact('data_detail', 'work_order'));
        // echo json_encode($primover);
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
