<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CedexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cedex_list = DB::table('cedex')->get();
        
        return view('admin/datamaster/datacedex/index', compact(
            'cedex_list' 
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
            'cedex_code' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'grup' => 'required|string|max:255',
        ]);

        // Menyimpan data ke dalam database menggunakan DB Facade
        $success = DB::table('cedex')->insert([
            'cedex_code' => $validatedData['cedex_code'],
            'deskripsi' => $validatedData['deskripsi'],
            'grup' => $validatedData['grup']
        ]);

        // Redirect ke halaman index dengan pesan sukses jika penyimpanan berhasil
        if ($success) {
            return redirect()->route('cedex.index')->with('success', 'Data Tarif berhasil ditambahkan');
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
        //
    }
}
