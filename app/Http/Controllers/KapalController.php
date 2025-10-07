<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Str;

class KapalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kapal_list = DB::table('kapal_new')
        ->get();
        
        return view('admin/datamaster/datakapal/index', compact(
            'kapal_list'
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
        $data_order = DB::table('kapal_new')->orderBy('set_time', 'desc')->first();

        $generate_kode = date('ym') . '001';

        if ($data_order) {
            $last_kode = explode('-', $data_order->kode_kapal)[1];
            $last_sequence = intval(substr($last_kode, 6));
            $generate_kode = date('ym') . sprintf('%03d', $last_sequence + 1);
        }

        $data = [
            'kode_kapal' => $request->kode_kapal . '-' . $generate_kode,
            'nama_kapal' => $request->nama_kapal,
            'bendera' => $request->bendera,
            'pemilik' => $request->pemilik,
            'alamat' => $request->alamat,
            'status' => $request->status,
            // 'author' => Auth::user()->username,
            'author' => 'ADMIN',
            'set_time' => date('Y-m-d'),
        ];

        $success = DB::table('kapal_new')->insert($data);

        if ($success) {
            return redirect()->route('kapal.index')->with('success', 'Data Kapal berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Kapal');
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
        $success = DB::table('kapal_new')->where('nama_kapal', $id)->delete();

        if ($success) {
            return redirect()->route('kapal.index')->with('success', 'Data Kapal berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data Kapal');
        }
    }
}
