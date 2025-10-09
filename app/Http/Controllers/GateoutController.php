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

        // Ambil record ann_import yang eligible + ambil NO_BLDO
        $ann = DB::table('ann_import')
            ->select('no_container', 'no_bldo', 'status_surveyout', 'status_gateout')
            ->where('no_container', $no_container)
            ->where('status_surveyout', 'CLOSE')
            ->where('status_gateout', 'OPEN')
            ->first();

        if (!$ann) {
            return back()->with('error', 'Kontainer tidak ditemukan/eligible di ANN_IMPORT (butuh status_surveyout=CLOSE & status_gateout=OPEN).')->withInput();
        }

        if (empty($ann->no_bldo)) {
            return back()->with('error', 'NO_BLDO di ANN_IMPORT kosong. Mohon lengkapi terlebih dahulu.')->withInput();
        }

        // ❗Duplikat dicek berdasarkan pasangan (no_container, no_bldo)
        $already = DB::table('gate_out')
            ->where('no_container', $no_container)
            ->where('no_bldo', $ann->no_bldo)
            ->exists();

        if ($already) {
            return back()->with('error', 'Gate Out sudah pernah dilakukan untuk pasangan NO_CONTAINER & NO_BLDO yang sama.')->withInput();
        }

        $gateout_time = now('Asia/Jakarta')->format('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            // Insert gate_out dengan NO_BLDO dari ann_import
            DB::table('gate_out')->insert([
                'no_container' => $no_container,
                'no_bldo'      => $ann->no_bldo,
                'pic_gateout'  => $pic_gateout,
                'gateout_time' => $gateout_time,
            ]);

            // Update ANN_IMPORT hanya untuk pasangan (no_container, no_bldo) yang sama
            DB::table('ann_import')
                ->where('no_container', $no_container)
                ->where('no_bldo', $ann->no_bldo)
                ->update([
                    'status_gateout' => 'CLOSE',
                    'out_time' => $gateout_time, // aktifkan bila kolom tersedia
                ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan Gate Out: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', "Gate Out berhasil. Pasangan NO_CONTAINER & NO_BLDO disimpan dan status_gateout di ANN_IMPORT ditutup.");
    }
}
