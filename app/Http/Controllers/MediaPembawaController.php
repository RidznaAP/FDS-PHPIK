<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaPembawa;
use App\Exports\MediaPembawaExport;
use App\Imports\MediaPembawaImport;
use Maatwebsite\Excel\Facades\Excel;

class MediaPembawaController extends Controller
{
    public function index(Request $request)
    {
        $sort      = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');
        $search    = $request->get('q');

        $query = MediaPembawa::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $items = $query->orderBy($sort, $direction)->paginate(50)->withQueryString();
        
        return view('master.media_pembawa.index', compact('items'));
    }

    public function create()
    {
        return view('master.media_pembawa.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:200|unique:media_pembawas,nama',
            'keterangan'=> 'nullable|string|max:500',
            'aktif'     => 'boolean',
        ]);

        MediaPembawa::create([
            'nama'       => $request->nama,
            'keterangan' => $request->keterangan,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        cache()->forget('master_media_pembawa');

        return redirect()->route('master.media-pembawa.index')
            ->with('success', '"' . $request->nama . '" berhasil ditambahkan ke Master Data.');
    }

    public function edit(MediaPembawa $mediaPembawa)
    {
        return view('master.media_pembawa.form', ['item' => $mediaPembawa]);
    }

    public function update(Request $request, MediaPembawa $mediaPembawa)
    {
        $request->validate([
            'nama'      => 'required|string|max:200|unique:media_pembawas,nama,' . $mediaPembawa->id,
            'keterangan'=> 'nullable|string|max:500',
            'aktif'     => 'boolean',
        ]);

        $mediaPembawa->update([
            'nama'       => $request->nama,
            'keterangan' => $request->keterangan,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        cache()->forget('master_media_pembawa');

        return redirect()->route('master.media-pembawa.index')
            ->with('success', '"' . $mediaPembawa->nama . '" berhasil diperbarui.');
    }

    public function destroy(MediaPembawa $mediaPembawa)
    {
        $nama = $mediaPembawa->nama;
        $mediaPembawa->delete();
        cache()->forget('master_media_pembawa');
        return redirect()->route('master.media-pembawa.index')
            ->with('success', '"' . $nama . '" berhasil dihapus dari Master Data.');
    }

    public function export()
    {
        return Excel::download(new MediaPembawaExport(), 'master_media_pembawa_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MediaPembawaExport(true), 'template_import_media_pembawa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MediaPembawaImport, $request->file('file'));
            cache()->forget('master_media_pembawa');
            return redirect()->back()->with('success', 'Data Media Pembawa berhasil diimpor!');
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

        MediaPembawa::whereIn('id', $ids)->delete();
        cache()->forget('master_media_pembawa');
        return redirect()->back()->with('success', count($ids) . ' data berhasil dihapus.');
    }
}
