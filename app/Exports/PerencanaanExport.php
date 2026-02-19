<?php

namespace App\Exports;

use App\Models\Perencanaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerencanaanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Perencanaan::all()->map(function ($p, $i) {
            return [
                'No'                => $i + 1,
                'Provinsi'          => $p->provinsi,
                'Kab/Kota'          => $p->kab_kota,
                'Jenis MP'          => $p->jenis_mp,
                'Jenis HPIK'        => $p->jenis_hpik,
                'Kemampuan Uji UPT' => $p->kemampuan_uji_upt,
                'Metode Pengujian'  => $p->metode_pengujian,
                'Lab Uji'           => $p->lab_uji,
                'Target Uji'        => $p->target_uji,
                'TW1'               => $p->tw1,
                'TW2'               => $p->tw2,
                'TW3'               => $p->tw3,
                'TW4'               => $p->tw4,
                'Total'             => $p->total_pengujian,
                'Status'            => strtoupper($p->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Provinsi', 'Kab/Kota', 'Jenis MP', 'Jenis HPIK',
            'Kemampuan Uji UPT', 'Metode Pengujian', 'Lab Uji', 'Target Uji',
            'TW1', 'TW2', 'TW3', 'TW4', 'Total', 'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003366'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return 'Data Perencanaan';
    }
}
