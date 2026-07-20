<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\RegulasiInformasi;

class RegulasiInformasiController extends Controller
{
    /**
     * Tampilkan semua regulasi informasi (semua role bisa melihat)
     */
    public function index()
    {
        $regulasis = RegulasiInformasi::with('user')
            ->latest()
            ->paginate(12);

        return view('regulasi_informasi.index', compact('regulasis'));
    }

    /**
     * Form tambah regulasi (hanya Pusat & Developer — dicek di route middleware)
     */
    public function create()
    {
        return view('regulasi_informasi.create');
    }

    /**
     * Simpan regulasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'required|string|max:5000',
            'file_upload' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
            ],
        ], [
            'judul.required'       => 'Judul informasi wajib diisi.',
            'deskripsi.required'   => 'Deskripsi wajib diisi.',
            'file_upload.max'      => 'Ukuran file maksimal 2 MB.',
            'file_upload.mimes'    => 'Format file: JPG, PNG, GIF, PDF, Word, Excel, PowerPoint, ZIP, RAR.',
        ]);

        $user = Auth::user();
        $tipe = 'none';
        $namaFile = null;
        $ukuran   = null;
        $path     = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');

            // ── Keamanan: cek double extension ──────────────
            $originalName = $file->getClientOriginalName();
            if (substr_count($originalName, '.') > 1) {
                return back()->withErrors(['file_upload' => 'Keamanan: Nama file mengandung ekstensi ganda yang mencurigakan.'])->withInput();
            }

            // ── Keamanan: cek file signature ─────────────────
            $filePath = $file->getRealPath();
            if (file_exists($filePath)) {
                $handle = fopen($filePath, 'rb');
                $bytes  = fread($handle, 4);
                fclose($handle);
                $hex = bin2hex($bytes);
                if (str_starts_with($hex, '4d5a') || str_starts_with($hex, '7f454c46')) {
                    return back()->withErrors(['file_upload' => 'Keamanan: File terdeteksi mengandung signature executable berbahaya.'])->withInput();
                }
            }
            // ─────────────────────────────────────────────────

            // Tentukan tipe berdasarkan MIME
            $mime = $file->getMimeType();
            $tipe = str_starts_with($mime, 'image/') ? 'foto' : 'dokumen';

            $namaFile = $originalName;
            $ukuran   = $this->formatBytes($file->getSize());
            $path     = $file->store('regulasi_informasi', 'public');
        }

        RegulasiInformasi::create([
            'user_id'       => $user->id,
            'judul'         => $request->judul,
            'deskripsi'     => $request->deskripsi,
            'tipe_lampiran' => $tipe,
            'nama_file'     => $namaFile,
            'path_file'     => $path,
            'ukuran_file'   => $ukuran,
        ]);

        return redirect()->route('regulasi.index')
            ->with('success', 'Informasi regulasi berhasil dipublikasikan!');
    }

    /**
     * Download / Lihat file lampiran
     */
    public function download($id)
    {
        $regulasi = RegulasiInformasi::findOrFail($id);

        if (!$regulasi->path_file) {
            return back()->with('error', 'Regulasi ini tidak memiliki lampiran file.');
        }

        $storagePath = storage_path('app/public/' . $regulasi->path_file);

        if (!file_exists($storagePath)) {
            return back()->with('error', 'File fisik tidak ditemukan di server.');
        }

        return response()->download($storagePath, $regulasi->nama_file);
    }

    /**
     * Hapus regulasi beserta file-nya
     */
    public function destroy($id)
    {
        $user     = Auth::user();
        $regulasi = RegulasiInformasi::findOrFail($id);

        // Hanya pemilik, Pusat, atau Developer yang bisa hapus
        if (!$user->isPusat() && !$user->isDeveloper() && $regulasi->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus regulasi ini.');
        }

        $regulasi->delete(); // Model boot() otomatis hapus file fisik

        return redirect()->route('regulasi.index')
            ->with('success', 'Informasi regulasi berhasil dihapus.');
    }

    /**
     * Helper: format ukuran bytes
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
