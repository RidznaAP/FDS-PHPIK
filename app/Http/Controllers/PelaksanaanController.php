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
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium'])->latest();

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
        $years = Pelaksanaan::selectRaw('YEAR(COALESCE(tanggal_pemantauan, created_at)) as tahun')
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
        $rencana = Perencanaan::findOrFail($id);
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

        \App\Models\Pelaksanaan::create($data);

        return redirect()->route('perencanaan.index')->with('success', 'Data Lapangan Berhasil Disimpan!');
    }
}   
