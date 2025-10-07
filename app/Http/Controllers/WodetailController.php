<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\WorkOrder;
use DB;

class WodetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wodetail_list = DB::table('wo_kegiatan')
        ->paginate(10);
        
        return view('admin/inventoryin/wodetail/index', compact(
            'wodetail_list'
        ));
    }

    public function search(Request $request)
    {
        // Cek apakah terdapat pencarian
        if ($request->has('search_wo')) {
            // Retrieve the search term from the request
            $search_wo = $request->input('search_wo');
            
            // Lakukan pencarian berdasarkan nomor WO
            $wodetail_list = DB::table('wo_kegiatan')
                ->where('nomor_wo', $search_wo)
                ->paginate(10);

            // Jika tidak ada hasil pencarian
            if ($wodetail_list->isEmpty()) {
                return redirect()->route('wodetail.index')->with('error', 'Nomor WO tidak ditemukan.');
            }
        } else {
            // Jika tidak ada pencarian, tampilkan semua data
            $wodetail_list = DB::table('wo_kegiatan')->paginate(10);
        }
        
        // Tampilkan data pada halaman index
        return view('admin/inventoryin/wodetail/index', compact('wodetail_list'));
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
