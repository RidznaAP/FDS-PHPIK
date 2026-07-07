<?php

namespace App\Exports;

use App\Models\MetodeUji;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MetodeUjiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        return MetodeUji::all();
    }

    public function headings(): array
    {
        return [
            'Nama Metode Uji',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama,
            $item->keterangan,
        ];
    }
}
