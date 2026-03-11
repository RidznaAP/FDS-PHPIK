<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    // Daftar perencanaan yang perlu dievaluasi
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Perencanaan::with(['pelaksanaans.laboratorium', 'evaluasi']);

        // ── Sorting ──────────────────────────────────────────────────────
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'created_at', 'provinsi', 'kab_kota', 'jenis_mp'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // Ambil perencanaan dengan data pelaksanaan dan hasil lab
        $perencanaans = $query->paginate(15)->withQueryString();
        return view('evaluasi.index', compact('perencanaans'));
    }

    // Form evaluasi untuk perencanaan tertentu
    public function create($id)
    {
        $perencanaan = Perencanaan::with(['pelaksanaans.laboratorium'])->findOrFail($id);
        return view('evaluasi.create', compact('perencanaan'));
    }

    // Simpan hasil evaluasi
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_id' => 'required',
            'kesimpulan' => 'required',
            'status_warna' => 'required',
            'tanggal_evaluasi' => 'required|date',
            'evaluator' => 'required',
        ]);

        // Simpan evaluasi dengan hanya input tervalidasi (Mencegah Mass Assignment)
        Evaluasi::create($request->only([
            'perencanaan_id', 'kesimpulan', 'status_warna', 'tanggal_evaluasi', 
            'evaluator', 'prevalensi', 'insidensi', 'rekomendasi'
        ]));

        return redirect()->route('evaluasi.index')->with('success', 'Evaluasi Berhasil Disimpan!');
    }

    // ── Show Detail Evaluasi ─────────────────────────────────────────────
    public function show($id)
    {
        $evaluasi = Evaluasi::with('perencanaan')->findOrFail($id);
        return view('evaluasi.show', compact('evaluasi'));
    }

    public function destroy($id)
    {
        $item = Evaluasi::findOrFail($id);
        
        // Jika bukan Pusat, pastikan pemilik data (lewat perencanaan)
        if (!Auth::user()->isPusat()) {
            if (!$item->perencanaan || $item->perencanaan->user_id !== Auth::id()) {
                abort(403);
            }
        }
        
        $item->delete();
        return back()->with('success', 'Data Evaluasi berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $query = Evaluasi::whereIn('id', $ids);

        // Jika bukan Pusat, hanya boleh hapus milik sendiri
        if (!Auth::user()->isPusat()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', Auth::id()));
        }

        $count = $query->delete();
        
        if ($count == 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus.');
        }

        return back()->with('success', $count . ' data evaluasi berhasil dihapus.');
    }
}
