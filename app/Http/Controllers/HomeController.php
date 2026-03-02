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
        
        // ── Auth-scoped Data Selection ──────────────────────────────────
        if ($user->isBkhit()) {
            // BKHIT: data sendiri
            $perencanaanQuery = Perencanaan::where('user_id', $user->id);
            $pelaksanaanQuery = Pelaksanaan::whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            // BBKHIT: data sendiri + unit di bawah koordinasinya
            $perencanaanQuery = Perencanaan::whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
            $pelaksanaanQuery = Pelaksanaan::whereHas('perencanaan', function($q) use ($user) {
                $q->whereIn('user_id', function($rq) use ($user) {
                    $rq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
                });
            });
        } else {
            // PUSAT/Lainnya: semua data
            $perencanaanQuery = Perencanaan::query();
            $pelaksanaanQuery = Pelaksanaan::query();
        }

        // Hitung Statistik
        $totalPerencanaan = (clone $perencanaanQuery)->count();
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

        // ── Data untuk Grafik ──────────────────────────────────────────
        // 1. Tren Perencanaan Bulanan (6 bulan terakhir)
        $chartMonthlyLabels = [];
        $chartMonthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartMonthlyLabels[] = $month->translatedFormat('M Y');
            $chartMonthlyData[] = (clone $perencanaanQuery)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        // 2. Distribusi Status (Donut Chart)
        $statusCounts = [
            'Draft' => (clone $perencanaanQuery)->where('status', 'draft')->count(),
            'Waiting' => (clone $perencanaanQuery)->where('status', 'waiting')->count(),
            'Approved' => (clone $perencanaanQuery)->where('status', 'approved')->count(),
        ];
        // ─────────────────────────────────────────────────────────────────

        return view('home', compact(
            'totalPerencanaan', 
            'totalPelaksanaan', 
            'totalGis', 
            'listPelaksanaan',
            'chartMonthlyLabels',
            'chartMonthlyData',
            'statusCounts'
        ));
    }
}
