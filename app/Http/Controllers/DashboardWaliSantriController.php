<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Izin;
use Illuminate\Http\Request;

class DashboardWaliSantriController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalSantri = Santri::where('wali_santri_id', $user->id)->count();

        $izinCounts = Izin::whereIn('santri_id', function ($q) use ($user) {
            $q->select('id')->from('santris')
              ->where('wali_santri_id', $user->id);
        })
        ->whereMonth('created_at', now()->month)
        ->selectRaw('jenis_izin, COUNT(*) as total')
        ->groupBy('jenis_izin')
        ->pluck('total', 'jenis_izin')
        ->toArray();

        $latestIzin = Izin::whereIn('santri_id', function ($q) use ($user) {
            $q->select('id')->from('santris')
              ->where('wali_santri_id', $user->id);
        })
        ->latest()
        ->take(5)
        ->get();

        return view('dashboard', [
            'user' => $user,
            'totalSantri' => $totalSantri,
            'izinCounts' => $izinCounts,
            'latestIzin' => $latestIzin,

            // Data lain kosong agar tidak error di blade
            'totalKelas' => null,
            'totalWaliKelas' => null,
            'totalWaliSantri' => null,
            'totalDendaDibayar' => 0,
            'totalDendaBelum' => 0,
        ]);
    }
}
