<?php

namespace App\Http\Controllers\WaliSantri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        // Ambil santri yang menjadi tanggung jawab wali santri
        $santriList = Auth::user()->santriAsWali()->get();

        // Query izin untuk santri tersebut
        $query = Izin::whereIn('santri_id', $santriList->pluck('id'))
                     ->with('santri')
                     ->orderBy('created_at', 'desc');

        // Filter search berdasarkan nama
        if ($request->filled('q')) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->q.'%');
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination 5 per halaman
        $izinList = $query->paginate(5)->withQueryString();

        // Flag untuk buka form create
        $isCreate = $request->has('create');

        return view('izin.walisantri.index', compact('santriList', 'izinList', 'isCreate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'jenis_izin' => 'required|in:Sakit,Kegiatan,Lainnya',
            'alasan' => 'nullable|string'
        ]);

        Izin::create([
            'santri_id' => $request->santri_id,
            'jenis_izin' => $request->jenis_izin,
            'alasan' => $request->alasan,
            'status' => 'pending_wali_kelas'
        ]);

        return redirect()->back()->with('success', 'Izin berhasil diajukan!');
    }
}
