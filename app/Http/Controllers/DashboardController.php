<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
        public function __construct()
        {
            $this->middleware('auth');
        }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        Carbon::setLocale('id');
        $currentDay = Carbon::now()->translatedFormat('l');
        $currentMonth = Carbon::now()->translatedFormat('F'); 
        $currentYear = Carbon::now()->format('Y');

        // Daily data
        $dataIn = DB::table('gate_in')->whereDate('gatein_time', $today)->count();
        $dataOut = DB::table('gate_out')->whereDate('gateout_time', $today)->count();
        $dataTotalToday = $dataIn + $dataOut;
        $dataIn20ft = DB::table('gate_in')
        ->whereDate('gatein_time', $today)
        ->where('size_type', '20GP')
        ->count();
        $dataIn40ft = DB::table('gate_in')
        ->whereDate('gatein_time', $today)
        ->where('size_type', '40HC')
        ->count();
        $dataOut20ft = DB::table('gate_out')
        ->join('gate_in', 'gate_out.no_container', '=', 'gate_in.no_container')
        ->whereDate('gate_out.gateout_time', $today)
        ->where('gate_in.size_type', '20GP')
        ->count();
        $dataOut40ft = DB::table('gate_out')
        ->join('gate_in', 'gate_out.no_container', '=', 'gate_in.no_container')
        ->whereDate('gate_out.gateout_time', $today)
        ->where('gate_in.size_type', '40HC')
        ->count();
        
        // Monthly data
        $dataInMonth = DB::table('gate_in')->whereBetween('gatein_time', [$startOfMonth, $endOfMonth])->count();
        $dataInMonth20ft = DB::table('gate_in')
        ->whereBetween('gatein_time', [$startOfMonth, $endOfMonth])
        ->where('size_type', '20GP')
        ->count();
        $dataInMonth40ft = DB::table('gate_in')
        ->whereBetween('gatein_time', [$startOfMonth, $endOfMonth])
        ->where('size_type', '40HC')
        ->count();
        $dataOutMonth = DB::table('gate_out')->whereBetween('gateout_time', [$startOfMonth, $endOfMonth])->count();
        $dataOutMonth20ft = DB::table('gate_out')
        ->join('gate_in', 'gate_out.no_container', '=', 'gate_in.no_container')
        ->whereBetween('gatein_time', [$startOfMonth, $endOfMonth])
        ->where('size_type', '20GP')
        ->count();
        $dataOutMonth40ft = DB::table('gate_out')
        ->join('gate_in', 'gate_out.no_container', '=', 'gate_in.no_container')
        ->whereBetween('gatein_time', [$startOfMonth, $endOfMonth])
        ->where('size_type', '40HC')
        ->count();
        $dataTotalMonth = $dataInMonth + $dataOutMonth;

        // Step 1: Retrieve tarif_depo data
        $tarifDepoData = DB::table('tarif_depo')
        ->select('NAMA_JASA', 'GRUP', 'TARIF')
        ->get();

        // Step 2: Create a mapping array from tarif_depo data
        $tarifDepoMappings = $tarifDepoData->mapWithKeys(function ($item) {
        return [$item->nama_jasa => ['grup' => $item->grup, 'tarif' => $item->tarif]];
        })->toArray();

        // Step 3: Retrieve WO_DETAIL and PAYMENT data
        $rows = DB::table('PAYMENTS')
        ->join('WO_DETAIL', 'PAYMENTS.NOMOR_WO', '=', 'WO_DETAIL.NOMOR_WO')
        ->where('PAYMENTS.STATUS', 'PAID')
        ->select('WO_DETAIL.KEGIATAN')
        ->get();

        // Step 4: Initialize totals array
        $totals = [
        'LOLO' => 0,
        'WASH' => 0,
        'STORAGE' => 0
        ];

        // Step 5: Calculate totals based on mapping and tarif
        foreach ($rows as $row) {
        // Split KEGIATAN field into individual activities
        $activities = array_map('trim', explode(',', $row->kegiatan));

        foreach ($activities as $activity) {
            // Check if activity exists in tarif_depoMappings
            if (isset($tarifDepoMappings[$activity])) {
                // Get group and tariff for the activity
                $grup = $tarifDepoMappings[$activity]['grup'];
                $tariff = $tarifDepoMappings[$activity]['tarif'];

                // Add the tariff to the appropriate group total
                if (isset($totals[$grup])) {
                    $totals[$grup] += $tariff;
                }
            }
        }
        }
        // dd($activities);
        // Calculate total of all groups
        $totals['total'] = array_sum($totals);
        
        // Retrieve monthly data for chart
        $monthlyData = DB::table('gate_in')
        ->select(DB::raw('TO_CHAR(gatein_time, \'YYYY-MM\') as month'), DB::raw('COUNT(*) as count'))
        ->whereBetween('gatein_time', [$startOfMonth->subMonths(11), $endOfMonth])
        ->groupBy(DB::raw('TO_CHAR(gatein_time, \'YYYY-MM\')'))
        ->orderBy('month')
        ->get();

        // Convert data to arrays for chart
        $months = $monthlyData->pluck('month')->toArray();
        $counts = $monthlyData->pluck('count')->toArray();

        // Retrieve monthly data for chart
        $monthlyOutData = DB::table('gate_out')
        ->select(DB::raw('TO_CHAR(gateout_time, \'YYYY-MM\') as month'), DB::raw('COUNT(*) as count'))
        ->whereBetween('gateout_time', [$startOfMonth->subMonths(11), $endOfMonth])
        ->groupBy(DB::raw('TO_CHAR(gateout_time, \'YYYY-MM\')'))
        ->orderBy('month')
        ->get();

        // Convert data to arrays for chart
        $monthsout = $monthlyOutData->pluck('month')->toArray();
        $countsout = $monthlyOutData->pluck('count')->toArray();

        // Fetch EOR data with join to eor_detail
    $eorData = DB::table('EOR')
    ->join('eor_detail', 'EOR.eor_code', '=', 'eor_detail.eor_code')
    ->select('EOR.*', 'eor_detail.total_cost')
    ->get();

    // Calculate statistics
    $totalProjects = $eorData->count();
    $completedTasks = $eorData->whereNotNull('date_completed')->count();
    $remainingTasks = $totalProjects - $completedTasks;
    $totalTasks = $eorData->sum(function($item) {
        return Carbon::parse($item->date_started)->diffInDays($item->date_completed ?? now());
    });
    $totalRevenue = $eorData->sum('total_cost');

    // Prepare monthly data for chart
    $monthlyData = DB::table('EOR')
        ->join('eor_detail', 'EOR.eor_code', '=', 'eor_detail.eor_code')
        ->select(DB::raw('EXTRACT(MONTH FROM eor.date_started) as month'), DB::raw('count(*) as count'))
        ->whereRaw('EXTRACT(YEAR FROM eor.date_started) = ?', [date('Y')])
        ->groupBy(DB::raw('EXTRACT(MONTH FROM eor.date_started)'))
        ->pluck('count', 'month');

    // Fill months with zero for missing months
    $monthlyCounts = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthlyCounts[$i] = $monthlyData->get($i, 0);
    }

        return view('admin.dashboard.index', compact(
            'dataIn',
            'dataIn20ft',
            'dataIn40ft',
            'dataInMonth20ft',
            'dataInMonth40ft',
            'dataOut20ft',
            'dataOut40ft',
            'dataOutMonth20ft',
            'dataOutMonth40ft',
            'dataOut',
            'dataTotalToday',
            'dataInMonth',
            'dataOutMonth',
            'dataTotalMonth',
            'totals',
            'months',
            'monthsout',
            'counts',
            'countsout',
            'totalProjects',
            'completedTasks',
            'remainingTasks',
            'totalTasks',
            'totalRevenue',
            'monthlyCounts',
            'currentDay',
            'currentMonth',
            'currentYear',
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

    public function change()
    {
        return view('admin/dashboard/change');
    }

    public function change_password(Request $request){

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        // DB::table('users')  
        //         ->where('id', Auth::User()->id)
        //         ->update([
        //         'password' => bcrypt($request->get('new-password'))]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();

        return redirect('/dashboard')->with("success","Ganti Password Berhasil !");

    }
}
