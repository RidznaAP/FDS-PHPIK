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
     * Tampilkan daftar dokumen seminar per modul (pelaporan / evaluasi / pelaksanaan_pasif)
     */
    public function index(string $modul)
    {
        $user = Auth::user();

        $query = DokumenSeminar::with(['user', 'targetUser'])
            ->where('jenis_modul', $modul)
            ->latest();

        if ($modul === 'pelaporan') {
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
        } elseif ($modul === 'evaluasi') {
            // Evaluasi: Pusat lihat semua.
            // BBKHIT: Lihat semua (karena dia juga pengunggah)
            // BKHIT: Lihat jika ditujukan padanya atau ke Semua (null)
            if ($user->isBkhit()) {
                $query->where(function ($q) use ($user) {
                    $q->whereNull('target_user_id')
                      ->orWhere('target_user_id', $user->id);
                });
            }
        } elseif ($modul === 'pelaksanaan_pasif') {
            // Pelaksanaan Pasif: BKHIT hanya lihat milik sendiri
            if ($user->isBkhit()) {
                $query->where('user_id', $user->id);
            }
            // BBKHIT lihat milik sendiri + unit bawah koordinasi
            elseif ($user->isBbkhit()) {
                $bkhitIds = User::where('parent_id', $user->id)->pluck('id')->push($user->id);
                $query->whereIn('user_id', $bkhitIds);
            }
            // Pusat: lihat semua
        }

        $dokumens = $query->paginate(15)->withQueryString();
        $judulModul = match($modul) {
            'pelaporan'        => 'Pelaporan',
            'evaluasi'         => 'Evaluasi',
            'pelaksanaan_pasif' => 'Pelaksanaan Pasif',
            default            => ucfirst($modul),
        };
        
        $uptUsers = collect();
        if ($modul === 'evaluasi') {
            $uptUsers = User::where('role', '!=', 'pusat')->orderBy('name')->get();
        }

        return view('dokumen_seminar.index', compact('dokumens', 'modul', 'judulModul', 'uptUsers'));
    }

    /**
     * Upload dokumen baru
     */
    public function store(Request $request, string $modul)
    {
        $user = Auth::user();

        // Semua admin (Pusat, BBKHIT, BKHIT) sekarang diizinkan mengunggah dokumen
        // sesuai permintaan untuk "semua admin".

        $request->validate([
            'judul'          => 'required|string|max:255',
            'file_upload'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:2048',
            'link_drive'     => 'nullable|url|max:1000',
            'keterangan'     => 'nullable|string|max:1000',
            'target_user_id' => 'nullable|exists:users,id',
        ], [
            'file_upload.max'   => 'Ukuran file maksimal 2 MB.',
            'file_upload.mimes' => 'Format file harus PDF, Word, Excel, PowerPoint, ZIP, atau RAR.',
            'link_drive.url'    => 'Format link Google Drive tidak valid.',
        ]);

        if (!$request->hasFile('file_upload') && !$request->link_drive) {
            return back()->withErrors(['file_upload' => 'Harap unggah file (maks 2MB) atau berikan link Google Drive sebagai alternatif.'])->withInput();
        }

        // ── Validasi File Ekstra Ketat (Anti-Malware / Virus Scan) ───────────
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $originalName = $file->getClientOriginalName();
            
            // 1. Cek Double Extension (Mencegah file.php.pdf)
            if (substr_count($originalName, '.') > 1) {
                // Kecuali ekstensi yang umum seperti .tar.gz (tidak kita izinkan di mimes, jadi aman)
                return back()->withErrors(['file_upload' => 'Keamanan: Nama file mengandung ekstensi ganda yang mencurigakan.'])->withInput();
            }

            // 2. Cek Signature File (Mencegah Executable menyamar jadi dokumen)
            $filePath = $file->getRealPath();
            if (file_exists($filePath)) {
                $handle = fopen($filePath, 'rb');
                $bytes = fread($handle, 4);
                fclose($handle);

                $hex = bin2hex($bytes);
                // "4d5a" = MZ (Windows EXE), "7f454c46" = ELF (Linux Executable)
                if (str_starts_with($hex, '4d5a') || str_starts_with($hex, '7f454c46')) {
                    // Log deteksi malware (bisa dikembangkan ke Audit Log)
                    \App\Models\ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'Deteksi Ancaman',
                        'model' => 'Sistem Keamanan',
                        'model_id' => 0,
                        'new_value' => "Mencoba mengunggah executable menyamar: {$originalName}",
                        'ip' => $request->ip()
                    ]);
                    
                    return back()->withErrors(['file_upload' => 'Keamanan Sistem: File terdeteksi mengandung signature executable berbahaya (Malware/Virus). Akses ditolak.'])->withInput();
                }
            }
        }
        // ─────────────────────────────────────────────────────────────────

        $namaFile = null;
        $ukuran   = null;
        $path     = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $namaFile = $file->getClientOriginalName();
            $ukuran   = $this->formatBytes($file->getSize());
            $path     = $file->store("seminar/{$modul}", 'public');
        }

        $doc = DokumenSeminar::create([
            'user_id'        => $user->id,
            'target_user_id' => $modul === 'evaluasi' ? $request->target_user_id : null,
            'jenis_modul'    => $modul,
            'judul'          => $request->judul,
            'nama_file'      => $namaFile,
            'path_file'      => $path,
            'ukuran_file'    => $ukuran,
            'link_drive'     => $request->link_drive,
            'keterangan'     => $request->keterangan,
        ]);

        // ── Kirim Notifikasi ───────────────────────────
        $judulModul = match($modul) {
            'pelaporan'        => 'Pelaporan',
            'evaluasi'         => 'Evaluasi',
            'pelaksanaan_pasif' => 'Pelaksanaan Pasif',
            default            => ucfirst($modul),
        };
        $judulNotif = "📄 Dokumen {$judulModul} Baru dari {$user->name}";
        
        $targetMsg = "";
        if ($modul === 'evaluasi' && $request->target_user_id) {
            $targetUser = User::find($request->target_user_id);
            $targetMsg = " (Ditujukan untuk " . ($targetUser ? $targetUser->name : 'UPT') . ")";
        }
        $tipeUpload = $path ? "berkas file" : "link Google Drive";
        $pesanNotif = "{$user->name} mengunggah dokumen \"{$request->judul}\" ({$tipeUpload}) pada modul {$judulModul}.{$targetMsg}";
        $urlNotif   = route('seminar.index', $modul);

        $penerima = collect();

        if ($modul === 'pelaporan' || $modul === 'pelaksanaan_pasif') {
            // Notifikasi ke BBKHIT Koordinator & Semua Pusat
            if ($user->parent_id) {
                $penerima->push($user->parent_id);
            }
            User::where('role', 'pusat')->pluck('id')->each(fn($id) => $penerima->push($id));
        } else {
            // Modul Evaluasi: Notifikasi ke target UPT, atau semua UPT jika null
            if ($request->target_user_id) {
                $penerima->push($request->target_user_id);
            } else {
                User::where('role', '!=', 'pusat')->pluck('id')->each(fn($id) => $penerima->push($id));
            }
        }

        if ($penerima->isNotEmpty()) {
            Notifikasi::kirim(
                $penerima->unique()->values()->toArray(),
                "upload_{$modul}",
                $judulNotif,
                $pesanNotif,
                $urlNotif,
                $user->id
            );
        }
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
        
        if ($dok->path_file) {
            $path = storage_path('app/public/' . $dok->path_file);
            if (!file_exists($path)) {
                return back()->with('error', 'File fisik tidak ditemukan di server.');
            }
            return response()->download($path, $dok->nama_file);
        }

        if ($dok->link_drive) {
            return redirect()->away($dok->link_drive);
        }

        return back()->with('error', 'Dokumen tidak memiliki file maupun link.');
    }

    /**
     * Hapus dokumen
     */
    public function destroy($id)
    {
        $dok = DokumenSeminar::findOrFail($id);
        $user = Auth::user();

        // Pastikan hanya pemilik, Pusat, atau Developer yang bisa hapus
        if (!$user->isPusat() && !$user->isDeveloper() && $dok->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus dokumen ini.');
        }

        $modul = $dok->jenis_modul;
        $dok->delete();

        // Bersihkan notifikasi terkait (jika ada yang spesifik)
        Notifikasi::where('pesan', 'like', "%{$dok->judul}%")->delete();

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
