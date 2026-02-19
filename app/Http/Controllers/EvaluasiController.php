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
        // Ambil perencanaan yang memiliki data pelaksanaan dan hasil lab lengkap
        $perencanaans = Perencanaan::with(['pelaksanaans.laboratorium', 'evaluasi'])->latest()->get();
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
}
