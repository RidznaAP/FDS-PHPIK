<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Mengambil jumlah data untuk statistik di dashboard
        $totalPerencanaan = \App\Models\Perencanaan::count();
        $totalPelaksanaan = \App\Models\Pelaksanaan::count();
        
        // Menghitung titik GIS (yang latitude & longitude-nya tidak kosong)
        $totalGis = \App\Models\Pelaksanaan::whereNotNull('latitude')
                                            ->whereNotNull('longitude')
                                            ->count();

        // Kirim data ke view home.blade.php
        return view('home', compact('totalPerencanaan', 'totalPelaksanaan', 'totalGis'));
    }
}
