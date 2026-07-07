<?php

namespace App\Exports;

use App\Models\MediaPembawa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediaPembawaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        return MediaPembawa::all();
    }

    public function headings(): array
    {
        return [
            'Nama Umum',
            'Nama Inggris',
            'Nama Ilmiah',
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama,
            $item->nama_inggris,
            $item->keterangan,
        ];
    }
}
