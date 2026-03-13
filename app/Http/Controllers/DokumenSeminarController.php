<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DokumenSeminar;
use App\Models\Notifikasi;
use App\Models\User;

class DokumenSeminarController extends Controller
{
    /**
     * Tampilkan daftar dokumen seminar per modul (pelaporan / evaluasi)
     */
    public function index(string $modul)
    {
        $user = Auth::user();

        $query = DokumenSeminar::with('user')
            ->where('jenis_modul', $modul)
            ->latest();

        // BKHIT hanya lihat milik sendiri
        if ($user->isBkhit()) {
            $query->where('user_id', $user->id);
        }
        // BBKHIT lihat milik sendiri + unit bawah koordinasi
        elseif ($user->isBbkhit()) {
            $bkhitIds = User::where('parent_id', $user->id)->pluck('id')->push($user->id);
            $query->whereIn('user_id', $bkhitIds);
        }
        // Pusat: lihat semua

        $dokumens = $query->paginate(15)->withQueryString();
        $judulModul = $modul === 'pelaporan' ? 'Pelaporan' : 'Evaluasi';

        return view('dokumen_seminar.index', compact('dokumens', 'modul', 'judulModul'));
    }

    /**
     * Upload dokumen baru
     */
    public function store(Request $request, string $modul)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'file_upload' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:20480',
            'keterangan'  => 'nullable|string|max:1000',
        ], [
            'file_upload.max'   => 'Ukuran file maksimal 20 MB.',
            'file_upload.mimes' => 'Format file harus PDF, Word, Excel, PowerPoint, ZIP, atau RAR.',
        ]);

        $file = $request->file('file_upload');
        $namaFile = $file->getClientOriginalName();
        $ukuran   = $this->formatBytes($file->getSize());
        $path     = $file->store("seminar/{$modul}", 'public');

        $doc = DokumenSeminar::create([
            'user_id'    => Auth::id(),
            'jenis_modul'=> $modul,
            'judul'      => $request->judul,
            'nama_file'  => $namaFile,
            'path_file'  => $path,
            'ukuran_file'=> $ukuran,
            'keterangan' => $request->keterangan,
        ]);

        // ── Kirim Notifikasi ke BBKHIT & Pusat ───────────────────────────
        $pengirim   = Auth::user();
        $judulModul = $modul === 'pelaporan' ? 'Pelaporan' : 'Evaluasi';
        $judulNotif = "📄 Dokumen {$judulModul} Baru dari {$pengirim->name}";
        $pesanNotif = "{$pengirim->name} mengunggah dokumen \"{$request->judul}\" pada modul {$judulModul}.";
        $urlNotif   = route('seminar.index', $modul);

        // Kumpulkan penerima: BBKHIT koordinator + semua Pusat
        $penerima = collect();

        if ($pengirim->parent_id) {
            $penerima->push($pengirim->parent_id);
        }

        User::where('role', 'pusat')
            ->pluck('id')
            ->each(fn($id) => $penerima->push($id));

        Notifikasi::kirim(
            $penerima->unique()->values()->toArray(),
            "upload_{$modul}",
            $judulNotif,
            $pesanNotif,
            $urlNotif,
            $pengirim->id
        );
        // ─────────────────────────────────────────────────────────────────

        return redirect()->route('seminar.index', $modul)
            ->with('success', "Dokumen {$judulModul} berhasil diunggah!");
    }

    /**
     * Download / Lihat file
     */
    public function download($id)
    {
        $dok = DokumenSeminar::findOrFail($id);
        $path = storage_path('app/public/' . $dok->path_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($path, $dok->nama_file);
    }

    /**
     * Hapus dokumen
     */
    public function destroy($id)
    {
        $dok = DokumenSeminar::findOrFail($id);
        $user = Auth::user();

        // Pastikan hanya pemilik atau Pusat yang bisa hapus
        if (!$user->isPusat() && $dok->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus dokumen ini.');
        }

        $modul = $dok->jenis_modul;
        $dok->delete();

        return redirect()->route('seminar.index', $modul)
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2)    . ' KB';
        return $bytes . ' B';
    }
}
