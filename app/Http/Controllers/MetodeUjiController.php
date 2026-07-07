<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetodeUji;
use App\Exports\MetodeUjiExport;
use App\Imports\MetodeUjiImport;
use Maatwebsite\Excel\Facades\Excel;

class MetodeUjiController extends Controller
{
    public function index(Request $request)
    {
        $sort      = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');
        $search    = $request->get('q');

        $query = MetodeUji::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $items = $query->orderBy($sort, $direction)->paginate(100)->withQueryString();
        
        return view('master.metode_uji.index', compact('items'));
    }

    public function create()
    {
        return view('master.metode_uji.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:200|unique:metode_ujis,nama',
            'keterangan'=> 'nullable|string|max:500',
            'aktif'     => 'boolean',
        ]);

        MetodeUji::create([
            'nama'       => $request->nama,
            'keterangan' => $request->keterangan,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        cache()->forget('master_metode_uji');

        return redirect()->route('master.metode-uji.index')
            ->with('success', '"' . $request->nama . '" berhasil ditambahkan ke Master Data.');
    }

    public function edit(MetodeUji $metodeUji)
    {
        return view('master.metode_uji.form', ['item' => $metodeUji]);
    }

    public function update(Request $request, MetodeUji $metodeUji)
    {
        $request->validate([
            'nama'      => 'required|string|max:200|unique:metode_ujis,nama,' . $metodeUji->id,
            'keterangan'=> 'nullable|string|max:500',
            'aktif'     => 'boolean',
        ]);

        $metodeUji->update([
            'nama'       => $request->nama,
            'keterangan' => $request->keterangan,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        cache()->forget('master_metode_uji');

        return redirect()->route('master.metode-uji.index')
            ->with('success', '"' . $metodeUji->nama . '" berhasil diperbarui.');
    }

    public function destroy(MetodeUji $metodeUji)
    {
        $nama = $metodeUji->nama;
        $metodeUji->delete();
        cache()->forget('master_metode_uji');
        return redirect()->route('master.metode-uji.index')
            ->with('success', '"' . $nama . '" berhasil dihapus dari Master Data.');
    }

    public function export()
    {
        return Excel::download(new MetodeUjiExport(), 'master_metode_uji_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MetodeUjiExport(true), 'template_import_metode_uji.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new MetodeUjiImport;
            Excel::import($import, $request->file('file'));
            cache()->forget('master_metode_uji');
            
            return redirect()->back()->with('success', 'Data Metode Uji berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        MetodeUji::whereIn('id', $ids)->delete();
        cache()->forget('master_metode_uji');
        return redirect()->back()->with('success', count($ids) . ' data berhasil dihapus.');
    }
}
