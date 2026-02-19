<?php

namespace App\Exports;

use App\Models\Pelaksanaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PelaksanaanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Pelaksanaan::with(['perencanaan', 'laboratorium'])->get()->map(function ($p, $i) {
            return [
                'No'                      => $i + 1,
                'Provinsi'                => $p->perencanaan->provinsi ?? '-',
                'Kab/Kota'               => $p->perencanaan->kab_kota ?? '-',
                'Jenis MP'               => $p->perencanaan->jenis_mp ?? '-',
                'Jenis HPIK'             => $p->perencanaan->jenis_hpik ?? '-',
                'Lokasi Sampling'         => $p->lokasi_pengambilan_sampel,
                'Jumlah Sampel'          => $p->jumlah_sampel,
                'Metode Sampling'        => $p->metode_pengambilan_sampel ?? '-',
                'Latitude'               => $p->latitude ?? '-',
                'Longitude'              => $p->longitude ?? '-',
                'Kode Sampel Lab'        => $p->laboratorium->kode_sampel ?? 'Belum',
                'Metode Uji'             => $p->laboratorium->metode_uji ?? '-',
                'Hasil Uji'              => $p->laboratorium->hasil_uji ?? 'Belum',
                'Lab Penguji'            => $p->laboratorium->lab_penguji ?? '-',
                'Tanggal Uji'            => $p->laboratorium->tanggal_uji ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Provinsi', 'Kab/Kota', 'Jenis MP', 'Jenis HPIK',
            'Lokasi Sampling', 'Jumlah Sampel', 'Metode Sampling',
            'Latitude', 'Longitude',
            'Kode Sampel Lab', 'Metode Uji', 'Hasil Uji', 'Lab Penguji', 'Tanggal Uji',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a6e3c'],
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
        return 'Data Pelaksanaan & Lab';
    }
}
