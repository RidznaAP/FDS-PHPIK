<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\User;
use App\Models\Notifikasi;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // ═══════════════════════════════════════════════════════════════
        // Scoping Helper — closure untuk filter query berdasarkan role
        // ═══════════════════════════════════════════════════════════════
        $scopeUserIds = function () use ($user) {
            if ($user->isBkhit()) {
                return [$user->id];
            } elseif ($user->isBbkhit()) {
                $childIds = User::where('parent_id', $user->id)->pluck('id')->push($user->id)->toArray();
                return $childIds;
            }
            return null; // null = tidak difilter (Pusat = semua)
        };

        $userIds = $scopeUserIds();

        // ── Filter Tahun & Daftar Tahun Tersedia ────────────────────────────
        $availableYears = Pelaksanaan::selectRaw('YEAR(created_at) as year')
            ->union(Perencanaan::selectRaw('tahun as year'))
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
            
        if (empty($availableYears)) $availableYears = [date('Y')];
        
        $selectedYear = $request->get('year', date('Y'));

        // ═══════════════════════════════════════════════════════════════
        // ZONE 1 — KPI Stats (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $totalPerencanaan = Perencanaan::where('tahun', $selectedYear)
            ->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count();
        
        $totalPelaksanaan = Pelaksanaan::whereYear('created_at', $selectedYear)
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))->count();
        
        $totalApproved    = Perencanaan::where('tahun', $selectedYear)
            ->where('status', 'approved')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count();

        // UPT Aktif: Total institusi UPT (BKHIT) yang telah memiliki hasil uji lab di tahun terpilih
        if ($user->isBkhit()) {
            $totalUptAktif = Pelaksanaan::whereYear('created_at', $selectedYear)
                ->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('laboratorium')
                ->exists() ? 1 : 0;
        } else {
            $totalUptAktif = User::where('role', 'bkhit')
                ->whereHas('perencanaan.pelaksanaans.laboratorium')
                ->when($user->isBbkhit(), fn($q) => $q->where('parent_id', $user->id))
                ->count();
        }

        // ── Filter Tahun & Daftar Tahun Tersedia ────────────────────────────
        $availableYears = Pelaksanaan::selectRaw('YEAR(created_at) as year')
            ->union(Perencanaan::selectRaw('tahun as year'))
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
            
        if (empty($availableYears)) $availableYears = [date('Y')];
        
        $selectedYear = $request->get('year', date('Y'));

        // ═══════════════════════════════════════════════════════════════
        // ZONE 2A — Grafik Pelaksanaan per Bulan (Jan - Des tahun terpilih)
        // ═══════════════════════════════════════════════════════════════
        $chartBulanLabels = [];
        $chartBulanData   = [];
        
        $monthlyCounts = Pelaksanaan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $selectedYear)
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
            
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::createFromDate($selectedYear, $m, 1);
            $chartBulanLabels[] = $date->translatedFormat('M');
            $chartBulanData[]   = $monthlyCounts[$m] ?? 0;
        }

        // ═══════════════════════════════════════════════════════════════
        // ZONE 2B — Top 5 Media Pembawa Dominan (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $mediaPembawaRaw = Perencanaan::where('tahun', $selectedYear)
            ->whereNotNull('jenis_mp')
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
        // ZONE 3A — Top Jenis Penyakit (HPIK) Terdeteksi (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $hpikTerdeteksiQuery = \App\Models\Laboratorium::whereYear('created_at', $selectedYear);
        if ($userIds !== null) {
            $hpikTerdeteksiQuery->whereIn('pelaksanaan_id', function($q) use ($userIds, $selectedYear) {
                $q->select('id')->from('pelaksanaans')
                  ->whereYear('created_at', $selectedYear)
                  ->whereIn('perencanaan_id', function($sq) use ($userIds, $selectedYear) {
                      $sq->select('id')->from('perencanaans')
                        ->where('tahun', $selectedYear)
                        ->whereIn('user_id', $userIds);
                  });
            });
        }

        $labPositif = $hpikTerdeteksiQuery->get();
        $hpikTerdeteksiTally = [];

        foreach ($labPositif as $lab) {
            $rawHasil = trim($lab->hasil_uji);
            $isPositif = !empty($rawHasil) && 
                         strcasecmp($rawHasil, 'Negatif') !== 0 && 
                         strcasecmp($rawHasil, 'NIHIL') !== 0 && 
                         strcasecmp($rawHasil, 'Inkonklusif') !== 0 &&
                         $rawHasil !== '—';

            if ($isPositif) {
                // Gunakan diagnosis_akhir jika ada, jika tidak jenis_hpik_diuji
                // Gunakan hasil_uji (jika spesifik) > jenis_hpik_diuji > diagnosis_akhir
                $namaPenyakit = 'HPIK';
                if (!empty($rawHasil) && !in_array(strtoupper($rawHasil), ['POSITIF', 'NEGATIF', 'NIHIL', 'INKONKLUSIF', '—'])) {
                    $namaPenyakit = $rawHasil;
                } else {
                    $namaPenyakit = $lab->jenis_hpik_diuji ?: ($lab->diagnosis_akhir ?: 'HPIK');
                }
                if ($namaPenyakit && $namaPenyakit !== 'Positif') {
                    foreach (array_map('trim', explode(',', $namaPenyakit)) as $p) {
                        if ($p) {
                            $hpikTerdeteksiTally[$p] = ($hpikTerdeteksiTally[$p] ?? 0) + 1;
                        }
                    }
                }
            }
        }
        arsort($hpikTerdeteksiTally);
        $top8HpikTerdeteksi = array_slice($hpikTerdeteksiTally, 0, 8, true);
        $chartHpikLabels    = array_keys($top8HpikTerdeteksi);
        $chartHpikData      = array_values($top8HpikTerdeteksi);

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3B — Status Perencanaan (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $statusCounts = [
            'Draft'     => Perencanaan::where('tahun', $selectedYear)->where('status', 'draft')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
            'Menunggu'  => Perencanaan::where('tahun', $selectedYear)->where('status', 'waiting')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
            'Disetujui' => Perencanaan::where('tahun', $selectedYear)->where('status', 'approved')->when($userIds !== null, fn($q) => $q->whereIn('user_id', $userIds))->count(),
        ];

        // ═══════════════════════════════════════════════════════════════
        // ZONE 3C — Top 5 UPT Paling Aktif (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $topUpt = User::where('role', 'bkhit')
            ->when($user->isBbkhit(), fn($q) => $q->where('parent_id', $user->id))
            ->withCount(['perencanaan as pelaksanaan_count' => function ($q) use ($selectedYear) {
                $q->where('tahun', $selectedYear)->whereHas('pelaksanaans.laboratorium');
            }])
            ->orderByDesc('pelaksanaan_count')
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 4 — Peta (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════

        // Dominan penyakit per provinsi: difilter PER TAHUN yang dipilih
        // agar warna provinsi mencerminkan dominasi penyakit di tahun tersebut.
        $domQuery = Pelaksanaan::whereYear('created_at', $selectedYear);
        if ($userIds !== null) {
            $domQuery->whereHas('perencanaan', fn($q) => $q->whereIn('user_id', $userIds));
        }
        $dominantProvinsi = Pelaksanaan::getDominanPenyakitPerProvinsi($domQuery);

        // Query peta (marker titik) juga difilter per tahun
        $baseQuery = Pelaksanaan::whereYear('created_at', $selectedYear);
        if ($userIds !== null) {
            $baseQuery->whereHas('perencanaan', fn($q) => $q->whereIn('user_id', $userIds));
        }
        
        $petaData = Pelaksanaan::with(['perencanaan.user', 'perencanaan.evaluasi', 'laboratorium'])
            ->whereYear('created_at', $selectedYear)
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
                
                $hasilLab = 'Belum Diuji';
                $rawHasil = '';
                if ($p->laboratorium) {
                    $rawHasil = trim($p->laboratorium->hasil_uji);
                    if (strcasecmp($rawHasil, 'Negatif') === 0 || strcasecmp($rawHasil, 'NIHIL') === 0) {
                        $hasilLab = 'Negatif';
                    } elseif (strcasecmp($rawHasil, 'Inkonklusif') === 0) {
                        $hasilLab = 'Inkonklusif';
                    } elseif (!empty($rawHasil) && $rawHasil !== '—') {
                        $hasilLab = 'Positif';
                    }
                }

                return [
                    'id'         => $p->id,
                    'lat'        => (float) $p->latitude,
                    'lng'        => (float) $p->longitude,
                    'lokasi'     => $p->lokasi_pengambilan_sampel ?? ($p->perencanaan?->kab_kota ?? '—'),
                    'provinsi'   => $p->perencanaan?->provinsi ?? '—',
                    'kab_kota'   => $p->perencanaan?->kab_kota ?? '—',
                    'komoditas'  => $p->jenis_ikan ?? ($p->perencanaan?->jenis_mp ?? '—'),
                    'upt'        => $p->perencanaan?->user?->name ?? '—',
                    'tanggal'    => $p->tanggal_pemantauan ? \Carbon\Carbon::parse($p->tanggal_pemantauan)->format('d M Y') : $p->created_at?->format('d M Y'),
                    'warna'      => $warna,
                    'hasil_lab'  => $hasilLab,
                    'hasil_raw'  => (strcasecmp($rawHasil, 'Negatif') === 0) ? 'Nihil' : ($rawHasil ?: 'Belum Diuji'),
                    'jenis_hpik' => ($hasilLab === 'Positif' && !empty($rawHasil) && !in_array(strtoupper($rawHasil), ['POSITIF'])) 
                                        ? $rawHasil 
                                        : ($p->laboratorium->jenis_hpik_diuji ?? ($p->perencanaan?->jenis_hpik ?? '—')),
                ];
            });



        // ═══════════════════════════════════════════════════════════════
        // ZONE 5 — Aktivitas Terbaru (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $aktivitasTerbaru = Pelaksanaan::with(['perencanaan.user', 'laboratorium'])
            ->whereYear('created_at', $selectedYear)
            ->when($userIds !== null, fn($q) => $q->whereHas('perencanaan', fn($rq) => $rq->whereIn('user_id', $userIds)))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ═══════════════════════════════════════════════════════════════
        // ZONE 6 — Rekap Hasil Uji (Filtered by Year)
        // ═══════════════════════════════════════════════════════════════
        $labQuery = \App\Models\Laboratorium::whereYear('created_at', $selectedYear);
        if ($userIds !== null) {
            $labQuery->whereIn('pelaksanaan_id', function($q) use ($userIds, $selectedYear) {
                $q->select('id')->from('pelaksanaans')
                  ->whereYear('created_at', $selectedYear)
                  ->whereIn('perencanaan_id', function($sq) use ($userIds, $selectedYear) {
                      $sq->select('id')->from('perencanaans')
                        ->where('tahun', $selectedYear)
                        ->whereIn('user_id', $userIds);
                  });
            });
        }

        $labResults = $labQuery->select('hasil_uji', DB::raw('count(*) as total'))
            ->groupBy('hasil_uji')
            ->pluck('total', 'hasil_uji')
            ->toArray();

        $rekapHasil = [
            'positif'     => 0,
            'negatif'     => 0,
            'inkonklusif' => 0,
            'total'       => array_sum($labResults)
        ];

        foreach ($labResults as $status => $count) {
            $statusClean = trim($status);
            if (strcasecmp($statusClean, 'Negatif') === 0 || strcasecmp($statusClean, 'NIHIL') === 0) {
                $rekapHasil['negatif'] += $count;
            } elseif (strcasecmp($statusClean, 'Inkonklusif') === 0) {
                $rekapHasil['inkonklusif'] += $count;
            } elseif (!empty($statusClean) && $statusClean !== '—') {
                $rekapHasil['positif'] += $count;
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // ZONE 8 — Heatmap Data (Titik Positif)
        // ═══════════════════════════════════════════════════════════════
        $heatmapData = $petaData->filter(fn($p) => $p['hasil_lab'] === 'Positif')
            ->map(fn($p) => [$p['lat'], $p['lng'], 1]) // intensity 1
            ->values();

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
            'availableYears', 'selectedYear',
            'aktivitasTerbaru', 'rekapHasil',
            'heatmapData'
        ));
    }
}
