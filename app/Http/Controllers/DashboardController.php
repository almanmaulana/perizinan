<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ===== ROLE KEAMANAN =====
        if ($user->role === 'keamanan') {
            $totalSantri = Santri::count();
            $totalKelas = Kelas::count();
            $totalWaliKelas = User::where('role','wali_kelas')->count();
            $totalWaliSantri = User::where('role','wali_santri')->count();

            $izinLabels = ['Sakit','Kegiatan','Lainnya'];
            $izinCounts = Izin::select('jenis_izin')
                            ->selectRaw('count(*) as total')
                            ->whereMonth('created_at', now()->month)
                            ->groupBy('jenis_izin')
                            ->pluck('total','jenis_izin')
                            ->toArray();
            $izinCounts = array_merge(array_fill_keys($izinLabels,0), $izinCounts);

            $totalDendaDibayar = Izin::where('status_lapor','sudah_lapor')
                                ->where('status_denda','dibayar')
                                ->sum('denda');

            $totalDendaBelum = Izin::where('status_lapor','sudah_lapor')
                                ->where('status_denda','belum_dibayar')
                                ->sum('denda');

            $latestIzin = Izin::with('santri.kelas')
                            ->latest()
                            ->take(5)
                            ->get();

        // ===== ROLE WALI KELAS =====
        } elseif ($user->role === 'wali_kelas') {
            $kelasIds = Kelas::where('wali_kelas_id', $user->id)->pluck('id');
            $santriIds = Santri::whereIn('kelas_id', $kelasIds)->pluck('id');

            $totalSantri = $santriIds->count();
            $totalKelas = $kelasIds->count();
            $totalWaliKelas = 1; // diri sendiri
            $totalWaliSantri = Santri::whereIn('kelas_id', $kelasIds)
                              ->distinct('wali_santri_id')
                              ->count('wali_santri_id');

            $izinLabels = ['Sakit','Kegiatan','Lainnya'];
            $izinCounts = Izin::whereIn('santri_id', $santriIds)
                            ->whereMonth('created_at', now()->month)
                            ->select('jenis_izin')
                            ->selectRaw('count(*) as total')
                            ->groupBy('jenis_izin')
                            ->pluck('total','jenis_izin')
                            ->toArray();
            $izinCounts = array_merge(array_fill_keys($izinLabels,0), $izinCounts);

            $totalDendaDibayar = Izin::whereIn('santri_id', $santriIds)
                                ->where('status_lapor','sudah_lapor')
                                ->where('status_denda','dibayar')
                                ->sum('denda');

            $totalDendaBelum = Izin::whereIn('santri_id', $santriIds)
                                ->where('status_lapor','sudah_lapor')
                                ->where('status_denda','belum_dibayar')
                                ->sum('denda');

            $latestIzin = Izin::with('santri.kelas')
                            ->whereIn('santri_id', $santriIds)
                            ->latest()
                            ->take(5)
                            ->get();

        // ===== ROLE WALI SANTRI =====
        } elseif ($user->role === 'wali_santri') {
            $santriIds = Santri::where('wali_santri_id', $user->id)->pluck('id');

            $totalSantri = $santriIds->count();
            $totalKelas = 0;
            $totalWaliKelas = 0;
            $totalWaliSantri = 1; // diri sendiri

            $izinLabels = ['Sakit','Kegiatan','Lainnya'];
            $izinCounts = Izin::whereIn('santri_id', $santriIds)
                            ->whereMonth('created_at', now()->month)
                            ->select('jenis_izin')
                            ->selectRaw('count(*) as total')
                            ->groupBy('jenis_izin')
                            ->pluck('total','jenis_izin')
                            ->toArray();
            $izinCounts = array_merge(array_fill_keys($izinLabels,0), $izinCounts);

            $totalDendaDibayar = Izin::whereIn('santri_id', $santriIds)
                                ->where('status_lapor','sudah_lapor')
                                ->where('status_denda','dibayar')
                                ->sum('denda');

            $totalDendaBelum = Izin::whereIn('santri_id', $santriIds)
                                ->where('status_lapor','sudah_lapor')
                                ->where('status_denda','belum_dibayar')
                                ->sum('denda');

            $latestIzin = Izin::with('santri.kelas')
                            ->whereIn('santri_id', $santriIds)
                            ->latest()
                            ->take(5)
                            ->get();
        }

        return view('dashboard', compact(
            'totalSantri','totalKelas','totalWaliKelas','totalWaliSantri',
            'izinLabels','izinCounts','totalDendaDibayar','totalDendaBelum','user','latestIzin'
        ));
    }
}
