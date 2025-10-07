<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EstimateofrepairController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil data pembayaran yang belum digate out
        $eor_list = DB::table('eor')->get();
        $dataeor = DB::table('surveyin')
        ->where('status_container', 'DM')
        ->get(['no_container']);

        return view('admin.eor.index', compact(
            'eor_list', 
            'dataeor'
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

    public function edit($eor_code)
    {
        $eor = DB::table('eor')->where('eor_code', $eor_code)->first(); 
    
        $eor_details = DB::table('eor_detail')->where('eor_code', $eor_code)->get();
        if ($eor) {
            $kode_survey = $eor->kode_survey;

            
            $surveyin = DB::table('surveyin')->where('kode_survey', $kode_survey)->first();
            if ($surveyin) {
                $photos = json_decode($surveyin->bukti_photo, true);
                if (!is_array($photos)) {
                    $photos = [];
                }

                return view('admin.eor.edit', compact('eor', 'photos', 'eor_details'));
            } else {
                return redirect()->back()->with('error', 'Data surveyin tidak ditemukan.');
            }
        } else {
            return redirect()->back()->with('error', 'Data eor tidak ditemukan.');
        }
    }

    // Method to show the edit form
    public function editeordetail($eor_code)
    {
       
        $eor_detail = DB::table('eor_detail')->where('eor_code', $eor_code)->first();
        $components = DB::table('cedex')->where('grup', 'Component')->get();
        $damages = DB::table('cedex')->where('grup', 'Damage')->get();
        $repairs = DB::table('cedex')->where('grup', 'Repair')->get();

        return view('admin.eor.editeordetail', compact('eor_detail', 'components', 'damages', 'repairs'));
    }

    // Method to update the EOR
    public function updateeordetail(Request $request, $eor_code)
    {
        
        $eor_detail = DB::table('eor_detail')->where('eor_code', $eor_code)->first();

        // Validasi data input
        $request->validate([
            'component' => 'required',
            'location' => 'required',
            'damage' => 'required',
            'repair' => 'required',
            'size_repair' => 'required',
            'qty' => 'required|integer',
            'manhour' => 'required',
            'wh' => 'required',
            'labour_cost' => 'required|numeric',
            'material_cost' => 'required|numeric',
        ]);

        // Ambil nilai dari request
        $material_cost = $request->input('material_cost');
        $labour_cost = $request->input('labour_cost');
        $qty = $request->input('qty');

        // Hitung total_cost
        $total_cost = ($material_cost + $labour_cost) * $qty;

        // Update data EOR detail
        DB::table('eor_detail')->where('eor_code', $eor_code)->update([
            'component' => $request->component,
            'location' => $request->location,
            'damage' => $request->damage,
            'repair' => $request->repair,
            'size_repair' => $request->size_repair,
            'qty' => $request->qty,
            'manhour' => $request->manhour,
            'wh' => $request->wh,
            'labour_cost' => $request->labour_cost,
            'material_cost' => $request->material_cost,
            'total_cost' => $total_cost, // Update total_cost
        ]);

        return redirect()->route('eor.edit', ['eor_code' => $eor_code])->with('success', 'Data updated successfully');
    }

    public function cetak_eor($eor_code)
    {
        try {
            $print_eor = DB::table('eor')
                ->join('eor_detail', 'eor.no_container', '=', 'eor_detail.no_container')
                ->select('eor.*', 'eor_detail.component', 'eor_detail.location', 'eor_detail.damage', 'eor_detail.size_repair', 'eor_detail.repair', 'eor_detail.qty', 'eor_detail.manhour', 'eor_detail.labour_cost', 'eor_detail.material_cost', 'eor_detail.total_cost')
                ->where('eor.eor_code', $eor_code)
                ->first();

            // dd($print_eor);
            if (!$print_eor) {
                return redirect()->back()->with('error', 'No EOR record found for the given survey code.');
            }
    
            $barcodeeirin = $this->generateBarcodeHTML($print_eor->eor_code);
    
            return view('admin.eor.cetak_eor', compact('print_eor', 'barcodeeirin'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching EOR details: ' . $e->getMessage());
        }
    }

    private function generateBarcodeHTML($eor_code)
    {
        $barcodeHTML = '<barcode code="' . $eor_code . '" type="C128" />';
        return $barcodeHTML;
    }
    

    public function start($eor_code)
    {
        $eor = DB::table('eor')->where('eor_code', $eor_code)->first();

        if (!$eor) {
            return redirect()->back()->with('error', 'Data EOR tidak ditemukan.');
        }

        if (!$eor->date_started) {
            $date_started = Carbon::now()->setTimezone('Asia/Jakarta');
            
            DB::table('eor')->where('eor_code', $eor_code)->update([
                'date_started' => $date_started
            ]);
        }
        
        return redirect()->route('eor.edit', ['eor_code' => $eor_code]);
    }


    public function complete($eor_code)
    {
        $eor = DB::table('eor')->where('eor_code', $eor_code)->first();

        if (!$eor) {
            return redirect()->back()->with('error', 'Data EOR tidak ditemukan.');
        }
        if ($eor->date_started && !$eor->date_completed) {
            $date_completed = Carbon::now()->setTimezone('Asia/Jakarta');

            DB::table('eor')->where('eor_code', $eor_code)->update([
                'date_completed' => $date_completed
            ]);
        }
        return redirect()->route('eor.edit', ['eor_code' => $eor_code]);
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
        $eor = DB::table('eor')->where('eor_code', $eor_code)->first();

        if (!$eor) {
            return redirect()->route('eor.index')->with('error', 'Data EOR tidak ditemukan');
        }

        $updateData = [
            'vessel' => $request->input('vessel'),
            'voyage' => $request->input('voyage'),
            'shipper' => $request->input('shipper'),
            'date_completed' => $request->input('date_completed'),
        ];

        $date_started = Carbon::now()->setTimezone('Asia/Jakarta');
        if (!$eor->date_started) {
            $updateData['date_started'] = $date_started; 
        } elseif ($request->input('date_started')) {
            $updateData['date_started'] = $request->input('date_started');
        }

        DB::table('eor')->where('eor_code', $eor_code)->update($updateData);

        return redirect()->route('eor.edit', ['eor_code' => $eor_code])->with('success', 'EOR berhasil diperbarui');
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
