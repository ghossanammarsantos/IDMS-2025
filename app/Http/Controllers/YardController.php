<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class YardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $yardData = DB::table('yard')->get();
        return view('admin.datamaster.datayard.index', compact('yardData'));
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
        $request->validate([
            'block' => 'required|string|max:10',
            'row2' => 'required|integer',
            'slot' => 'required|integer',
            'tier' => 'required|integer',
            'remark' => 'nullable|string|max:255',
        ]);

        DB::table('yard')->insert([
            'block' => $request->block,
            'row2' => $request->row2,
            'slot' => $request->slot,
            'tier' => $request->tier,
            'remark' => $request->remark,
        ]);

        return redirect()->route('yard.index')->with('success', 'Yard data added successfully!');
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
