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
        $user = auth()->user();
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium']);

        if ($user->isBkhit()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $query->whereHas('perencanaan', function($q) use ($user) {
                $q->whereIn('user_id', function($rq) use ($user) {
                    $rq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
                });
            });
        }

        if (request()->filled('wilayah')) {
            $query->whereHas('perencanaan.user', fn($q) => $q->where('upt_asal', request('wilayah')));
        }

        return $query->get()->map(function ($p, $i) {
            $jenis = trim($p->jenis_ikan . ($p->nama_latin ? " ({$p->nama_latin})" : ''));

            return [
                'No'                      => $i + 1,
                'Nama Instansi'           => $p->perencanaan->user->upt_asal ?? $p->perencanaan->user->name ?? '-',
                'Provinsi'                => $p->perencanaan->provinsi ?? '-',
                'Lokasi Pemantauan'       => $p->lokasi_pengambilan_sampel,
                'Tanggal Pemantauan'      => $p->tanggal_pemantauan ? \Carbon\Carbon::parse($p->tanggal_pemantauan)->format('d/m/Y') : '-',
                'Jenis'                   => $jenis ?: '-',
                'Panjang (cm)'            => $p->laboratorium->panjang ?? $p->panjang_cm ?? '-',
                'Berat (gram)'            => $p->laboratorium->berat ?? $p->berat_gram ?? '-',
                'Asal Benih/ Induk'       => $p->laboratorium->asal_benih_induk ?? $p->asal_benih_induk ?? '-',
                'Padat Tebar'             => $p->laboratorium->padat_tebar ?? $p->padat_tebar ?? '-',
                'Gejala Klinis'           => $p->laboratorium->gejala_klinis ?? $p->gejala_klinis ?? '-',
                'Jumlah Kematian'         => $p->laboratorium->jumlah_kematian ?? $p->jumlah_kematian ?? '-',
                'Parasit'                 => $p->laboratorium->hasil_parasit ?? 'NT',
                'Bakteri'                 => $p->laboratorium->hasil_bakteri ?? 'NT',
                'Virus'                   => $p->laboratorium->hasil_virus ?? 'NT',
                'Jamur'                   => $p->laboratorium->hasil_jamur ?? 'NT',
                'Prev. (%)'               => $p->laboratorium->prevalensi ?? '-',
                'Insidensi (%)'           => $p->laboratorium->insidensi ?? '-',
                'Lab. Uji'                => $p->laboratorium->lab_penguji ?? ($p->perencanaan->lab_uji ?? '-'),
                'Ket'                     => $p->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['No', 'Nama Instansi', 'Provinsi', 'Lokasi Pemantauan (Kec/Desa)', 'Tanggal Pemantauan', 'Contoh Uji', '', '', '', '', '', '', 'Hasil Pemeriksaan', '', '', '', 'Prev. (%)', 'Insidensi (%)', 'Lab. Uji', 'Ket.'],
            ['', '', '', '', '', 'Jenis', 'Panjang (cm)', 'Berat (gram)', 'Asal Benih/ Induk', 'Padat Tebar', 'Gejala Klinis', 'Jumlah Kematian', 'Parasit', 'Bakteri', 'Virus', 'Jamur', '', '', '', ''],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A2'); // No
        $sheet->mergeCells('B1:B2'); // Nama Instansi
        $sheet->mergeCells('C1:C2'); // Provinsi
        $sheet->mergeCells('D1:D2'); // Lokasi
        $sheet->mergeCells('E1:E2'); // Tanggal
        $sheet->mergeCells('F1:L1'); // Contoh Uji (6-12)
        $sheet->mergeCells('M1:P1'); // Hasil (13-16)
        $sheet->mergeCells('Q1:Q2'); // Prev
        $sheet->mergeCells('R1:R2'); // Insidensi
        $sheet->mergeCells('S1:S2'); // Lab Uji
        $sheet->mergeCells('T1:T2'); // Ket

        $sheet->getStyle('A1:T3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D3D3D3'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        return [1 => ['font' => ['bold' => true]], 2 => ['font' => ['bold' => true]], 3 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return 'Output Hasil Pemantauan';
    }
}
