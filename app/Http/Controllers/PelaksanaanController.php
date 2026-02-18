<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;

class PelaksanaanController extends Controller
{
    // Form untuk mengisi pelaksanaan berdasarkan ID Perencanaan
    public function create($id)
    {
        $rencana = Perencanaan::findOrFail($id);
        return view('pelaksanaan.create', compact('rencana'));
    }

    // Simpan data lapangan
    public function store(Request $request) {
    // Validasi sederhana agar data tidak kosong
    $request->validate([
        'perencanaan_id' => 'required',
        'lokasi_pengambilan_sampel' => 'required',
        'jumlah_sampel' => 'required|numeric',
    ]);

    // Simpan semua data dari form (termasuk lat & lng)
    \App\Models\Pelaksanaan::create([
        'perencanaan_id' => $request->perencanaan_id,
        'lokasi_pengambilan_sampel' => $request->lokasi_pengambilan_sampel,
        'jumlah_sampel' => $request->jumlah_sampel,
        'metode_pengambilan_sampel' => $request->metode_pengambilan_sampel,
        'latitude' => $request->latitude,   // Sekarang kita tangkap
        'longitude' => $request->longitude, // Sekarang kita tangkap
    ]);

    return redirect()->route('perencanaan.index')->with('success', 'Data Lapangan Berhasil Disimpan!');
}
}   
