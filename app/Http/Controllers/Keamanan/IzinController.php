<?php
namespace App\Http\Controllers\Keamanan;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('santri.kelas')
            ->whereIn('status',['pending_keamanan','disetujui_keamanan','ditolak_keamanan'])
            ->orderBy('created_at', 'desc');

        // Filter search
        if ($request->filled('q')) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('nis', 'like', '%'.$request->q.'%')
                  ->orWhere('nama', 'like', '%'.$request->q.'%');
            });
        }

        // Filter status izin
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter status denda
        if ($request->filled('status_denda')) {
            $query->where('status_denda', $request->status_denda);
        }

        // Filter tanggal simulasi jika ada
        if ($request->filled('simulasi')) {
            $query->whereDate('created_at', $request->simulasi);
        }

        // Pagination 5 per halaman
        $izinList = $query->paginate(5)->withQueryString();

        return view('izin.keamanan.index', compact('izinList'));
    }

    public function approve(Request $request, Izin $izin)
    {
        $request->validate([
            'tgl_mulai_disetujui'=>'required|date',
            'tgl_selesai_disetujui'=>'required|date|after_or_equal:tgl_mulai_disetujui'
        ]);

        $izin->update([
            'status'=>'disetujui_keamanan',
            'tgl_mulai_disetujui'=>$request->tgl_mulai_disetujui,
            'tgl_selesai_disetujui'=>$request->tgl_selesai_disetujui,
            'status_lapor'=>'belum_lapor',
            'denda'=>0,
            'status_denda'=>'belum_dibayar'
        ]);

        return back()->with('success','Izin disetujui keamanan.');
    }

    public function reject(Request $request, Izin $izin)
    {
        $request->validate(['catatan'=>'required|string']);
        $izin->update([
            'status'=>'ditolak_keamanan',
            'catatan'=>$request->catatan,
            'status_lapor'=>null,
            'denda'=>0,
            'status_denda'=>null
        ]);

        return back()->with('success','Izin ditolak keamanan.');
    }

    public function lapor(Izin $izin)
    {
        if($izin->status != 'disetujui_keamanan') return back()->with('error','Izin belum disetujui.');
        if($izin->status_lapor=='sudah_lapor') return back()->with('error','Sudah lapor.');

        $izin->kunciDenda();
        $izin->update(['status_lapor'=>'sudah_lapor']);

        return back()->with('success','Santri telah lapor.');
    }

    public function bayarDenda(Izin $izin)
    {
        if($izin->status_lapor!='sudah_lapor') return back()->with('error','Belum lapor.');
        if($izin->status_denda=='dibayar') return back()->with('error','Denda sudah dibayar.');

        $izin->update([
            'status_denda'=>'dibayar',
            'tanggal_dibayar'=>now()
        ]);

        return back()->with('success','Denda dibayar.');
    }

        public function softDelete(Izin $izin)
    {
        $izin->delete(); // soft delete
        return back()->with('success', 'Data izin berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $izin = Izin::withTrashed()->findOrFail($id);
        $izin->restore(); // kembalikan data
        return back()->with('success', 'Data izin berhasil dikembalikan.');
    }

    public function forceDelete($id)
    {
        $izin = Izin::withTrashed()->findOrFail($id);
        $izin->forceDelete(); // hapus permanen
        return back()->with('success', 'Data izin dihapus permanen.');
    }

    public function trash(Request $request)
{
    // Ambil semua data yang sudah dihapus (soft deleted)
    $query = Izin::onlyTrashed()->with('santri.kelas')->orderBy('deleted_at', 'desc');

    // Filter search (optional)
    if ($request->filled('q')) {
        $query->whereHas('santri', function($q) use ($request) {
            $q->where('nis', 'like', '%'.$request->q.'%')
              ->orWhere('nama', 'like', '%'.$request->q.'%');
        });
    }

    // Pagination 5 per halaman
    $izinList = $query->paginate(5)->withQueryString();

    return view('izin.keamanan.trash', compact('izinList'));
}

    public function bulkSoftDelete(Request $request)
    {
        $ids = $request->selected ?? [];

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        Izin::whereIn('id', $ids)->delete();

        return back()->with('success', 'Berhasil menghapus beberapa data.');
    }





}
