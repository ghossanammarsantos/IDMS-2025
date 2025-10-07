<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GateinController extends Controller
{
    public function index()
    {
        $gatein_list = DB::table('gate_in')->get();
        $ann_import = DB::table('ann_import')
            ->where(function ($query) {
                $query->whereNull('status_gatein')
                    ->orWhere('status_gatein', '');
            })
            ->get();


        return view('admin/gatein/index', compact(
            'gatein_list',
            'ann_import',
        ));
    }

    public function select2Containers(Request $request)
    {
        $q    = trim($request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        $base = DB::table('ann_import')
            ->select('no_container', 'no_bldo')
            ->where(function ($w) {
                $w->whereNull('status_gatein')
                    ->orWhere('status_gatein', '');
            });

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('no_container', 'like', "%{$q}%")
                    ->orWhere('no_bldo', 'like', "%{$q}%");
            });
        }

        $total = (clone $base)->count();

        $rows = $base
            ->orderBy('no_container')
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'items' => $rows->map(function ($r) {
                return [
                    'id'   => $r->no_container,
                    'text' => "{$r->no_container} - {$r->no_bldo}",
                ];
            })->values(),
            'more' => ($page * $perPage) < $total,
        ]);
    }

    public function store(Request $request)
    {
        $no_container = $request->input('no_container');
        $data_container = DB::table('ann_import')
            ->selectRaw('ROWIDTOCHAR(rowid) as "rowid", no_container, jenis_container, size_type, no_bldo')
            ->where('no_container', $no_container)
            ->get();

        if ($data_container->isEmpty()) {
            return redirect()->back()->with('error', 'Nomor kontainer tidak ditemukan.');
        }

        $pic_gatein = Auth::user()->name;
        $gatein_time = Carbon::now()->setTimezone('Asia/Jakarta');

        $success = false;
        foreach ($data_container as $row) {
            $is_check = DB::table('gate_in')
                ->where('no_container', $no_container)
                ->get();

            if ($is_check->isEmpty() || ($is_check[0]->no_bldo !== $row->no_bldo)) {
                $success = DB::table('gate_in')->insert([
                    'no_container' => $no_container,
                    'jenis_container' => $row->jenis_container,
                    'size_type' => $row->size_type,
                    'no_bldo' => $row->no_bldo,
                    'gatein_time' => $gatein_time,
                    'pic_gatein' => $pic_gatein,
                ]);
            }
        }

        if ($success) {
            $in_time = Carbon::now()->setTimezone('Asia/Jakarta');
            DB::table('ann_import')
                ->where('no_container', $no_container)
                ->update([
                    'status_gatein' => 'IN',
                    'in_time' => $in_time
                ]);

            return redirect()->route('gatein.index')->with('success', 'Data Gate In berhasil disimpan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data Gate In.');
        }
    }

    public function update(Request $request, $id)
    {
        // Jika PIC harus selalu user login & field disabled di form:
        $pic_gatein  = Auth::user()->name;

        $validated = $request->validate([
            'gatein_time' => ['required', 'date'],
            // Jika mengizinkan ubah PIC dari input, tambahkan 'pic_gatein' validasi
        ]);

        // Normalisasi timezone Asia/Jakarta ke format DB
        $gatein_time = Carbon::parse($validated['gatein_time'], 'Asia/Jakarta');

        $affected = DB::table('gate_in')
            ->where('id', $id)
            ->update([
                'gatein_time' => $gatein_time,
                'pic_gatein'  => $pic_gatein, // atau $request->input('pic_gatein')
                'updated_at'  => now(),
            ]);

        return redirect()->route('gatein.index')
            ->with($affected ? 'success' : 'error', $affected ? 'Data berhasil diperbarui.' : 'Tidak ada perubahan.');
    }

    public function destroy($id)
    {
        $deleted = DB::table('gate_in')->where('id', $id)->delete();

        return redirect()->route('gatein.index')
            ->with($deleted ? 'success' : 'error', $deleted ? 'Data berhasil dihapus.' : 'Gagal menghapus data.');
    }
}
