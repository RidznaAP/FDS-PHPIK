<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaksanaan;
use App\Models\Perencanaan;
use Carbon\Carbon;

class PetaController extends Controller
{
    /**
     * Peta GIS interaktif dengan dukungan filter sidebar.
     */
    public function index(Request $request)
    {
        // ── Tahun filter ──────────────────────────────────────────────────────
        $availableYears = Pelaksanaan::selectRaw('YEAR(created_at) as year')
            ->union(Perencanaan::selectRaw('YEAR(created_at) as year'))
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (empty($availableYears)) $availableYears = [date('Y')];
        $selectedYear = $request->get('year', date('Y'));

        // ── Query dasar dengan eager loading ──────────────────────────────────
        $query = Pelaksanaan::with(['perencanaan.evaluasi', 'perencanaan.user', 'laboratorium'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereYear('created_at', $selectedYear);

        // ── Scope berbasis User Role ──────────────────────────────────────────
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->isBkhit()) {
            $query->whereHas('perencanaan', fn ($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $childIds = \App\Models\User::where('parent_id', $user->id)->pluck('id')->push($user->id);
            $query->whereHas('perencanaan', fn ($q) => $q->whereIn('user_id', $childIds));
        }

        // ── Filter: Provinsi ──────────────────────────────────────────────────
        if ($request->filled('provinsi')) {
            $query->whereHas('perencanaan', fn ($q) =>
                $q->where('provinsi', $request->provinsi)
            );
        }

        // ── Filter: Hasil Lab ─────────────────────────────────────────────────
        if ($request->filled('hasil_lab')) {
            if ($request->hasil_lab === 'belum') {
                $query->whereDoesntHave('laboratorium');
            } elseif ($request->hasil_lab === 'Positif') {
                $query->whereHas('laboratorium', fn ($q) =>
                    $q->whereNotIn('hasil_uji', ['Negatif', 'NIHIL', 'Inkonklusif', '—', ''])
                );
            } elseif ($request->hasil_lab === 'Negatif') {
                $query->whereHas('laboratorium', fn ($q) =>
                    $q->whereIn('hasil_uji', ['Negatif', 'NIHIL'])
                );
            }
        }

        // ── Filter: Jenis HPIK ────────────────────────────────────────────────
        if ($request->filled('hpik')) {
            $hpikFilter = $request->hpik;
            $query->where(function ($q) use ($hpikFilter) {
                $q->whereHas('laboratorium', fn ($lq) =>
                    $lq->where('jenis_hpik_diuji', 'like', "%{$hpikFilter}%")
                       ->orWhere('diagnosis_akhir', 'like', "%{$hpikFilter}%")
                )->orWhereHas('perencanaan', fn ($pq) =>
                    $pq->where('jenis_hpik', 'like', "%{$hpikFilter}%")
                );
            });
        }

        $lokasis = $query->get();

        // ── Format marker untuk Leaflet ───────────────────────────────────────
        $markers = $lokasis->map(function ($item) {
            $hasilLab = 'Belum Diuji';
            $penyakit = $item->perencanaan->jenis_hpik ?? '-';

            if ($item->laboratorium) {
                $rawHasil = trim($item->laboratorium->hasil_uji);
                
                // Categorize
                if (strcasecmp($rawHasil, 'Negatif') === 0 || strcasecmp($rawHasil, 'NIHIL') === 0) {
                    $hasilLab = 'Negatif';
                } elseif (strcasecmp($rawHasil, 'Inkonklusif') === 0) {
                    $hasilLab = 'Inkonklusif';
                } elseif (!empty($rawHasil) && $rawHasil !== '—') {
                    // Any other value (like a disease name) is considered Positif
                    $hasilLab = 'Positif';
                } else {
                    $hasilLab = 'Belum Diuji';
                }

                // Prioritas Nama Penyakit: Hasil Uji (spesifik) > Jenis HPIK Diuji > Diagnosis Akhir > Perencanaan
                if (!empty($rawHasil) && !in_array(strtoupper($rawHasil), ['POSITIF', 'NEGATIF', 'NIHIL', 'INKONKLUSIF', '—'])) {
                    $penyakit = $rawHasil;
                } else {
                    $penyakit = $item->laboratorium->jenis_hpik_diuji ?: ($item->laboratorium->diagnosis_akhir ?: ($item->perencanaan->jenis_hpik ?? 'HPIK'));
                }
            }

            return [
                'lat'         => (float) $item->latitude,
                'lng'         => (float) $item->longitude,
                'lokasi'      => $item->lokasi_pengambilan_sampel,
                'provinsi'    => $item->perencanaan->provinsi ?? '-',
                'kab_kota'    => $item->perencanaan->kab_kota ?? '-',
                'jenis_mp'    => $item->perencanaan->jenis_mp ?? '-',
                'jenis_hpik'  => $penyakit,
                'hasil_lab'   => $hasilLab, // Normalized status for coloring
                'hasil_raw'   => (strcasecmp($item->laboratorium->hasil_uji ?? '', 'Negatif') === 0) ? 'Nihil' : ($item->laboratorium->hasil_uji ?? 'Belum Diuji'), // Actual value for popup
                'upt'         => $item->perencanaan->user->name ?? '-',
                'tanggal'     => $item->tanggal_pemantauan ? \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d/m/Y') : '-',
                'id'          => $item->id,
            ];
        });

        // ── Statistik ringkasan ────────────────────────────────────────────────
        $stats = [
            'total'   => $markers->count(),
            'positif' => $markers->where('hasil_lab', 'Positif')->count(),
            'negatif' => $markers->where('hasil_lab', 'Negatif')->count(),
            'pending' => $markers->where('hasil_lab', 'Belum Diuji')->count() + $markers->where('hasil_lab', 'Inkonklusif')->count(),
        ];

        // ── Top penyakit untuk ringkasan ───────────────────────────────────────
        $penyakitTally = [];
        foreach ($lokasis as $item) {
            if ($item->laboratorium) {
                $hpik = $item->laboratorium->jenis_hpik_diuji
                    ?: $item->perencanaan->jenis_hpik
                    ?: null;
                if ($hpik) {
                    foreach (array_map('trim', explode(',', $hpik)) as $p) {
                        if ($p) {
                            $tag = strtoupper($p);
                            if (!isset($penyakitTally[$tag])) {
                                $penyakitTally[$tag] = ['total' => 0, 'positif' => 0];
                            }
                            $penyakitTally[$tag]['total']++;
                            $rawHasil = trim($item->laboratorium->hasil_uji);
                            $isPositif = !empty($rawHasil) && 
                                         strcasecmp($rawHasil, 'Negatif') !== 0 && 
                                         strcasecmp($rawHasil, 'NIHIL') !== 0 && 
                                         strcasecmp($rawHasil, 'Inkonklusif') !== 0 &&
                                         $rawHasil !== '—';

                            if ($isPositif) {
                                $penyakitTally[$tag]['positif']++;
                            }
                        }
                    }
                }
            }
        }
        uasort($penyakitTally, fn ($a, $b) => $b['positif'] - $a['positif']);
        $topPenyakit = array_slice($penyakitTally, 0, 6, true);

        // ── Opsi dropdown filter ───────────────────────────────────────────────
        $provinsiList = Perencanaan::whereNotNull('provinsi')
            ->distinct()->orderBy('provinsi')->pluck('provinsi');

        $hpikList = Perencanaan::whereNotNull('jenis_hpik')
            ->distinct()->orderBy('jenis_hpik')->pluck('jenis_hpik');

        // ── Aggregasi Dominan HPIK per Provinsi (Thematic Map, difilter per tahun) ───
        $domQuery = \App\Models\Pelaksanaan::whereYear('created_at', $selectedYear);
        if ($user->isBkhit()) {
            $domQuery->whereHas('perencanaan', fn ($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $domQuery->whereHas('perencanaan', fn ($q) => $q->whereIn('user_id', $childIds ?? []));
        }
        $dominantProvinsi = \App\Models\Pelaksanaan::getDominanPenyakitPerProvinsi($domQuery);

        return view('peta.index', [
            'markers'          => $markers->values(),
            'stats'            => $stats,
            'topPenyakit'      => $topPenyakit,
            'provinsiList'     => $provinsiList,
            'hpikList'         => $hpikList,
            'dominantProvinsi' => $dominantProvinsi,
            'availableYears'   => $availableYears,
            'selectedYear'     => $selectedYear,
            'filters'          => $request->only(['provinsi', 'hasil_lab', 'hpik', 'year']),
        ]);
    }
}
