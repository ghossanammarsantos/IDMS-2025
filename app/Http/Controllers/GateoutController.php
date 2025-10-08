<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GateoutController extends Controller
{
    public function index()
    {
        // Tabel list gate out yang sudah terjadi (tampilkan di data table)
        $gateout_list = DB::table('gate_out')
            ->orderByDesc('gateout_time')
            ->get();

        return view('admin.gateout.index', compact('gateout_list'));
    }

    /**
     * Endpoint Select2: ambil kontainer dari ann_import
     * Filter: status_surveyout = CLOSE dan status_gateout = OPEN
     * Optional search: q
     * Pagination: page (default 1), perPage 20
     */
    public function select2Containers(Request $request)
    {
        $q       = trim($request->get('q', ''));
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        $base = DB::table('ann_import')
            ->select('no_container', 'size_type', 'no_bldo')
            ->where('status_surveyout', 'CLOSE')
            ->where('status_gateout', 'OPEN');

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $like = "%{$q}%";
                $w->where('no_container', 'LIKE', $like)
                    ->orWhere('no_bldo', 'LIKE', $like)
                    ->orWhere('size_type', 'LIKE', $like);
            });
        }

        $total = $base->count();

        $rows = $base
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $items = $rows->map(function ($r) {
            $label = $r->no_container;
            // Tambahkan info pendukung biar operator mudah cek
            if (!empty($r->size_type) || !empty($r->no_bldo)) {
                $label .= ' — ' . trim(($r->size_type ?? '') . ' ' . ($r->no_bldo ?? ''));
            }
            return [
                'id'   => $r->no_container,
                'text' => $label,
            ];
        });

        $more = ($page * $perPage) < $total;

        return response()->json([
            'items' => $items,
            'more'  => $more,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_container' => 'required|string',
        ]);

        $no_container = $validated['no_container'];
        $pic_gateout  = Auth::user()->name ?? 'SYSTEM';

        // Pastikan kontainer memang eligible: CLOSE (surveyout) & OPEN (gateout)
        $eligible = DB::table('ann_import')
            ->where('no_container', $no_container)
            ->where('status_surveyout', 'CLOSE')
            ->where('status_gateout', 'OPEN')
            ->exists();

        if (!$eligible) {
            return back()->with('error', 'Kontainer tidak eligible untuk Gate Out (cek status_surveyout/status_gateout).')->withInput();
        }

        // (Opsional) Pastikan sudah dibayar — kalau tidak dipakai, silakan dihapus blok ini
        // $isPaid = DB::table('wo_detail')
        //     ->join('payments', 'wo_detail.nomor_wo', '=', 'payments.nomor_wo')
        //     ->where('wo_detail.no_container', $no_container)
        //     ->where('payments.status', 'PAID')
        //     ->exists();

        // if (!$isPaid) {
        //     return back()->with('error', 'Nomor kontainer belum dibayar.')->withInput();
        // }


        $gateout_time = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            DB::table('gate_out')->insert([
                'no_container' => $no_container,
                'pic_gateout'  => $pic_gateout,
                'gateout_time' => $gateout_time,
                'created_by'   => Auth::user()->name ?? null, // jika ingin jejak user
            ]);

            // Update ann_import: set gateout CLOSE
            DB::table('ann_import')
                ->where('no_container', $no_container)
                ->update([
                    'status_gateout' => 'CLOSE',
                    // Jika kolom waktu gateout ada di ann_import, boleh aktifkan:
                    // 'gateout_time' => $gateout_time,
                ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan Gate Out: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Gate Out berhasil ditambahkan.');
    }
}
