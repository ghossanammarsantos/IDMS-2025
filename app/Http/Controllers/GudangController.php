<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid; 

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gudang_list = DB::table('gudang')
        ->get();
        
        return view('admin/datamaster/datagudang/index', compact(
            'gudang_list'
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
            $last_data = DB::table('gudang')->orderByDesc('kode_gudang')->first();

            $generate_kode = 'GD-001';

            if ($last_data) {
                $last_sequence = intval(substr($last_data->kode_gudang, 3));
                $next_sequence = sprintf('%03d', $last_sequence + 1);
                $generate_kode = 'GD-' . $next_sequence;
            }

            $success = DB::table('gudang')->insert([
                'kode_gudang' => $generate_kode,
                'nama_gudang' => $request->nama_gudang,
                'jenis_gudang' => $request->jenis_gudang,
                'luas' => $request->luas,
                'lokasi' => $request->lokasi,
                'alamat' => $request->alamat,
                'status_gd' => $request->status_gd
            ]);

            if ($success) {
                return redirect()->route('gudang.index')->with('success', 'Data Gudang berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan data Gudang');
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
        $success = DB::table('gudang')->where('nama_gudang', $id)->delete();

        if ($success) {
            return redirect()->route('gudang.index')->with('success', 'Data Gudang berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data Gudang');
        }
    }
}
