<?php

namespace App\Exports;

use App\Models\JenisPenyakit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JenisPenyakitExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $template;

    public function __construct($template = false)
    {
        $this->template = $template;
    }

    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        return JenisPenyakit::all();
    }

    public function headings(): array
    {
        return [
            'Nama Penyakit / HPIK',
            'Organisme Penyebab',
            'Golongan (Virus/Bakteri/Parasit/Jamur)',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama,
            $item->organisme_penyebab,
            $item->golongan,
            $item->keterangan,
        ];
    }
}
