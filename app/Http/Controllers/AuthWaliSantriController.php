<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthWaliSantriController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'kode_keluarga' => 'required',
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $santris = Santri::where('kode_keluarga', $request->kode_keluarga)->get();

        if ($santris->isEmpty()) {
            return back()->withErrors(['kode_keluarga' => 'Kode keluarga tidak ditemukan.'])->withInput();
        }

        if (User::where('kode_keluarga', $request->kode_keluarga)->exists()) {
            return back()->withErrors(['kode_keluarga' => 'Kode keluarga sudah digunakan.'])->withInput();
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'wali_santri',
            'kode_keluarga' => $request->kode_keluarga,
        ]);

        foreach ($santris as $santri) {
            $santri->update(['wali_santri_id' => $user->id]);
        }

        Auth::login($user);

        return redirect()->route('dashboard.walisantri');
    }
}
