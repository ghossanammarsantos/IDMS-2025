<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MonitoringyardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch blocks with their rows, slots, and tiers
        $blocks = DB::table('yard')->get();

        // Pass blocks to the view
        return view('admin.monitoringyard.monitoring', compact('blocks'));
    }

    public function tierDetails(Request $request)
    {
        $block = $request->input('block');
        $row = $request->input('row');
        $slot = $request->input('slot');
    
        // Fetch tier details based on the block, row, and slot
        $tierDetails = DB::table('yard')
            ->where('block', $block)
            ->where('row2', $row)
            ->where('slot', $slot)
            ->orderBy('tier', 'asc')
            ->get();
    
        // Return data as JSON
        return response()->json($tierDetails);
    }
    
    public function getMonitoringYard($block, $slot, $row, $tier)
    {
        // Ambil data monitoring yard berdasarkan block, slot, row, dan tier
        $monitoringYardData = DB::table('yard')
            ->where('block', $block)
            ->where('slot', $slot)
            ->where('row2', $row)
            ->where('tier', $tier)
            ->get();

        // Render view dengan data yang diambil
        $html = view('admin.monitoringyard.monitoring', compact('monitoringYardData'))->render();
        
        return response()->json(['html' => $html]);
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
        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($kode_survey)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($eor_code)
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
    public function update(Request $request, $eor_code)
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
