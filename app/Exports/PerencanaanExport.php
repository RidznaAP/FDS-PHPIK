<?php

namespace App\Exports;

use App\Models\Perencanaan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerencanaanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function view(): View
    {
        if ($this->isTemplate) {
            return view('exports.perencanaan', ['data' => collect([])]);
        }
        $user = auth()->user();
        $query = Perencanaan::query();

        if ($user->isBkhit()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $query->whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }

        $data = $query->with('user')->get();

        return view('exports.perencanaan', [
            'data' => $data
        ]);
    }

    public function title(): string
    {
        return 'Data Perencanaan';
    }
}
