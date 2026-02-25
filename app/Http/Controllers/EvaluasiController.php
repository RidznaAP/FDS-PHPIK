<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    // Daftar perencanaan yang perlu dievaluasi
    public function index()
    {
        $user = Auth::user();
        $query = Perencanaan::with(['pelaksanaans.laboratorium', 'evaluasi', 'user'])->latest();

        // ── Auth-scoped Filtering ──────────────────────────────────────
        if ($user->isBkhit()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $query->whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')
                  ->where('id', $user->id)
                  ->orWhere('parent_id', $user->id);
            });
        }

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

        // Simpan evaluasi
        Evaluasi::create($request->all());

        return redirect()->route('evaluasi.index')->with('success', 'Evaluasi Berhasil Disimpan!');
    }

    // ── Show Detail Evaluasi ─────────────────────────────────────────────
    public function show($id)
    {
        $evaluasi = Evaluasi::with('perencanaan')->findOrFail($id);
        return view('evaluasi.show', compact('evaluasi'));
    }
}
