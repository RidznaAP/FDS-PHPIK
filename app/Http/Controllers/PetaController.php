<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaksanaan;
use App\Models\Evaluasi;
use App\Models\Perencanaan;

class PetaController extends Controller
{
    // Peta GIS interaktif
    public function index()
    {
        // Ambil semua pelaksanaan yang punya koordinat GPS
        $lokasis = Pelaksanaan::with(['perencanaan.evaluasi', 'laboratorium'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Format data untuk Leaflet JS (JSON)
        $markers = $lokasis->map(function ($item) {
            $statusWarna = 'abu-abu'; // default: belum dievaluasi
            $badgeClass = 'secondary';
            $hasilLab = 'Belum Ada Hasil';

            if ($item->perencanaan && $item->perencanaan->evaluasi) {
                $statusWarna = $item->perencanaan->evaluasi->status_warna;
                $badgeClass = $item->perencanaan->evaluasi->warna;
            }

            if ($item->laboratorium) {
                $hasilLab = $item->laboratorium->hasil_uji;
            }

            return [
                'lat'       => (float) $item->latitude,
                'lng'       => (float) $item->longitude,
                'lokasi'    => $item->lokasi_pengambilan_sampel,
                'provinsi'  => $item->perencanaan->provinsi ?? '-',
                'kab_kota'  => $item->perencanaan->kab_kota ?? '-',
                'jenis_mp'  => $item->perencanaan->jenis_mp ?? '-',
                'jenis_hpik'=> $item->perencanaan->jenis_hpik ?? '-',
                'hasil_lab' => $hasilLab,
                'warna'     => $statusWarna, // hijau / kuning / merah / abu-abu
                'badge'     => $badgeClass,
                'kesimpulan'=> $item->perencanaan->evaluasi->kesimpulan ?? 'Belum Dievaluasi',
                'id'        => $item->id,
            ];
        });

        // Statistik ringkasan
        $stats = [
            'hijau'   => $markers->where('warna', 'hijau')->count(),
            'kuning'  => $markers->where('warna', 'kuning')->count(),
            'merah'   => $markers->where('warna', 'merah')->count(),
            'abu'     => $markers->where('warna', 'abu-abu')->count(),
        ];

        return view('peta.index', [
            'markers' => $markers->values(),
            'stats'   => $stats,
        ]);
    }
}
