<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Exports\PerencanaanExport;
use App\Exports\PelaksanaanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Halaman pilihan laporan
    public function index()
    {
        return view('laporan.index');
    }

    // Export Excel: Data Perencanaan
    public function exportPerencanaan()
    {
        $filename = 'Laporan_Perencanaan_HPIK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new PerencanaanExport, $filename);
    }

    // Export Excel: Data Pelaksanaan + Laboratorium
    public function exportPelaksanaan()
    {
        $filename = 'Laporan_Pelaksanaan_HPIK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new PelaksanaanExport, $filename);
    }
}
