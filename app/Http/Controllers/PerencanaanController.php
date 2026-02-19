<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;

class PerencanaanController extends Controller
{
    // Menampilkan Form Tambah
    public function create()
    {
        return view('perencanaan.create');
    }

    // Daftar Perencanaan
    public function index()
    {
        $perencanaans = Perencanaan::latest()->get();
        return view('perencanaan.index', compact('perencanaans'));
    }

    // Menyimpan Data ke Database
    public function store(Request $request)
    {
        $total_tw = $request->tw1 + $request->tw2 + $request->tw3 + $request->tw4;

        Perencanaan::create([
            'provinsi'          => $request->provinsi,
            'kab_kota'          => $request->kab_kota,
            'jenis_mp'          => $request->jenis_mp,
            'jenis_hpik'        => $request->jenis_hpik,
            'kemampuan_uji_upt' => $request->kemampuan_uji_upt,
            'metode_pengujian'  => $request->metode_pengujian,
            'lab_uji'           => $request->lab_uji,
            'target_uji'        => $request->target_uji,
            'tw1'               => $request->tw1,
            'tw2'               => $request->tw2,
            'tw3'               => $request->tw3,
            'tw4'               => $request->tw4,
            'total_pengujian'   => $total_tw,
            'status'            => 'draft',
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // UPT mengajukan validasi: draft -> waiting
    public function submit($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        if ($perencanaan->status === 'draft') {
            $perencanaan->update(['status' => 'waiting']);
            return back()->with('success', 'Perencanaan berhasil diajukan untuk validasi.');
        }
        return back()->with('error', 'Status tidak valid untuk diajukan.');
    }

    // BBKHIT/Pusat menyetujui perencanaan: waiting -> approved
    public function approve($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        if ($perencanaan->status === 'waiting') {
            $perencanaan->update(['status' => 'approved']);
            return back()->with('success', 'Perencanaan telah disetujui!');
        }
        return back()->with('error', 'Hanya perencanaan dengan status waiting yang bisa disetujui.');
    }
}