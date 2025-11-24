<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // =========================
    // INDEX
    // =========================
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        return view('users.create');
    }

    // =========================
    // STORE USER
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:keamanan,wali_kelas,wali_santri',
            'no_hp' => 'nullable|string|max:20',
            'kode_keluarga' => 'nullable|string|max:50|unique:users,kode_keluarga',
            'foto' => 'nullable|image|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Upload Foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('profile', 'public');
        }

        // Jika role wali_santri → generate otomatis
        if ($validated['role'] === 'wali_santri' && empty($validated['kode_keluarga'])) {
            $validated['kode_keluarga'] = strtoupper('KK-' . uniqid());
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan ✅');
    }

    // =========================
    // EDIT
    // =========================
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // =========================
    // UPDATE USER
    // =========================
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:keamanan,wali_kelas,wali_santri',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'kode_keluarga' => $user->role === 'wali_santri'
                ? 'prohibited' // ❗ wali santri tidak bisa ubah KK
                : 'nullable|string|max:50|unique:users,kode_keluarga,' . $user->id,
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama', 'email', 'role', 'no_hp', 'kode_keluarga']);

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload Foto Baru
        if ($request->hasFile('foto')) {

            // Hapus foto lama
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $data['foto'] = $request->file('foto')->store('profile', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui ✅');
    }

    // =========================
    // DELETE USER
    // =========================
    public function destroy(User $user)
    {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus 🗑');
    }

    // =========================
    // IMPORT
    // =========================
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        Excel::import(new UserImport, $request->file('file'));

        return back()->with('success', 'Data user berhasil diimport!');
    }

    // =========================
    // AUTOCOMPLETE WALI SANTRI
    // =========================
    public function searchWaliSantri(Request $request)
    {
        return User::where('role', 'wali_santri')
            ->where('nama', 'like', "%{$request->get('q')}%")
            ->limit(10)
            ->get(['id', 'nama', 'no_hp']);
    }

    // =========================
    // BULK DELETE
    // =========================
    public function bulkDelete(Request $request)
    {
        if (auth()->user()->role !== 'keamanan') {
            abort(403, 'Akses ditolak');
        }

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada user dipilih.');
        }

        User::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' user berhasil dihapus.');
    }

    // =========================
    // PROFILE PAGE
    // =========================
    public function profile()
    {
        return view('profile', ['user' => auth()->user()]);
    }

    // =========================
    // UPDATE PROFILE
    // =========================
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|max:20',
            'kode_keluarga' => $user->role === 'wali_santri'
                ? 'prohibited'
                : 'nullable|string|max:50',
        ]);

        $user->update($request->only('nama', 'email', 'no_hp', 'kode_keluarga'));

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // =========================
    // UPDATE PASSWORD
    // =========================
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    // =========================
    // UPDATE foto
    // =========================
    public function updatephoto(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('foto')) {

            // Hapus foto lama
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Upload foto baru
            $path = $request->file('foto')->store('profile', 'public');

            $user->foto = $path;
            $user->save();
        }

        return back()->with('success', 'Foto berhasil diperbarui');
    }


}
