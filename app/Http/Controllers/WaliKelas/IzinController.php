<?php

namespace App\Http\Controllers\Walikelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('santri.kelas')
            ->whereIn('status', [
                'pending_wali_kelas',
                'pending_keamanan',
                'disetujui_keamanan',
                'ditolak_wali_kelas',
                'ditolak_keamanan'
            ])
            ->orderBy('updated_at', 'desc');

        // Filter search NIS / Nama
        if ($request->filled('q')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('nis', 'like', '%'.$request->q.'%')
                  ->orWhere('nama', 'like', '%'.$request->q.'%');
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination 10 per halaman
        $izinList = $query->paginate(10)->withQueryString();

        return view('izin.walikelas.index', compact('izinList'));
    }

    public function approve(Izin $izin)
    {
        $izin->update([
            'status' => 'pending_keamanan',
            'catatan' => null,
        ]);

        return back()->with('success', 'Izin disetujui, menunggu keamanan.');
    }

    public function reject(Request $request, Izin $izin)
    {
        $request->validate([
            'catatan' => 'required|string|max:255'
        ]);

        $izin->update([
            'status' => 'ditolak_wali_kelas',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Izin ditolak.');
    }
}
