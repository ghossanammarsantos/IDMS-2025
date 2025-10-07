<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class TarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tarif_list = DB::table('tarif_depo')
        ->get();

        $tarif_lolo_list = DB::table('tarif_depo')
        ->where('grup', 'LOLO')
        ->get();
        
        $tarif_wash_list = DB::table('tarif_depo')
        ->where('grup', 'WASH')
        ->get();
        
        return view('admin/datamaster/datatarif/index', compact(
            'tarif_list', 
            'tarif_lolo_list',
            'tarif_wash_list' 
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
        // Validasi input form
        $validatedData = $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'cedex' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'grup' => 'required|string|max:255',
            'tarif' => 'required|numeric',
        ]);

        // Menyimpan data ke dalam database menggunakan DB Facade
        $success = DB::table('tarif_depo')->insert([
            'nama_jasa' => $validatedData['nama_jasa'],
            'cedex' => $validatedData['cedex'],
            'deskripsi' => $validatedData['deskripsi'],
            'grup' => $validatedData['grup'],
            'tarif' => $validatedData['tarif'],
        ]);

        // Redirect ke halaman index dengan pesan sukses jika penyimpanan berhasil
        if ($success) {
            return redirect()->route('tarif.index')->with('success', 'Data Tarif berhasil ditambahkan');
        } else {
            // Jika terjadi error, redirect ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', 'Gagal menambahkan data Tarif');
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
        $success = DB::table('tarif_depo')->where('nama_jasa', $id)->delete();

        if ($success) {
            return redirect()->route('tarif.index')->with('success', 'Data Tarif berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data Tarif');
        }
    }
}
