<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\User;
use App\Models\Notifikasi;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // ═══════════════════════════════════════════════════════════════
        // ZONE 1 — KPI Stats (Nasional)
        // ═══════════════════════════════════════════════════════════════
        $totalPerencanaan = Perencanaan::count();
        $totalPelaksanaan = Pelaksanaan::count();
        $totalUptAktif    = User::where('role', 'bkhit')
                                ->whereHas('perencanaan', fn($q) => $q->where('status', 'approved'))
                                ->count();
        $totalApproved    = Perencanaan::where('status', 'approved')->count();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 2A — Grafik Pelaksanaan per Bulan (12 bulan terakhir)
        // ═══════════════════════════════════════════════════════════════
        $chartBulanLabels = [];
        $chartBulanData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartBulanLabels[] = $month->translatedFormat('M Y');
            $chartBulanData[]   = Pelaksanaan::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        // ═══════════════════════════════════════════════════════════════
        // ZONE 2B — Top 5 Media Pembawa Dominan
        // Dari field jenis_mp di perencanaan (bisa berupa string tunggal)
        // ═══════════════════════════════════════════════════════════════
        $mediaPembawaRaw = Perencanaan::whereNotNull('jenis_mp')
            ->pluck('jenis_mp');

        $mediaTally = [];
        foreach ($mediaPembawaRaw as $raw) {
            foreach (array_map('trim', explode(',', $raw)) as $item) {
                if ($item) $mediaTally[$item] = ($mediaTally[$item] ?? 0) + 1;
            }
        }
        arsort($mediaTally);
        $top5Media        = array_slice($mediaTally, 0, 5, true);
        $chartMediaLabels = array_keys($top5Media);
        $chartMediaData   = array_values($top5Media);

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3A — Top Jenis Penyakit (HPIK) Dominan
        // Dari field jenis_hpik di perencanaan
        // ═══════════════════════════════════════════════════════════════
        $hpikRaw = Perencanaan::whereNotNull('jenis_hpik')->pluck('jenis_hpik');
        $hpikTally = [];
        foreach ($hpikRaw as $raw) {
            foreach (array_map('trim', explode(',', $raw)) as $item) {
                if ($item) $hpikTally[$item] = ($hpikTally[$item] ?? 0) + 1;
            }
        }
        arsort($hpikTally);
        $top8Hpik          = array_slice($hpikTally, 0, 8, true);
        $chartHpikLabels   = array_keys($top8Hpik);
        $chartHpikData     = array_values($top8Hpik);

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3B — Status Perencanaan (Donut)
        // ═══════════════════════════════════════════════════════════════
        $statusCounts = [
            'Draft'    => Perencanaan::where('status', 'draft')->count(),
            'Menunggu' => Perencanaan::where('status', 'waiting')->count(),
            'Disetujui'=> Perencanaan::where('status', 'approved')->count(),
        ];

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3C — Top 5 UPT Paling Aktif (by pelaksanaan count)
        // ═══════════════════════════════════════════════════════════════
        $topUpt = User::where('role', 'bkhit')
            ->withCount(['perencanaan as pelaksanaan_count' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->orderByDesc('pelaksanaan_count')
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 4 — Peta (titik dari pelaksanaan yang punya lat/lng)
        // ═══════════════════════════════════════════════════════════════
        $petaData = Pelaksanaan::with('perencanaan.user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(fn($p) => [
                'lat'        => (float) $p->latitude,
                'lng'        => (float) $p->longitude,
                'lokasi'     => $p->lokasi_sampling ?? ($p->perencanaan?->kab_kota ?? '—'),
                'provinsi'   => $p->perencanaan?->provinsi ?? '—',
                'komoditas'  => $p->komoditas_ikan ?? ($p->perencanaan?->jenis_mp ?? '—'),
                'upt'        => $p->perencanaan?->user?->name ?? '—',
                'tanggal'    => $p->tgl_pelaksanaan ?? $p->created_at?->format('d M Y'),
            ]);

        // Notifikasi unread count untuk badge
        $unreadNotif = Notifikasi::where('user_id', Auth::id())->where('dibaca', false)->count();

        return view('home', compact(
            'totalPerencanaan', 'totalPelaksanaan', 'totalUptAktif', 'totalApproved',
            'chartBulanLabels', 'chartBulanData',
            'chartMediaLabels', 'chartMediaData',
            'chartHpikLabels',  'chartHpikData',
            'statusCounts', 'topUpt',
            'petaData', 'unreadNotif'
        ));
    }
}
