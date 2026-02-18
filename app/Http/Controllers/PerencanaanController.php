<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;

class PerencanaanController extends Controller
{
    // Menampilkan Form
    public function create()
    {
        return view('perencanaan.create');
    }
    public function index()
    {
        $perencanaans = \App\Models\Perencanaan::latest()->get();

        return view('perencanaan.index', compact('perencanaans'));
    }
    // Menyimpan Data ke Database
    public function store(Request $request)
    {
        // Hitung total otomatis dari TW
        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        \App\Models\Perencanaan::create([
            'provinsi' => $request->provinsi,
            'kab_kota' => $request->kab_kota,
            'jenis_mp' => $request->jenis_mp,
            'jenis_hpik' => $request->jenis_hpik,
            'kemampuan_uji_upt' => $request->kemampuan_uji_upt, // Sekarang terisi
            'metode_pengujian' => $request->metode_pengujian,
            'lab_uji' => $request->lab_uji,
            'target_uji' => $request->target_uji,
            'tw1' => $request->tw1,
            'tw2' => $request->tw2,
            'tw3' => $request->tw3,
            'tw4' => $request->tw4,
            'total_pengujian' => $total_tw,
            'status' => 'draft',
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Data berhasil ditambahkan!');
    }
}