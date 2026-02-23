<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pusat']);
    }

    // Daftar semua pengguna
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    // Form buat akun baru
    public function create()
    {
        return view('users.create');
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:bkhit,bbkhit',
            'upt_asal'  => 'nullable|string|max:255',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'upt_asal'  => $request->upt_asal,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun ' . strtoupper($request->role) . ' untuk "' . $request->name . '" berhasil dibuat!');
    }

    // Hapus akun (kecuali pusat sendiri)
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'pusat') {
            return back()->with('error', 'Akun Admin Pusat tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun "' . $name . '" berhasil dihapus.');
    }

    // ── #5: Form edit pengguna ──────────────────────────────────────────
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role'     => 'required|in:bkhit,bbkhit',
            'upt_asal' => 'nullable|string|max:255',
        ]);

        // Pusat tidak bisa diubah role-nya menjadi bukan pusat
        if ($user->role === 'pusat') {
            return back()->with('error', 'Akun Admin Pusat tidak dapat diubah rolenya.');
        }

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'upt_asal' => $request->upt_asal,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun "' . $user->name . '" berhasil diperbarui.');
    }
    // ───────────────────────────────────────────────────────────────────

    // Reset password
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password untuk "' . $user->name . '" berhasil direset.');
    }
}
