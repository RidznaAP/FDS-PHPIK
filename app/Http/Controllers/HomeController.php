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
        $user = Auth::user();

        // ═══════════════════════════════════════════════════════════════
        // Scoping Helper — closure untuk filter query berdasarkan role
        // ═══════════════════════════════════════════════════════════════
        $scopeUserIds = function () use ($user) {
            if ($user->isBkhit()) {
                return [$user->id];
            } elseif ($user->isBbkhit()) {
                return User::where('id', $user->id)
                    ->orWhere('parent_id', $user->id)
                    ->pluck('id')
                    ->toArray();
            }
            return null; // null = tidak difilter (Pusat = semua)
        };

        $userIds = $scopeUserIds();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 1 — KPI Stats
        // ═══════════════════════════════════════════════════════════════
        $totalPerencanaan = Perencanaan::when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count();
        $totalPelaksanaan = Pelaksanaan::when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))->count();
        $totalApproved    = Perencanaan::where('status', 'approved')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count();

        // UPT Aktif: Total institusi UPT (BKHIT) yang terdaftar di bawah BBKHIT / Nasional
        if ($user->isBkhit()) {
            $totalUptAktif = 1;
        } else {
            $totalUptAktif = User::where('role', 'bkhit')
                ->when($user->isBbkhit(), fn($q) => $q->where('parent_id', $user->id))
                ->count();
        }

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
                ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
                ->count();
        }

        // ═══════════════════════════════════════════════════════════════
        // ZONE 2B — Top 5 Media Pembawa Dominan
        // ═══════════════════════════════════════════════════════════════
        $mediaPembawaRaw = Perencanaan::whereNotNull('jenis_mp')
            ->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))
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
        // ═══════════════════════════════════════════════════════════════
        $hpikRaw = Perencanaan::whereNotNull('jenis_hpik')
            ->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))
            ->pluck('jenis_hpik');

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
            'Draft'     => Perencanaan::where('status', 'draft')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
            'Menunggu'  => Perencanaan::where('status', 'waiting')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
            'Disetujui' => Perencanaan::where('status', 'approved')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
        ];

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3C — Top 5 UPT Paling Aktif (by perencanaan approved)
        // ═══════════════════════════════════════════════════════════════
        $topUpt = User::where('role', 'bkhit')
            ->when($user->isBbkhit(), fn($q) => $q->where('parent_id', $user->id))
            ->withCount(['perencanaan as pelaksanaan_count' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->orderByDesc('pelaksanaan_count')
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 4 — Peta (titik dari pelaksanaan yang punya lat/lng + Status Warna)
        // ═══════════════════════════════════════════════════════════════
        $petaData = Pelaksanaan::with(['perencanaan.user', 'perencanaan.evaluasi', 'laboratorium'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->get()
            ->map(function($p) {
                // Tentukan warna marker berdasar evaluasi wilayah atau hasil lab terakhir
                $warna = 'blue'; // default
                if ($p->perencanaan && $p->perencanaan->evaluasi) {
                    $warna = strtolower($p->perencanaan->evaluasi->warna); // hijau, kuning, merah
                } elseif ($p->laboratorium) {
                    $warna = $p->laboratorium->hasil_uji === 'Positif' ? 'merah' : 'hijau';
                }
                
                return [
                    'lat'        => (float) $p->latitude,
                    'lng'        => (float) $p->longitude,
                    'lokasi'     => $p->lokasi_pengambilan_sampel ?? ($p->perencanaan?->kab_kota ?? '—'),
                    'provinsi'   => $p->perencanaan?->provinsi ?? '—',
                    'komoditas'  => $p->jenis_ikan ?? ($p->perencanaan?->jenis_mp ?? '—'),
                    'upt'        => $p->perencanaan?->user?->name ?? '—',
                    'tanggal'    => $p->tanggal_pemantauan ? \Carbon\Carbon::parse($p->tanggal_pemantauan)->format('d M Y') : $p->created_at?->format('d M Y'),
                    'warna'      => $warna,
                    'hasil_lab'  => $p->laboratorium ? $p->laboratorium->hasil_uji : 'Belum Uji Lab'
                ];
            });

        // ═══════════════════════════════════════════════════════════════
        // ZONE 4B — Agregasi Dominan Penyakit per Provinsi (Untuk Polygon)
        // ═══════════════════════════════════════════════════════════════
        $provinsiSakit = Pelaksanaan::whereHas('laboratorium', function($q) {
                $q->where('hasil_uji', 'Positif');
            })
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->with(['perencanaan', 'laboratorium'])->get();

        $agg = [];
        foreach($provinsiSakit as $p) {
             $prov = strtoupper(Trim($p->perencanaan?->provinsi));
             if (empty($prov) || $prov === '—') continue;
             $penyakit = strtoupper($p->laboratorium->diagnosis_akhir ?: $p->laboratorium->jenis_hpik_diuji ?: 'HPIK');
             if(!isset($agg[$prov])) $agg[$prov] = [];
             if(!isset($agg[$prov][$penyakit])) $agg[$prov][$penyakit] = 0;
             $agg[$prov][$penyakit]++;
        }

        $dominantProvinsi = [];
        // Palette warna untuk membedakan jenis penyakit di provinsi
        $colorPalette = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#6366f1', '#a855f7', '#ec4899'];
        $diseaseToColor = [];
        $ci = 0;

        foreach($agg as $prov => $penyakits) {
            arsort($penyakits); // Sort desc by count
            $dominant = array_key_first($penyakits);
            if(!isset($diseaseToColor[$dominant])) {
                 $diseaseToColor[$dominant] = $colorPalette[$ci % count($colorPalette)];
                 $ci++;
            }
            $dominantProvinsi[$prov] = [
                'dominant' => $dominant,
                'count' => $penyakits[$dominant],
                'color' => $diseaseToColor[$dominant]
            ];
        }

        // ═══════════════════════════════════════════════════════════════
        // ZONE 5 — Aktivitas Terbaru (5 pelaksanaan terakhir)
        // ═══════════════════════════════════════════════════════════════
        $aktivitasTerbaru = Pelaksanaan::with(['perencanaan.user', 'laboratorium'])
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 6 — Menunggu Tindakan (Action Required)
        // ═══════════════════════════════════════════════════════════════
        $menungguApproval = 0;
        $menungguLab = 0;

        if ($user->isBbkhit() || $user->isPusat()) {
            $menungguApproval = Perencanaan::where('status', 'waiting')
                ->when($user->isBbkhit(), fn($q) => $q->whereHas('user', fn($uq) => $uq->where('parent_id', $user->id)))
                ->count();
        }

        $menungguLab = Pelaksanaan::whereDoesntHave('laboratorium')
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->count();

        // Notifikasi unread count untuk badge
        $unreadNotif = Notifikasi::where('user_id', Auth::id())->where('dibaca', false)->count();

        return view('home', compact(
            'totalPerencanaan', 'totalPelaksanaan', 'totalUptAktif', 'totalApproved',
            'chartBulanLabels', 'chartBulanData',
            'chartMediaLabels', 'chartMediaData',
            'chartHpikLabels',  'chartHpikData',
            'statusCounts', 'topUpt',
            'dominantProvinsi',
            'petaData', 'unreadNotif',
            'aktivitasTerbaru', 'menungguApproval', 'menungguLab'
        ));
    }
}
