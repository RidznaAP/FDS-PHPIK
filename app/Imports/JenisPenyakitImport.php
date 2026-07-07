<?php

namespace App\Imports;

use App\Models\JenisPenyakit;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class JenisPenyakitImport implements OnEachRow, WithHeadingRow
{
    use Importable;

    public function onRow(Row $rowData)
    {
        $row = $rowData->toArray();
        if (!isset($row['nama_penyakit_hpik']) || empty(trim($row['nama_penyakit_hpik']))) {
            return;
        }

        $nama = trim($row['nama_penyakit_hpik']);

        // Normalize Kelompok Patogen
        $golongan = ucfirst(strtolower($row['kelompok_patogen_virusbakteriparasitjamurlainnya'] ?? $row['golongan_virusbakteriparasitjamur'] ?? ''));
        if (!in_array($golongan, ['Virus', 'Bakteri', 'Parasit', 'Jamur', 'Lainnya'])) {
            $golongan = 'Lainnya';
        }

        // Gunakan organisme_penyebab sebagai kunci unik (sesuai aturan validasi baru).
        // Jika organisme_penyebab kosong, selalu buat record baru.
        $organisme = !empty(trim($row['organisme_penyebab'] ?? '')) ? trim($row['organisme_penyebab']) : null;

        if ($organisme) {
            JenisPenyakit::updateOrCreate(
                ['organisme_penyebab' => $organisme],
                [
                    'nama'     => $nama,
                    'golongan' => $golongan,
                    'aktif'    => true,
                ]
            );
        } else {
            JenisPenyakit::create([
                'nama'               => $nama,
                'organisme_penyebab' => null,
                'golongan'           => $golongan,
                'aktif'              => true,
            ]);
        }
    }
}
