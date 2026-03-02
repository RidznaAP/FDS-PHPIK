<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use Illuminate\Support\Facades\Auth;

use App\Models\MediaPembawa;
use App\Models\JenisPenyakit;
use App\Models\User;
use App\Exports\PerencanaanExport;
use App\Imports\PerencanaanImport;
use Maatwebsite\Excel\Facades\Excel;

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
        $user = Auth::user();
        $query = Perencanaan::with(['evaluasi', 'pelaksanaans', 'user']);

        // ── Sorting ──────────────────────────────────────────────────────
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'created_at', 'provinsi', 'kab_kota', 'jenis_mp', 'jenis_hpik', 'target_uji', 'status'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // ── Auth-scoped Filtering ──────────────────────────────────────
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

        // Ambil daftar tahun yang tersedia untuk dropdown (Filtered by role)
        $yearQuery = Perencanaan::selectRaw('YEAR(created_at) as tahun');
        if ($user->isBkhit()) {
            $yearQuery->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $yearQuery->whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }
        $years = $yearQuery->groupBy('tahun')->orderByDesc('tahun')->pluck('tahun');

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
        $user = Auth::user();
        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        // Force provinsi based on user info if not PUSAT
        $provinsi = $user->isPusat() ? $request->provinsi : ($user->upt_asal ?? $request->provinsi);

        Perencanaan::create([
            'user_id'                 => Auth::id(),
            'provinsi'                => $provinsi,
            'kab_kota'                => $request->kab_kota,
            'jenis_mp'                => $request->jenis_mp,
            'jenis_hpik'              => $request->jenis_hpik,
            'kemampuan_uji_upt'       => $request->kemampuan_uji_upt,
            'metode_pengujian'        => $request->metode_pengujian,
            'lab_uji'                 => $request->lab_uji,
            'target_uji'              => $request->target_uji,
            'tw1'                     => $request->tw1,
            'tw2'                     => $request->tw2,
            'tw3'                     => $request->tw3,
            'tw4'                     => $request->tw4,
            'total_pengujian'         => $total_tw,
            'rencana_lokasi'          => $request->rencana_lokasi,
            'rencana_jumlah_sampel'   => $request->rencana_jumlah_sampel ?? 0,
            'rencana_metode_sampling' => $request->rencana_metode_sampling,
            'status'                  => 'draft',
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
        $user = Auth::user();

        if ($perencanaan->status !== 'draft' || $perencanaan->user_id !== Auth::id()) {
            return redirect()->route('perencanaan.index')
                ->with('error', 'Tidak diizinkan mengubah data ini.');
        }

        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        // Force provinsi based on user info if not PUSAT
        $provinsi = $user->isPusat() ? $request->provinsi : ($user->upt_asal ?? $perencanaan->provinsi);

        $perencanaan->update([
            'provinsi'                => $provinsi,
            'kab_kota'                => $request->kab_kota,
            'jenis_mp'                => $request->jenis_mp,
            'jenis_hpik'              => $request->jenis_hpik,
            'kemampuan_uji_upt'       => $request->kemampuan_uji_upt,
            'metode_pengujian'        => $request->metode_pengujian,
            'lab_uji'                 => $request->lab_uji,
            'target_uji'              => $request->target_uji,
            'tw1'                     => $request->tw1,
            'tw2'                     => $request->tw2,
            'tw3'                     => $request->tw3,
            'tw4'                     => $request->tw4,
            'total_pengujian'         => $total_tw,
            'rencana_lokasi'          => $request->rencana_lokasi,
            'rencana_jumlah_sampel'   => $request->rencana_jumlah_sampel ?? 0,
            'rencana_metode_sampling' => $request->rencana_metode_sampling,
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil diperbarui!');
    }
    // ─────────────────────────────────────────────────────────────────────

    // ── #4: Hapus Perencanaan (hanya Draft, hanya milik sendiri) ─────────
    public function destroy($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);

        // Jika bukan Pusat, hanya boleh hapus milik sendiri yang masih Draft
        if (!Auth::user()->isPusat()) {
            if ($perencanaan->status !== 'draft' || $perencanaan->user_id !== Auth::id()) {
                return redirect()->route('perencanaan.index')
                    ->with('error', 'Perencanaan tidak dapat dihapus (bukan Draft atau bukan milik Anda).');
            }
        }

        $perencanaan->delete();
        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil dihapus.');
    }
    // ─────────────────────────────────────────────────────────────────────

    // ─────────────────────────────────────────────────────────────────────
    // #6 Export & Import
    // ─────────────────────────────────────────────────────────────────────

    public function export()
    {
        return Excel::download(new PerencanaanExport(), 'perencanaan_hpik_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PerencanaanExport(true), 'template_import_perencanaan.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new PerencanaanImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Perencanaan berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $query = Perencanaan::whereIn('id', $ids);

        // Jika bukan Pusat, hanya boleh hapus milik sendiri yang masih Draft
        if (!Auth::user()->isPusat()) {
            $query->where('user_id', Auth::id())
                  ->where('status', 'draft');
        }

        $count = $query->delete();
        
        if ($count == 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus (Pastikan status masih Draft).');
        }

        return back()->with('success', $count . ' data perencanaan berhasil dihapus.');
    }

    // BKHIT mengajukan validasi: draft -> waiting
    public function submit($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        
        // Pastikan hanya pemilik yang bisa submit draft miliknya
        if ($perencanaan->user_id !== Auth::id()) {
            abort(403);
        }

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
            $owner = User::find($perencanaan->user_id);
            if ($perencanaan->user_id !== $user->id && (!$owner || $owner->parent_id !== $user->id)) {
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