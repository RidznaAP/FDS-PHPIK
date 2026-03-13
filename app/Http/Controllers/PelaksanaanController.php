<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;

class PelaksanaanController extends Controller
{
    // Menampilkan daftar pelaksanaan (dengan search, filter status, & tahun)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium']);

        // ── Sorting ──────────────────────────────────────────────────────
        $sortBy = $request->get('sort_by', 'tanggal_pemantauan');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'tanggal_pemantauan', 'lokasi_pengambilan_sampel', 'jenis_ikan', 'jumlah_sampel', 'created_at'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // ── Auth-scoped Filtering ──────────────────────────────────────
        if ($user->isBkhit()) {
            // BKHIT hanya lihat miliknya sendiri
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            // BBKHIT lihat miliknya sendiri + milik BKHIT di bawah koordinasinya
            $query->whereHas('perencanaan', function($q) use ($user) {
                $q->whereIn('user_id', function($rq) use ($user) {
                    $rq->select('id')->from('users')
                      ->where('id', $user->id)
                      ->orWhere('parent_id', $user->id);
                });
            });
        }
        // PUSAT tidak difilter (lihat semua)

        // ── #8: Filter Tahun ─────────────────────────────────────────────
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pemantauan', $request->tahun);
        }

        // Filter status lab
        if ($request->filled('lab')) {
            if ($request->lab === 'done') {
                $query->whereHas('laboratorium');
            } elseif ($request->lab === 'pending') {
                $query->whereDoesntHave('laboratorium');
            }
        }

        // Search keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('lokasi_pengambilan_sampel', 'like', "%{$search}%")
                  ->orWhere('jenis_ikan', 'like', "%{$search}%")
                  ->orWhereHas('perencanaan', function ($rq) use ($search) {
                      $rq->where('provinsi', 'like', "%{$search}%")
                        ->orWhere('kab_kota', 'like', "%{$search}%")
                        ->orWhere('jenis_mp', 'like', "%{$search}%");
                  });
            });
        }

        // Tahun yang tersedia (dari tanggal_pemantauan, fallback ke created_at)
        $yearQuery = clone $query;
        // Hapus eager loads & orderings dari clone agar tidak error saat count/group
        $yearQuery->setEagerLoads([]);
        $yearQuery->orders = null; 
        
        $years = $yearQuery->selectRaw('YEAR(COALESCE(tanggal_pemantauan, created_at)) as tahun')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter();

        $pelaksanaans = $query->paginate(15)->withQueryString();
        return view('pelaksanaan.index', compact('pelaksanaans', 'years'));
    }

    // Form untuk mengisi pelaksanaan berdasarkan ID Perencanaan
    public function create($id)
    {
        $user = Auth::user();
        $rencana = Perencanaan::with('user')->findOrFail($id);

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($rencana->user_id !== $user->id && (!$rencana->user || $rencana->user->parent_id !== $user->id) && !$user->isPusat()) {
            abort(403, 'Akses ditolak.');
        }

        if ($rencana->pelaksanaans()->count() >= $rencana->target_uji) {
            return redirect()->route('perencanaan.show', $id)->with('error', 'Target uji sudah terpenuhi. Tidak dapat menambahkan pelaksanaan baru.');
        }

        return view('pelaksanaan.create', compact('rencana'));
    }

    // ── Show Detail Pelaksanaan ───────────────────────────────────────────
    public function show($id)
    {
        $user = Auth::user();
        $item = Pelaksanaan::with(['perencanaan.user', 'perencanaan.evaluasi', 'laboratorium'])->findOrFail($id);

        // ── Auth-scoped Detail Check ──────────────────────────────────
        if ($user->isBkhit() && $item->perencanaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($user->isBbkhit()) {
            $owner = $item->perencanaan->user;
            if ($item->perencanaan->user_id !== $user->id && ($owner && $owner->parent_id !== $user->id)) {
                abort(403, 'Data ini berada di luar wilayah koordinasi Anda.');
            }
        }

        return view('pelaksanaan.show', compact('item'));
    }

    // Simpan data lapangan
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_id'           => 'required|exists:perencanaans,id',
            'lokasi_pengambilan_sampel'=> 'required|string',
            'tanggal_pemantauan'       => 'required|date',
            'jenis_ikan'               => 'required|string',
            'jumlah_sampel'            => 'required|integer|min:1',
            'metode_pengambilan_sampel'=> 'required|string',
            'jumlah_kematian'          => 'nullable|integer|min:0',
            'panjang_cm'               => 'nullable|numeric|min:0',
            'berat_gram'               => 'nullable|numeric|min:0',
            'padat_tebar'              => 'nullable|integer|min:0',
            'latitude'                 => 'nullable|numeric',
            'longitude'                => 'nullable|numeric',
            'pengambil_sampel'         => 'nullable|array',
            'pengambil_sampel.*'       => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $rencana = Perencanaan::with('user')->findOrFail($request->perencanaan_id);

        if ($rencana->user_id !== $user->id && (!$rencana->user || $rencana->user->parent_id !== $user->id) && !$user->isPusat()) {
            abort(403, 'Akses ditolak.');
        }

        // ── Target Uji Validation ──────────────────────────────────
        if ($rencana->pelaksanaans()->count() >= $rencana->target_uji) {
            return back()->withInput()->with('error', 'Target uji sudah terpenuhi. Tidak dapat menambahkan pelaksanaan baru.');
        }

        // Bersihkan array pengambil_sampel: hapus entri kosong
        $pengambil = collect($request->input('pengambil_sampel', []))
            ->map('trim')
            ->filter()
            ->values()
            ->toArray();

        $data = $request->only([
            'perencanaan_id', 'lokasi_pengambilan_sampel', 'tanggal_pemantauan',
            'jenis_ikan', 'nama_latin', 'panjang_cm', 'berat_gram',
            'asal_benih_induk', 'padat_tebar', 'gejala_klinis', 'jumlah_kematian',
            'jumlah_sampel', 'metode_pengambilan_sampel', 'latitude', 'longitude',
        ]);
        $data['pengambil_sampel'] = !empty($pengambil) ? $pengambil : null;

        Pelaksanaan::create($data);

        return redirect()->route('perencanaan.index')->with('success', 'Data Lapangan Berhasil Disimpan!');
    }

    public function destroy($id)
    {
        $item = Pelaksanaan::findOrFail($id);
        
        // Jika bukan Pusat, pastikan pemilik data (lewat perencanaan)
        if (!Auth::user()->isPusat()) {
            if (!$item->perencanaan || $item->perencanaan->user_id !== Auth::id()) {
                abort(403);
            }
        }
        
        $item->delete();
        return back()->with('success', 'Data Pelaksanaan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $query = Pelaksanaan::whereIn('id', $ids);

        // Jika bukan Pusat, hanya boleh hapus milik sendiri (Berangkat dari Perencanaan)
        if (!Auth::user()->isPusat()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', Auth::id()));
        }

        $count = $query->delete();
        
        if ($count == 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus.');
        }

        return back()->with('success', $count . ' data pelaksanaan berhasil dihapus.');
    }
}
