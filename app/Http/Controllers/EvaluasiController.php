<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{


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

        return redirect()->route('perencanaan.show', $request->perencanaan_id)
            ->with('success', 'Status Evaluasi Akhir berhasil ditetapkan pada Peta GIS!');
    }


}
