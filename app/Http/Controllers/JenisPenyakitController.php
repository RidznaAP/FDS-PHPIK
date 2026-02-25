<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisPenyakit;
use App\Exports\JenisPenyakitExport;
use App\Imports\JenisPenyakitImport;
use Maatwebsite\Excel\Facades\Excel;

class JenisPenyakitController extends Controller
{
    public function index(Request $request)
    {
        $sort      = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');
        $search    = $request->get('q');

        $query = JenisPenyakit::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('organisme_penyebab', 'like', "%{$search}%");
        }

        $items = $query->orderBy($sort, $direction)->paginate(50)->withQueryString();

        return view('master.jenis_penyakit.index', compact('items'));
    }

    public function create()
    {
        return view('master.jenis_penyakit.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'               => 'required|string|max:200|unique:jenis_penyakits,nama',
            'organisme_penyebab' => 'nullable|string|max:200',
            'golongan'           => 'required|string|in:Virus,Bakteri,Parasit,Jamur,Lainnya',
            'keterangan'         => 'nullable|string|max:500',
            'aktif'              => 'boolean',
        ]);

        JenisPenyakit::create([
            'nama'               => $request->nama,
            'organisme_penyebab' => $request->organisme_penyebab,
            'golongan'           => $request->golongan,
            'keterangan'         => $request->keterangan,
            'aktif'              => $request->boolean('aktif', true),
        ]);

        return redirect()->route('master.jenis-penyakit.index')
            ->with('success', '"' . $request->nama . '" berhasil ditambahkan ke Master Data.');
    }

    public function edit(JenisPenyakit $jenisPenyakit)
    {
        return view('master.jenis_penyakit.form', ['item' => $jenisPenyakit]);
    }

    public function update(Request $request, JenisPenyakit $jenisPenyakit)
    {
        $request->validate([
            'nama'               => 'required|string|max:200|unique:jenis_penyakits,nama,' . $jenisPenyakit->id,
            'organisme_penyebab' => 'nullable|string|max:200',
            'golongan'           => 'required|string|in:Virus,Bakteri,Parasit,Jamur,Lainnya',
            'keterangan'         => 'nullable|string|max:500',
            'aktif'              => 'boolean',
        ]);

        $jenisPenyakit->update([
            'nama'               => $request->nama,
            'organisme_penyebab' => $request->organisme_penyebab,
            'golongan'           => $request->golongan,
            'keterangan'         => $request->keterangan,
            'aktif'              => $request->boolean('aktif', true),
        ]);

        return redirect()->route('master.jenis-penyakit.index')
            ->with('success', '"' . $jenisPenyakit->nama . '" berhasil diperbarui.');
    }

    public function destroy(JenisPenyakit $jenisPenyakit)
    {
        $nama = $jenisPenyakit->nama;
        $jenisPenyakit->delete();
        return redirect()->route('master.jenis-penyakit.index')
            ->with('success', '"' . $nama . '" berhasil dihapus dari Master Data.');
    }

    public function export()
    {
        return Excel::download(new JenisPenyakitExport(), 'master_jenis_penyakit_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new JenisPenyakitExport(true), 'template_import_jenis_penyakit.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new JenisPenyakitImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Jenis Penyakit berhasil diimpor!');
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

        JenisPenyakit::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', count($ids) . ' data berhasil dihapus.');
    }
}
