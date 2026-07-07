<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\Notifikasi;

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

    public function create(Perencanaan $perencanaan)
    {
        $user = Auth::user();
        $rencana = $perencanaan->load('user');

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($rencana->user_id !== $user->id && (!$rencana->user || $rencana->user->parent_id !== $user->id) && !$user->isPusat() && !$user->isDeveloper()) {
            abort(403, 'Akses ditolak.');
        }

        if ($rencana->pelaksanaans()->count() >= $rencana->target_uji) {
            return redirect()->route('perencanaan.show', $rencana->id)->with('error', 'Target uji sudah terpenuhi. Tidak dapat menambahkan pelaksanaan baru.');
        }

        return view('pelaksanaan.create', compact('rencana'));
    }

    // ── Show Detail Pelaksanaan ───────────────────────────────────────────    // Menampilkan detail pelaksanaan
    public function show(Pelaksanaan $pelaksanaan)
    {
        $user = Auth::user();
        $item = $pelaksanaan->load(['perencanaan.user', 'laboratorium']);

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($user->isBkhit() && $item->perencanaan->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $jenis_penyakits = \App\Models\JenisPenyakit::all();
        $metode_ujis = \App\Models\MetodeUji::all();

        return view('pelaksanaan.show', compact('item', 'jenis_penyakits', 'metode_ujis'));
    }

    // #Cetak PDF Pelaksanaan Tunggal
    public function print(Pelaksanaan $pelaksanaan)
    {
        $user = Auth::user();
        $item = $pelaksanaan->load(['perencanaan.user', 'laboratorium']);

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($user->isBkhit() && $item->perencanaan->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('pelaksanaan.print', compact('item'));
    }

    // Edit form for pelaksanaan
    public function edit(Pelaksanaan $pelaksanaan)
    {
        $user = Auth::user();
        $item = $pelaksanaan->load(['perencanaan.user']);

        // Auth check: only owner, Pusat, or Developer can edit
        if (!$user->isPusat() && !$user->isDeveloper()) {
            if (!$item->perencanaan || $item->perencanaan->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
            }
        }

        return view('pelaksanaan.edit', compact('item'));
    }

    // Update pelaksanaan data
    public function update(Request $request, Pelaksanaan $pelaksanaan)
    {
        $item = $pelaksanaan->load('perencanaan.user');
        $user = Auth::user();

        // Auth check
        if (!$user->isPusat() && !$user->isDeveloper()) {
            if (!$item->perencanaan || $item->perencanaan->user_id !== $user->id) {
                abort(403);
            }
        }

        $request->validate([
            'lokasi_pengambilan_sampel' => 'required|string',
            'tanggal_pemantauan'        => 'required|date',
            'jenis_ikan'                => 'required|string',
            'jumlah_sampel'             => 'required|integer|min:1',
            'metode_pengambilan_sampel' => 'required|string',
            'jumlah_kematian'           => 'nullable|integer|min:0',
            'panjang_cm'                => 'nullable|numeric|min:0',
            'berat_gram'                => 'nullable|numeric|min:0',
            'padat_tebar'               => 'nullable|integer|min:0',
            'latitude'                  => 'nullable|numeric',
            'longitude'                 => 'nullable|numeric',
            'pengambil_sampel'          => 'nullable|array',
            'pengambil_sampel.*'        => 'nullable|string|max:100',
        ]);

        $pengambil = collect($request->input('pengambil_sampel', []))
            ->map('trim')->filter()->values()->toArray();

        $data = $request->only([
            'lokasi_pengambilan_sampel', 'tanggal_pemantauan', 'jenis_ikan',
            'nama_latin', 'panjang_cm', 'berat_gram', 'asal_benih_induk',
            'padat_tebar', 'gejala_klinis', 'jumlah_kematian',
            'jumlah_sampel', 'metode_pengambilan_sampel', 'latitude', 'longitude',
        ]);
        $data['pengambil_sampel'] = !empty($pengambil) ? $pengambil : null;

        $item->update($data);

        return redirect()->route('pelaksanaan.show', $pelaksanaan->id)
            ->with('success', 'Data Pelaksanaan berhasil diperbarui.');
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

        if ($rencana->user_id !== $user->id && (!$rencana->user || $rencana->user->parent_id !== $user->id) && !$user->isPusat() && !$user->isDeveloper()) {
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

        return redirect()->route('perencanaan.show', $rencana->id)->with('success', 'Data Lapangan Berhasil Disimpan! Silakan input hasil lab di bawah.');
    }

    public function destroy(Pelaksanaan $pelaksanaan)
    {
        $item = $pelaksanaan;
        
        // Jika bukan Pusat atau Developer, pastikan pemilik data atau wilayah
        $user = Auth::user();
        if (!$user->isPusat() && !$user->isDeveloper()) {
            $owner = $item->perencanaan->user ?? null;
            if ($user->isBbkhit()) {
                if ($item->perencanaan->user_id !== $user->id && ($owner && $owner->parent_id !== $user->id)) {
                    abort(403, 'Akses ditolak: Data ini berada di luar koordinasi Anda.');
                }
            } else {
                if (!$item->perencanaan || $item->perencanaan->user_id !== $user->id) {
                    abort(403, 'Akses ditolak: Anda hanya dapat menghapus data milik Anda sendiri.');
                }
            }
        }
        
        $id = $item->id;
        $item->delete();

        // Bersihkan notifikasi terkait
        Notifikasi::hapusTerkaitUrl("/pelaksanaan/{$id}");

        return back()->with('success', 'Data Pelaksanaan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $query = Pelaksanaan::whereIn('id', $ids);

        $user = Auth::user();
        // Jika bukan Pusat atau Developer, hanya boleh hapus milik sendiri
        if (!$user->isPusat() && !$user->isDeveloper()) {
            if ($user->isBbkhit()) {
                $childIds = \App\Models\User::where('parent_id', $user->id)->pluck('id')->push($user->id);
                $query->whereHas('perencanaan', fn($q) => $q->whereIn('user_id', $childIds));
            } else {
                $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
            }
        }

        $count = $query->delete();

        if ($count > 0) {
            foreach ($ids as $id) {
                Notifikasi::hapusTerkaitUrl("/pelaksanaan/{$id}");
            }
        }
        
        if ($count == 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus.');
        }

        return back()->with('success', $count . ' data pelaksanaan berhasil dihapus.');
    }
}
