<?php

namespace App\Imports;

use App\Models\MediaPembawa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class MediaPembawaImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        if (!isset($row['nama_media_pembawa_inang_rentan']) || empty($row['nama_media_pembawa_inang_rentan'])) {
            return null;
        }

        $nama = trim($row['nama_media_pembawa_inang_rentan']);

        // Mencegah Duplikasi: Gunakan updateOrCreate agar data yang sama persis tidak dobel
        return MediaPembawa::updateOrCreate(
            ['nama' => $nama],
            [
                'keterangan' => $row['keterangan'] ?? null,
                'aktif'      => true,
            ]
        );
    }
}
