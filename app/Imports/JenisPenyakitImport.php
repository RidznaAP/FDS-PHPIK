<?php

namespace App\Imports;

use App\Models\JenisPenyakit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class JenisPenyakitImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        if (!isset($row['nama_penyakit_hpik']) || empty($row['nama_penyakit_hpik'])) {
            return null;
        }

        $nama = trim($row['nama_penyakit_hpik']);

        // Normalize Golongan
        $golongan = ucfirst(strtolower($row['golongan_virusbakteriparasitjamur'] ?? ''));
        if (!in_array($golongan, ['Virus', 'Bakteri', 'Parasit', 'Jamur'])) {
            $golongan = 'Lainnya';
        }

        // Mencegah Duplikasi: Update jika nama sudah ada, buat baru jika belum
        return JenisPenyakit::updateOrCreate(
            ['nama' => $nama],
            [
                'organisme_penyebab' => $row['organisme_penyebab'] ?? null,
                'golongan'           => $golongan,
                'keterangan'         => $row['keterangan'] ?? null,
                'aktif'              => true,
            ]
        );
    }
}
