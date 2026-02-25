<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use Illuminate\Support\Facades\Auth;

use App\Models\MediaPembawa;
use App\Models\JenisPenyakit;

class PerencanaanController extends Controller
{
    // Menampilkan Form Tambah
    public function create()
    {
        $mediaPembawas  = MediaPembawa::aktif()->orderBy('nama')->get();
        $jenisPenyakits = JenisPenyakit::aktif()->orderBy('nama')->get();
        return view('perencanaan.create', compact('mediaPembawas', 'jenisPenyakits'));
    }

    // Daftar Perencanaan (dengan search, filter status & tahun)
    public function index(Request $request)
    {
        $query = Perencanaan::with(['evaluasi', 'pelaksanaans', 'user'])->latest();

        // ── Auth-scoped Filtering ──────────────────────────────────────
        $user = Auth::user();
        if ($user->isBkhit()) {
            // BKHIT hanya lihat miliknya sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            // BBKHIT lihat miliknya sendiri + milik BKHIT di bawah koordinasinya
            $query->whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')
                  ->where('id', $user->id)
                  ->orWhere('parent_id', $user->id);
            });
        }
        // PUSAT tidak difilter (lihat semua)

        // ── #8: Filter Tahun ─────────────────────────────────────────────
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('provinsi', 'like', "%{$search}%")
                  ->orWhere('kab_kota', 'like', "%{$search}%")
                  ->orWhere('jenis_mp', 'like', "%{$search}%")
                  ->orWhere('jenis_hpik', 'like', "%{$search}%");
            });
        }

        // Ambil daftar tahun yang tersedia untuk dropdown
        $years = Perencanaan::selectRaw('YEAR(created_at) as tahun')
            ->when(Auth::user()->isBkhit(), fn($q) => $q->where('user_id', Auth::id()))
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $perencanaans = $query->paginate(15)->withQueryString();
        return view('perencanaan.index', compact('perencanaans', 'years'));
    }

    // ── Show Detail Perencanaan ───────────────────────────────────────────
    public function show($id)
    {
        $p = Perencanaan::with(['user', 'pelaksanaans.laboratorium', 'evaluasi'])->findOrFail($id);
        $user = Auth::user();

        // BKHIT/BBKHIT Scoped Access
        if ($user->isBkhit() && $p->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        } 
        
        if ($user->isBbkhit()) {
            // BBKHIT boleh akses data sendiri atau data unit di bawahnya
            $owner = \App\Models\User::find($p->user_id);
            if ($p->user_id !== $user->id && $owner->parent_id !== $user->id) {
                abort(403, 'Data ini berada di luar wilayah koordinasi Anda.');
            }
        }

        return view('perencanaan.show', compact('p'));
    }

    // Menyimpan Data ke Database
    public function store(Request $request)
    {
        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        Perencanaan::create([
            'user_id'           => Auth::id(),   // ── #2: simpan user_id ──
            'provinsi'          => $request->provinsi,
            'kab_kota'          => $request->kab_kota,
            'jenis_mp'          => $request->jenis_mp,
            'jenis_hpik'        => $request->jenis_hpik,
            'kemampuan_uji_upt' => $request->kemampuan_uji_upt,
            'metode_pengujian'  => $request->metode_pengujian,
            'lab_uji'           => $request->lab_uji,
            'target_uji'        => $request->target_uji,
            'tw1'               => $request->tw1,
            'tw2'               => $request->tw2,
            'tw3'               => $request->tw3,
            'tw4'               => $request->tw4,
            'total_pengujian'   => $total_tw,
            'status'            => 'draft',
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // ── #3: Edit Perencanaan (hanya Draft, hanya milik sendiri) ──────────
    public function edit($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);

        // Hanya boleh edit jika masih draft & milik user ini
        if ($perencanaan->status !== 'draft' || $perencanaan->user_id !== Auth::id()) {
            return redirect()->route('perencanaan.index')
                ->with('error', 'Perencanaan tidak dapat diedit (bukan Draft atau bukan milik Anda).');
        }

        return view('perencanaan.edit', compact('perencanaan'));
    }

    public function update(Request $request, $id)
    {
        $perencanaan = Perencanaan::findOrFail($id);

        if ($perencanaan->status !== 'draft' || $perencanaan->user_id !== Auth::id()) {
            return redirect()->route('perencanaan.index')
                ->with('error', 'Tidak diizinkan mengubah data ini.');
        }

        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        $perencanaan->update([
            'provinsi'          => $request->provinsi,
            'kab_kota'          => $request->kab_kota,
            'jenis_mp'          => $request->jenis_mp,
            'jenis_hpik'        => $request->jenis_hpik,
            'kemampuan_uji_upt' => $request->kemampuan_uji_upt,
            'metode_pengujian'  => $request->metode_pengujian,
            'lab_uji'           => $request->lab_uji,
            'target_uji'        => $request->target_uji,
            'tw1'               => $request->tw1,
            'tw2'               => $request->tw2,
            'tw3'               => $request->tw3,
            'tw4'               => $request->tw4,
            'total_pengujian'   => $total_tw,
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil diperbarui!');
    }
    // ─────────────────────────────────────────────────────────────────────

    // ── #4: Hapus Perencanaan (hanya Draft, hanya milik sendiri) ─────────
    public function destroy($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);

        if ($perencanaan->status !== 'draft' || $perencanaan->user_id !== Auth::id()) {
            return redirect()->route('perencanaan.index')
                ->with('error', 'Perencanaan tidak dapat dihapus (bukan Draft atau bukan milik Anda).');
        }

        $perencanaan->delete();
        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil dihapus.');
    }
    // ─────────────────────────────────────────────────────────────────────

    // BKHIT mengajukan validasi: draft -> waiting
    public function submit($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        if ($perencanaan->status === 'draft') {
            $perencanaan->update(['status' => 'waiting']);
            return back()->with('success', 'Perencanaan berhasil diajukan untuk validasi.');
        }
        return back()->with('error', 'Status tidak valid untuk diajukan.');
    }

    // BBKHIT/Pusat menyetujui perencanaan: waiting -> approved
    public function approve($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        $user = Auth::user();

        // BBKHIT hanya bisa approve data di wilayahnya
        if ($user->isBbkhit()) {
            $owner = \App\Models\User::find($perencanaan->user_id);
            if ($perencanaan->user_id !== $user->id && $owner->parent_id !== $user->id) {
                abort(403, 'Anda tidak memiliki wewenang memvalidasi data di luar wilayah koordinasi Anda.');
            }
        }

        if ($perencanaan->status === 'waiting') {
            $perencanaan->update(['status' => 'approved']);
            return back()->with('success', 'Perencanaan telah disetujui!');
        }
        return back()->with('error', 'Hanya perencanaan dengan status waiting yang bisa disetujui.');
    }
}