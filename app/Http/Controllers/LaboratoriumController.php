<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaksanaan;
use App\Models\Laboratorium;

class LaboratoriumController extends Controller
{
    // Tampilkan daftar sampel yang perlu diuji (ambil dari Pelaksanaan)
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium'])->latest();

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

        // Ambil data pelaksanaan dengan info lab
        $pelaksanaans = $query->paginate(15)->withQueryString();
        return view('laboratorium.index', compact('pelaksanaans'));
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

        return view('laboratorium.create', compact('pelaksanaan'));
    }

    // Simpan hasil laboratorium
    public function store(Request $request)
    {
        $request->validate([
            'pelaksanaan_id'   => 'required|exists:pelaksanaans,id',
            'kode_sampel'      => 'required|unique:laboratoriums,kode_sampel',
            'metode_uji'       => 'required|string',
            'jenis_hpik_diuji' => 'required|string',
            'hasil_uji'        => 'required|in:Positif,Negatif,Inkonklusif',
            'lab_penguji'      => 'required|string',
            'tanggal_uji'      => 'required|date',
            'hasil_parasit'    => 'nullable|in:+,-,NT',
            'hasil_bakteri'    => 'nullable|in:+,-,NT',
            'hasil_virus'      => 'nullable|in:+,-,NT',
            'hasil_jamur'      => 'nullable|in:+,-,NT',
            'prevalensi'       => 'nullable|numeric|min:0|max:100',
            'insidensi'        => 'nullable|numeric|min:0|max:100',
            'tanggal_hasil'    => 'nullable|date',
        ]);

        Laboratorium::create($request->only([
            'pelaksanaan_id', 'kode_sampel', 'metode_uji', 'jenis_hpik_diuji',
            'hasil_uji', 'diagnosis_akhir', 'lab_penguji', 'tanggal_uji', 'tanggal_hasil',
            'hasil_parasit', 'hasil_bakteri', 'hasil_virus', 'hasil_jamur',
            'prevalensi', 'insidensi',
            'jumlah_ikan_terinfeksi', 'jumlah_sampel_diperiksa',
            'jumlah_kolam_uji', 'periode_pengamatan',
        ]));

        return redirect()->route('laboratorium.index')->with('success', 'Hasil Uji Laboratorium Berhasil Disimpan!');
    }

    // ── Show Detail Laboratorium ─────────────────────────────────────────
    public function show($id)
    {
        $lab = Laboratorium::with('pelaksanaan.perencanaan')->findOrFail($id);
        return view('laboratorium.show', compact('lab'));
    }
}
