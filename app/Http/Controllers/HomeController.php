<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\Laboratorium;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $isBkhit = $user->isBkhit();

        // ── #10: Scoped stats — BKHIT hanya lihat data miliknya ──────────
        $totalPerencanaan = Perencanaan::when($isBkhit, fn($q) => $q->where('user_id', $user->id))->count();

        // Pelaksanaan diambil via Perencanaan milik user (untuk BKHIT)
        $pelaksanaanQuery = Pelaksanaan::when($isBkhit, fn($q) =>
            $q->whereHas('perencanaan', fn($rq) => $rq->where('user_id', $user->id))
        );
        $totalPelaksanaan = (clone $pelaksanaanQuery)->count();

        // Titik GIS
        $totalGis = (clone $pelaksanaanQuery)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

        // Data peta
        $listPelaksanaan = (clone $pelaksanaanQuery)
            ->with('perencanaan')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        // ─────────────────────────────────────────────────────────────────

        return view('home', compact('totalPerencanaan', 'totalPelaksanaan', 'totalGis', 'listPelaksanaan'));
    }
}
