<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_list = DB::table('customer_new')
        // ->orderBy('set_time','DESC')
        ->get();

        return view('admin/datamaster/datacustomer/index', compact(
            'customer_list'
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
        $data_order = DB::table('customer_new')->orderBy('set_time', 'desc')->first();

        $generate_kode = date('ym') . '001';
        
        if ($data_order) {
            $last_kode = explode('-', $data_order->kode_customer)[1];
            $last_sequence = intval(substr($last_kode, 6));
            $generate_kode = date('ym') . sprintf('%03d', $last_sequence + 1);
        }
        
        $data = [
            'kode_customer' => 'CUST-' . $generate_kode,
            'nama_customer' => $request->nama_customer,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'negara' => $request->negara,
            'tgl_bergabung' => $request->tgl_bergabung,
            'kategori_customer' => $request->kategori_customer,
            'set_time' => date('Y-m-d'),
        ];
        
        $success = DB::table('customer_new')->insert($data);
        
        if ($success) {
            return redirect()->route('customer.index')->with('success', 'Data Customer berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Customer');
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
        $success = DB::table('customer_new')->where('nama_customer', $id)->delete();

        if ($success) {
            return redirect()->route('customer.index')->with('success', 'Data Customer berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data Customer');
        }
    }
}
