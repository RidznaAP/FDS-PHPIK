<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected ?string $role;

    public function __construct(?string $role = null)
    {
        $this->role = $role;
    }

    public function collection()
    {
        $query = User::with('coordinator')
            ->whereNotIn('role', ['developer']) // Developer tidak diekspor
            ->orderBy('role')
            ->orderBy('name');

        if ($this->role) {
            $query->where('role', $this->role);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama / UPT',
            'Nama Lengkap',
            'Email',
            'Password (Awal/Terakhir Reset)',
            'Role',
            'Koordinator (BBKHIT)',
            'UPT Asal',
            'Tanggal Dibuat',
        ];
    }

    public function map($user): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $user->upt_asal ?? $user->name,
            $user->name,
            $user->email,
            $user->plain_password ?? '(belum diset / lihat password saat dibuat)',
            strtoupper($user->role),
            $user->coordinator ? ($user->coordinator->upt_asal ?? $user->coordinator->name) : '—',
            $user->upt_asal ?? '—',
            $user->created_at->format('d/m/Y'),
        ];
    }

    public function title(): string
    {
        return 'Data Akun Pengguna';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
