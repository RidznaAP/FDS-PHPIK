<?php

namespace App\Imports;

use App\Models\MediaPembawa;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class MediaPembawaImport implements OnEachRow, WithHeadingRow
{
    use Importable;

    public function onRow(Row $rowData)
    {
        $row = $rowData->toArray();
        // Cek nama umum (sebelumnya nama_media_pembawa)
        $namaUmum = $row['nama_umum'] ?? null;
        
        if (empty(trim($namaUmum))) {
            return;
        }

        $nama = trim($namaUmum);
        $namaInggris = isset($row['nama_inggris']) ? trim($row['nama_inggris']) : null;
        $namaIlmiah = isset($row['nama_ilmiah']) ? trim($row['nama_ilmiah']) : null;

        // Mencegah Duplikasi: Gunakan updateOrCreate agar data yang sama persis tidak dobel
        MediaPembawa::updateOrCreate(
            ['nama' => $nama],
            [
                'nama_inggris' => $namaInggris,
                'keterangan'   => $namaIlmiah,
                'aktif'        => true,
            ]
        );
    }
}
