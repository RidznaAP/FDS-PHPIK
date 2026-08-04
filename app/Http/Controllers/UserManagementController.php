<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pusat,developer']);
    }

    // Daftar semua pengguna
    public function index()
    {
        $users = User::with('coordinator')->orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    // Form buat akun baru
    public function create()
    {
        $coordinators = User::where('role', 'bbkhit')->orderBy('name')->get();
        return view('users.create', compact('coordinators'));
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $isDeveloper = auth()->user()->isDeveloper();

        // Developer bisa membuat akun pusat, pusat hanya bisa membuat bkhit/bbkhit
        $allowedRoles = $isDeveloper ? 'required|in:bkhit,bbkhit,pusat' : 'required|in:bkhit,bbkhit';

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => $allowedRoles,
            'upt_asal'  => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:users,id',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'role'           => $request->role,
            'upt_asal'       => $request->upt_asal,
            'parent_id'      => $request->parent_id,
            'password'       => Hash::make($request->password),
            'plain_password' => $request->password, // Simpan plain untuk keperluan ekspor kredensial
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun ' . strtoupper($request->role) . ' untuk "' . $request->name . '" berhasil dibuat!');
    }

    // Hapus akun
    public function destroy($id)
    {
        $user     = User::findOrFail($id);
        $authUser = auth()->user();

        // Tidak ada yang bisa menghapus sesama developer
        if ($user->isDeveloper()) {
            return back()->with('error', 'Akun Developer tidak dapat dihapus.');
        }

        // Pusat tidak bisa dihapus kecuali oleh Developer
        if ($user->isPusat() && !$authUser->isDeveloper()) {
            return back()->with('error', 'Akun Admin Pusat tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun "' . $name . '" berhasil dihapus.');
    }

    // ── Form edit pengguna ──────────────────────────────────────────
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Developer tidak bisa diedit dari UI (keamanan)
        if ($user->isDeveloper()) {
            return back()->with('error', 'Akun Developer tidak dapat diedit dari halaman ini.');
        }

        $coordinators = User::where('role', 'bbkhit')->orderBy('name')->get();
        return view('users.edit', compact('user', 'coordinators'));
    }

    public function update(Request $request, $id)
    {
        $user     = User::findOrFail($id);
        $authUser = auth()->user();

        // Developer tidak bisa diedit
        if ($user->isDeveloper()) {
            return back()->with('error', 'Akun Developer tidak dapat diedit.');
        }

        $isDeveloper = $authUser->isDeveloper();
        // Developer bisa ubah role ke pusat, pusat hanya bisa ke bkhit/bbkhit
        $allowedRoles = $isDeveloper ? 'required|in:bkhit,bbkhit,pusat' : 'required|in:bkhit,bbkhit';

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'role'      => $allowedRoles,
            'upt_asal'  => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        // Pusat tidak bisa diubah role-nya kecuali oleh Developer
        if ($user->isPusat() && !$isDeveloper) {
            return back()->with('error', 'Akun Admin Pusat tidak dapat diubah rolenya.');
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'upt_asal'  => $request->upt_asal,
            'parent_id' => $request->parent_id,
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

        $user     = User::findOrFail($id);
        $authUser = auth()->user();

        // Developer tidak bisa di-reset passwordnya dari UI (keamanan)
        if ($user->isDeveloper() && !$authUser->isDeveloper()) {
            return back()->with('error', 'Password Developer tidak dapat direset dari halaman ini.');
        }

        $user->update([
            'password'       => Hash::make($request->password),
            'plain_password' => $request->password, // Perbarui plain_password saat reset
        ]);

        return back()->with('success', 'Password untuk "' . $user->name . '" berhasil direset.');
    }

    // ── Export Kredensial ke Excel ──────────────────────────────────────
    public function export(Request $request)
    {
        $role     = $request->get('role'); // opsional: filter per role
        $filename = 'Kredensial_Pengguna_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new UsersExport($role), $filename);
    }
    // ───────────────────────────────────────────────────────────────────
}
