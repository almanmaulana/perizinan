<?php

namespace App\Http\Controllers\Keamanan;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanIzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('santri.kelas')
            ->whereIn('status', ['pending_keamanan','disetujui_keamanan','ditolak_keamanan']);

        // Search NIS/Nama
        if ($request->filled('q')) {
            $query->whereHas('santri', fn($q) =>
                $q->where('nis','like','%'.$request->q.'%')
                  ->orWhere('nama','like','%'.$request->q.'%')
            );
        }

        // Filter jenis izin
        if ($request->filled('jenis_izin')) {
            $query->where('jenis_izin', $request->jenis_izin);
        }

        // Filter tingkat/angkatan
        if ($request->filled('tingkat')) {
            $query->whereHas('santri.kelas', fn($q) => $q->where('tingkat', $request->tingkat));
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('santri', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }

        // Filter periode
        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'minggu':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulan':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'tahun':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Clone query untuk total
        $totalQuery = clone $query;

        // Pagination
        $izinList = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Total denda
        $totalDendaDibayar = $totalQuery->get()
            ->where('status_lapor','sudah_lapor')
            ->where('status_denda','dibayar')
            ->sum('denda');

        $totalDendaBelum = $totalQuery->get()
            ->where('status_lapor','sudah_lapor')
            ->where('status_denda','belum_dibayar')
            ->sum('denda_berjalan');

        // Kelas untuk filter
        $kelasList = Kelas::whereHas('santris.izins')->get();

        return view('laporan.index', compact('izinList','kelasList','totalDendaDibayar','totalDendaBelum'));
    }

    public function exportPdf(Request $request)
    {
        $query = Izin::with('santri.kelas')
            ->whereIn('status', ['pending_keamanan','disetujui_keamanan','ditolak_keamanan']);

        if ($request->filled('q')) {
            $query->whereHas('santri', fn($q) =>
                $q->where('nis','like','%'.$request->q.'%')
                  ->orWhere('nama','like','%'.$request->q.'%')
            );
        }

        if ($request->filled('jenis_izin')) {
            $query->where('jenis_izin', $request->jenis_izin);
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('santri.kelas', fn($q) => $q->where('tingkat', $request->tingkat));
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('santri', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'minggu':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulan':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'tahun':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $izinList = $query->orderBy('updated_at', 'desc')->get();

        // Total denda untuk PDF
        $totalDendaDibayar = $izinList
            ->where('status_lapor','sudah_lapor')
            ->where('status_denda','dibayar')
            ->sum('denda');

        $totalDendaBelum = $izinList
            ->where('status_lapor','sudah_lapor')
            ->where('status_denda','belum_dibayar')
            ->sum('denda_berjalan');

        $pdf = Pdf::loadView('laporan.pdf', compact('izinList','totalDendaDibayar','totalDendaBelum'));
        return $pdf->stream('laporan-izin.pdf');
    }
}
