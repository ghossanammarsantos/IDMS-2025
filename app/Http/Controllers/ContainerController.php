<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $container_list = DB::table('container')
            ->get();

        return view('admin/datamaster/datacontainer/index', compact(
            'container_list'
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
        $validatedData = $request->validate([
            'nama_container' => 'required|string|max:255',
            'jenis_container' => 'required|string|max:255',
            'ukuran_container' => 'required|string|max:255',
        ]);

        $success = DB::table('container')->insert([
            'nama_container' => $validatedData['nama_container'],
            'jenis_container' => $validatedData['jenis_container'],
            'ukuran_container' => $validatedData['ukuran_container'],
        ]);

        if ($success) {
            return redirect('/admin/datamaster/datacontainer')->with('success', 'Data Container berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data Container');
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
        $success = DB::table('container')->where('nama_container', $id)->delete();

        if ($success) {
            return redirect()->route('container.index')->with('success', 'Data Container berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data Container');
        }
    }
}
