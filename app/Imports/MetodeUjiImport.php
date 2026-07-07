<?php

namespace App\Imports;

use App\Models\MetodeUji;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class MetodeUjiImport implements OnEachRow, WithHeadingRow
{
    use Importable;

    public function onRow(Row $rowData)
    {
        $row = $rowData->toArray();
        if (!isset($row['nama_metode_uji']) || empty(trim($row['nama_metode_uji']))) {
            return;
        }

        $nama = trim($row['nama_metode_uji']);

        MetodeUji::updateOrCreate(
            ['nama' => $nama],
            [
                'keterangan' => $row['keterangan'] ?? null,
                'aktif'      => true,
            ]
        );
    }
}
