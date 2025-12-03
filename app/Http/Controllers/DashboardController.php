<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Izin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Wali santri diarahkan ke controller khusus
        if ($user->role === 'wali_santri') {
            return redirect()->route('dashboard.walisantri');
        }

        // === ROLE KEAMANAN ===
        if ($user->role === 'keamanan') {

            $totalSantri = Santri::count();
            $totalKelas = Kelas::count();
            $totalWaliKelas = User::where('role', 'wali_kelas')->count();
            $totalWaliSantri = User::where('role', 'wali_santri')->count();

            $izinCounts = Izin::whereMonth('created_at', now()->month)
                ->selectRaw('jenis_izin, COUNT(*) as total')
                ->groupBy('jenis_izin')
                ->pluck('total', 'jenis_izin')
                ->toArray();

            $totalDendaDibayar = Izin::where('status_lapor', 'sudah_lapor')
                ->where('status_denda', 'dibayar')
                ->sum('denda');

            $totalDendaBelum = Izin::where('status_lapor', 'sudah_lapor')
                ->where('status_denda', 'belum_dibayar')
                ->sum('denda');

            $latestIzin = Izin::latest()->take(5)->get();

            return view('dashboard', compact(
                'user', 'totalSantri', 'totalKelas',
                'totalWaliKelas', 'totalWaliSantri',
                'izinCounts', 'latestIzin',
                'totalDendaDibayar', 'totalDendaBelum'
            ));
        }

        // === ROLE: WALI KELAS ===
        elseif ($user->role === 'wali_kelas') {

            $kelasIds = Kelas::where('wali_kelas_id', $user->id)->pluck('id');
            $santriIds = Santri::whereIn('kelas_id', $kelasIds)->pluck('id');

            $totalSantri = $santriIds->count();
            $totalKelas = $kelasIds->count();

            $totalWaliSantri = Santri::whereIn('kelas_id', $kelasIds)
                ->distinct('wali_santri_id')
                ->count('wali_santri_id');

            $izinCounts = Izin::whereIn('santri_id', $santriIds)
                ->whereMonth('created_at', now()->month)
                ->selectRaw('jenis_izin, COUNT(*) as total')
                ->groupBy('jenis_izin')
                ->pluck('total', 'jenis_izin')
                ->toArray();

            $latestIzin = Izin::with('santri.kelas')
                ->whereIn('santri_id', $santriIds)
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', [
                'user' => $user,
                'totalSantri' => $totalSantri,
                'totalKelas' => $totalKelas,
                'totalWaliKelas' => 1,
                'totalWaliSantri' => $totalWaliSantri,
                'izinCounts' => $izinCounts,
                'latestIzin' => $latestIzin,
                'totalDendaDibayar' => 0,
                'totalDendaBelum' => 0,
                'izinLabels' => ['Sakit','Kegiatan','Lainnya'],
            ]);
        }

    } 

} 
