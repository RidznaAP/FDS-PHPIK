<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    protected $fillable = [
        'perencanaan_id', 'lokasi_pengambilan_sampel', 'tanggal_pemantauan',
        'jenis_ikan', 'nama_latin', 'panjang_cm', 'berat_gram',
        'asal_benih_induk', 'padat_tebar', 'gejala_klinis', 'jumlah_kematian',
        'jumlah_sampel', 'metode_pengambilan_sampel',
        'latitude', 'longitude', 'pengambil_sampel',
    ];

    public static function getDominanPenyakitPerProvinsi($baseQuery = null)
    {
        $query = $baseQuery ?: self::query();

        // ── Pass 1: Semua provinsi yang punya hasil lab (positif maupun nihil) ──
        $semuaHasil = (clone $query)
            ->whereHas('laboratorium')
            ->with(['perencanaan', 'laboratorium'])
            ->get();

        $aggPositif = [];  // Akumulasi penyakit positif per provinsi
        $nihilCount = [];  // Provinsi dengan hanya nihil/negatif

        foreach ($semuaHasil as $p) {
            $prov = strtoupper(trim($p->perencanaan?->provinsi));
            if (empty($prov) || $prov === '—') continue;

            $lab      = $p->laboratorium;
            $rawHasil = trim($lab->hasil_uji ?? '');

            $isNihil   = in_array(strtoupper($rawHasil), ['NEGATIF', 'NIHIL']);
            $isPositif = !empty($rawHasil)
                && !$isNihil
                && !in_array(strtoupper($rawHasil), ['INKONKLUSIF', '—', '']);

            if ($isPositif) {
                // Tentukan nama penyakit dominan
                $penyakit = 'HPIK';
                if (!in_array(strtoupper($rawHasil), ['POSITIF'])) {
                    $penyakit = $rawHasil;
                } else {
                    $penyakit = $lab->jenis_hpik_diuji ?: ($lab->diagnosis_akhir ?: 'HPIK');
                }
                $penyakit = strtoupper(trim($penyakit));

                if (!isset($aggPositif[$prov]))           $aggPositif[$prov] = [];
                if (!isset($aggPositif[$prov][$penyakit])) $aggPositif[$prov][$penyakit] = 0;
                $aggPositif[$prov][$penyakit]++;
            }

            if ($isNihil) {
                $nihilCount[$prov] = ($nihilCount[$prov] ?? 0) + 1;
            }
        }

        $dominantProvinsi = [];
        // Hindari hijau di palette — hijau sudah dicadangkan untuk Nihil
        $colorPalette = ['#ef4444', '#f97316', '#eab308', '#06b6d4', '#6366f1', '#a855f7', '#ec4899', '#f43f5e'];
        $diseaseToColor = [];
        $ci = 0;

        // ── Pass 2: Warna provinsi positif berdasarkan penyakit dominan ──
        foreach ($aggPositif as $prov => $penyakits) {
            arsort($penyakits);
            $dominant = array_key_first($penyakits);
            if (!isset($diseaseToColor[$dominant])) {
                $diseaseToColor[$dominant] = $colorPalette[$ci % count($colorPalette)];
                $ci++;
            }
            $dominantProvinsi[$prov] = [
                'dominant' => $dominant,
                'count'    => $penyakits[$dominant],
                'color'    => $diseaseToColor[$dominant],
                'status'   => 'positif',
            ];
        }

        // ── Pass 3: Warna hijau untuk provinsi nihil saja (tidak ada positif) ──
        foreach ($nihilCount as $prov => $count) {
            if (!isset($dominantProvinsi[$prov])) {
                $dominantProvinsi[$prov] = [
                    'dominant' => 'Nihil',
                    'count'    => $count,
                    'color'    => '#22c55e',
                    'status'   => 'nihil',
                ];
            }
        }

        return $dominantProvinsi;
    }

    protected $casts = [
        'tanggal_pemantauan' => 'date',
        'pengambil_sampel' => 'array',
    ];

    // Relasi balik ke Perencanaan
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class);
    }

    // Relasi ke Laboratorium (satu pelaksanaan punya satu hasil lab)
    public function laboratorium()
    {
        return $this->hasOne(Laboratorium::class);
    }
}