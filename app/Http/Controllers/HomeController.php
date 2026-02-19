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
        $totalPerencanaan = Perencanaan::count();
        $totalPelaksanaan = Pelaksanaan::count();
        $totalGIS = Pelaksanaan::whereNotNull('latitude')->whereNotNull('longitude')->count();

        return view('home', compact('totalPerencanaan', 'totalPelaksanaan', 'totalGIS'));
    }
}
