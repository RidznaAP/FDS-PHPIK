<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaksanaan;
use App\Models\Laboratorium;

class LaboratoriumController extends Controller
{
    // Tampilkan daftar sampel yang perlu diuji (ambil dari Pelaksanaan)
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium']);

        // ── Sorting ──────────────────────────────────────────────────────
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'created_at', 'lokasi_pengambilan_sampel', 'jumlah_sampel'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // ── Auth-scoped Filtering ──────────────────────────────────────
        if ($user->isBkhit()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $query->whereHas('perencanaan', function($q) use ($user) {
                $q->whereIn('user_id', function($rq) use ($user) {
                    $rq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
                });
            });
        }

        // ── Search ─────────────────────────────────────────────────────
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lokasi_pengambilan_sampel', 'like', "%{$search}%")
                  ->orWhereHas('perencanaan', fn($rq) => $rq->where('jenis_mp', 'like', "%{$search}%")
                      ->orWhere('kab_kota', 'like', "%{$search}%"));
            });
        }

        // ── Tahun Filter ───────────────────────────────────────────────
        if ($request->tahun) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Ambil data pelaksanaan dengan info lab
        $pelaksanaans = $query->paginate(15)->withQueryString();

        // Tahun unik untuk filter sesuai dengan scope user
        $yearQuery = clone $query;
        $yearQuery->setEagerLoads([]);
        $yearQuery->orders = null; 
        
        $years = $yearQuery->selectRaw('YEAR(created_at) as year')
            ->distinct()->orderByDesc('year')
            ->pluck('year')
            ->filter();

        return view('laboratorium.index', compact('pelaksanaans', 'years'));
    }

    // Form input hasil laboratorium
    public function create($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $pelaksanaan = Pelaksanaan::with(['perencanaan.user'])->findOrFail($id);

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($user->isBkhit() && $pelaksanaan->perencanaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($user->isBbkhit()) {
            $owner = $pelaksanaan->perencanaan->user;
            if ($pelaksanaan->perencanaan->user_id !== $user->id && ($owner && $owner->parent_id !== $user->id)) {
                abort(403, 'Data ini berada di luar wilayah koordinasi Anda.');
            }
        }

        $jenis_penyakits = cache()->remember('master_jenis_penyakit', 86400, function() {
            return \App\Models\JenisPenyakit::aktif()->orderBy('nama')->get();
        });
        
        return view('laboratorium.create', compact('pelaksanaan', 'jenis_penyakits'));
    }

    // Simpan hasil laboratorium
    public function store(Request $request)
    {
        $request->validate([
            'pelaksanaan_id'    => 'required|exists:pelaksanaans,id',
            'kode_sampel'       => 'required|unique:laboratoriums,kode_sampel',
            'metode_uji'        => 'required|string',
            'jenis_hpik_diuji'  => 'required|string',
            'hasil_uji'         => 'required|string|max:255',
            'lab_penguji'       => 'required|string',
            'nama_petugas_uji'  => 'required|string|max:255',
            'tanggal_uji'       => 'required|date',
            'kelompok_patogen'  => 'required|string|in:Parasit,Bakteri,Virus,Jamur,Nihil',
            'prevalensi'        => 'nullable|numeric|min:0|max:100',
            'insidensi'         => 'nullable|numeric|min:0|max:100',
            'tanggal_hasil'     => 'nullable|date',
        ]);

        $data = $request->only([
            'pelaksanaan_id', 'kode_sampel', 'metode_uji', 'jenis_hpik_diuji',
            'hasil_uji', 'diagnosis_akhir', 'lab_penguji', 'nama_petugas_uji',
            'tanggal_uji', 'tanggal_hasil',
            'prevalensi', 'insidensi',
            'jumlah_ikan_terinfeksi', 'jumlah_sampel_diperiksa',
            'jumlah_kolam_uji', 'periode_pengamatan',
            'panjang', 'berat', 'asal_benih_induk',
            'padat_tebar', 'gejala_klinis', 'jumlah_kematian'
        ]);

        $data['hasil_parasit'] = 'Negatif (-)';
        $data['hasil_bakteri'] = 'Negatif (-)';
        $data['hasil_virus'] = 'Negatif (-)';
        $data['hasil_jamur'] = 'Negatif (-)';

        if ($request->kelompok_patogen === 'Parasit') $data['hasil_parasit'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Bakteri') $data['hasil_bakteri'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Virus') $data['hasil_virus'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Jamur') $data['hasil_jamur'] = 'Positif (+)';

        $lab = Laboratorium::create($data);

        return redirect()->route('pelaksanaan.show', $request->pelaksanaan_id)->with('success', 'Hasil Uji Laboratorium Berhasil Disimpan!');
    }

    public function edit($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $lab = Laboratorium::with(['pelaksanaan.perencanaan.user'])->findOrFail($id);
        $pelaksanaan = $lab->pelaksanaan;

        // ── Auth-scoped Check ──────────────────────────────────────
        if ($user->isBkhit() && $pelaksanaan->perencanaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        if ($user->isBbkhit()) {
            $owner = $pelaksanaan->perencanaan->user;
            if ($pelaksanaan->perencanaan->user_id !== $user->id && ($owner && $owner->parent_id !== $user->id)) {
                abort(403, 'Data ini berada di luar wilayah koordinasi Anda.');
            }
        }

        $jenis_penyakits = cache()->remember('master_jenis_penyakit', 86400, function() {
            return \App\Models\JenisPenyakit::aktif()->orderBy('nama')->get();
        });
        
        return view('laboratorium.edit', compact('lab', 'pelaksanaan', 'jenis_penyakits'));
    }

    public function update(Request $request, $id)
    {
        $lab = Laboratorium::findOrFail($id);
        
        $request->validate([
            'kode_sampel'       => 'required|unique:laboratoriums,kode_sampel,' . $lab->id,
            'metode_uji'        => 'required|string',
            'jenis_hpik_diuji'  => 'required|string',
            'hasil_uji'         => 'required|string|max:255',
            'lab_penguji'       => 'required|string',
            'nama_petugas_uji'  => 'required|string|max:255',
            'tanggal_uji'       => 'required|date',
            'kelompok_patogen'  => 'required|string|in:Parasit,Bakteri,Virus,Jamur,Nihil',
            'prevalensi'        => 'nullable|numeric|min:0|max:100',
            'insidensi'         => 'nullable|numeric|min:0|max:100',
            'tanggal_hasil'     => 'nullable|date',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $pelaksanaan = $lab->pelaksanaan;
        
        // ── Auth-scoped Check ──────────────────────────────────────
        if ($user->isBkhit() && $pelaksanaan->perencanaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        if ($user->isBbkhit()) {
            $owner = $pelaksanaan->perencanaan->user;
            if ($pelaksanaan->perencanaan->user_id !== $user->id && ($owner && $owner->parent_id !== $user->id)) {
                abort(403, 'Data ini berada di luar wilayah koordinasi Anda.');
            }
        }

        $data = $request->only([
            'kode_sampel', 'metode_uji', 'jenis_hpik_diuji',
            'hasil_uji', 'diagnosis_akhir', 'lab_penguji', 'nama_petugas_uji',
            'tanggal_uji', 'tanggal_hasil',
            'prevalensi', 'insidensi',
            'jumlah_ikan_terinfeksi', 'jumlah_sampel_diperiksa',
            'jumlah_kolam_uji', 'periode_pengamatan',
            'panjang', 'berat', 'asal_benih_induk',
            'padat_tebar', 'gejala_klinis', 'jumlah_kematian'
        ]);

        $data['hasil_parasit'] = 'Negatif (-)';
        $data['hasil_bakteri'] = 'Negatif (-)';
        $data['hasil_virus'] = 'Negatif (-)';
        $data['hasil_jamur'] = 'Negatif (-)';

        if ($request->kelompok_patogen === 'Parasit') $data['hasil_parasit'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Bakteri') $data['hasil_bakteri'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Virus') $data['hasil_virus'] = 'Positif (+)';
        elseif ($request->kelompok_patogen === 'Jamur') $data['hasil_jamur'] = 'Positif (+)';

        $lab->update($data);

        return redirect()->route('pelaksanaan.show', $pelaksanaan->id)->with('success', 'Hasil Uji Laboratorium Berhasil Diperbarui!');
    }

    public function show($id)
    {
        $lab = Laboratorium::with('pelaksanaan.perencanaan')->findOrFail($id);
        return view('laboratorium.show', compact('lab'));
    }

    public function destroy($id)
    {
        $item = Laboratorium::findOrFail($id);
        
        // Jika bukan Pusat, pastikan pemilik data (lewat pelaksanaan -> perencanaan)
        if (!\Illuminate\Support\Facades\Auth::user()->isPusat()) {
            if (!$item->pelaksanaan || !$item->pelaksanaan->perencanaan || $item->pelaksanaan->perencanaan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
                abort(403);
            }
        }

        $item->delete();
        return back()->with('success', 'Data Hasil Lab berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $query = Laboratorium::whereIn('id', $ids);

        // Jika bukan Pusat, hanya boleh hapus milik sendiri
        if (!\Illuminate\Support\Facades\Auth::user()->isPusat()) {
            $query->whereHas('pelaksanaan.perencanaan', fn($q) => $q->where('user_id', \Illuminate\Support\Facades\Auth::id()));
        }

        $count = $query->delete();
        
        if ($count == 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus.');
        }

        return back()->with('success', $count . ' data hasil laboratorium berhasil dihapus.');
    }
}
