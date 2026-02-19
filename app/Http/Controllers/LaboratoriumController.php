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
        // Ambil data pelaksanaan yang belum ada hasil laboratoriumnya
        $pelaksanaans = Pelaksanaan::with(['perencanaan', 'laboratorium'])->latest()->get();
        return view('laboratorium.index', compact('pelaksanaans'));
    }

    // Form input hasil laboratorium
    public function create($id)
    {
        $pelaksanaan = Pelaksanaan::with('perencanaan')->findOrFail($id);
        return view('laboratorium.create', compact('pelaksanaan'));
    }

    // Simpan hasil laboratorium
    public function store(Request $request)
    {
        $request->validate([
            'pelaksanaan_id' => 'required',
            'kode_sampel' => 'required|unique:laboratoriums,kode_sampel',
            'metode_uji' => 'required',
            'jenis_hpik_diuji' => 'required',
            'hasil_uji' => 'required',
            'lab_penguji' => 'required',
            'tanggal_uji' => 'required|date',
        ]);

        Laboratorium::create($request->all());

        return redirect()->route('laboratorium.index')->with('success', 'Hasil Uji Laboratorium Berhasil Disimpan!');
    }
}
