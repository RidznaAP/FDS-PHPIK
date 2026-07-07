<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

use App\Models\MediaPembawa;
use App\Models\JenisPenyakit;
use App\Models\User;
use App\Exports\PerencanaanExport;
use App\Imports\PerencanaanImport;
use App\Http\Requests\StorePerencanaanRequest;
use App\Http\Requests\UpdatePerencanaanRequest;
use Maatwebsite\Excel\Facades\Excel;

class PerencanaanController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Perencanaan::class, 'perencanaan', [
            'except' => ['index', 'create', 'show', 'store'],
        ]);
    }

    // ── #1: Daftar Perencanaan (dengan search, filter status & tahun) ───────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Perencanaan::class);

        $user = Auth::user();
        $query = Perencanaan::with(['evaluasi', 'pelaksanaans', 'user']);

        // ── Sorting ──────────────────────────────────────────────────────────
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'created_at', 'provinsi', 'kab_kota', 'jenis_mp', 'jenis_hpik', 'target_uji', 'status'];
        in_array($sortBy, $allowedSorts) ? $query->orderBy($sortBy, $sortOrder) : $query->latest();

        // ── Auth-scoped Filtering ─────────────────────────────────────────────
        if ($user->isBkhit()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $query->whereIn('user_id', function ($q) use ($user) {
                $q->select('id')->from('users')
                  ->where('id', $user->id)
                  ->orWhere('parent_id', $user->id);
            });
        }
        // PUSAT tidak difilter (lihat semua)

        // ── Filter Tahun ──────────────────────────────────────────────────────
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // ── Filter Status ─────────────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ── Search ────────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('provinsi', 'like', "%{$search}%")
                  ->orWhere('kab_kota', 'like', "%{$search}%")
                  ->orWhere('jenis_mp', 'like', "%{$search}%")
                  ->orWhere('jenis_hpik', 'like', "%{$search}%");
            });
        }

        // ── Dropdown Tahun (role-scoped) ────────────────────────────────────
        $yearQuery = Perencanaan::selectRaw('YEAR(created_at) as tahun');
        if ($user->isBkhit()) {
            $yearQuery->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $yearQuery->whereIn('user_id', function ($q) use ($user) {
                $q->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }
        $years = $yearQuery->groupBy('tahun')->orderByDesc('tahun')->pluck('tahun');

        $limit = $request->get('view') == 'board' ? 100 : 15;
        $perencanaans = $query->paginate($limit)->withQueryString();

        return view('perencanaan.index', compact('perencanaans', 'years'));
    }

    // ── #2: Form Tambah ───────────────────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Perencanaan::class);

        $mediaPembawas  = cache()->remember('master_media_pembawa', 86400, fn () => MediaPembawa::aktif()->orderBy('nama')->get());
        $jenisPenyakits = cache()->remember('master_jenis_penyakit', 86400, fn () => JenisPenyakit::aktif()->orderBy('nama')->get());
        $metodeUjis    = cache()->remember('master_metode_uji', 86400, fn () => \App\Models\MetodeUji::aktif()->orderBy('nama')->get());

        return view('perencanaan.create', compact('mediaPembawas', 'jenisPenyakits', 'metodeUjis'));
    }

    // ── #3: Simpan ke Database ────────────────────────────────────────────────
    public function store(StorePerencanaanRequest $request)
    {
        $this->authorize('create', Perencanaan::class);

        $user  = Auth::user();
        $total = (int) $request->tw1 + (int) $request->tw2 + (int) $request->tw3 + (int) $request->tw4;

        // Force provinsi untuk non-Pusat/non-Developer
        $provinsi = ($user->isPusat() || $user->isDeveloper()) ? $request->provinsi : ($user->upt_asal ?: $request->provinsi);

        Perencanaan::create([
            'user_id'                 => Auth::id(),
            'provinsi'                => $provinsi,
            'kab_kota'                => $request->kab_kota,
            'jenis_mp'                => $request->jenis_mp,
            'jenis_hpik'              => implode(', ', (array) $request->jenis_hpik),
            'kemampuan_uji_upt'       => is_array($request->kemampuan_uji_upt) ? implode(', ', $request->kemampuan_uji_upt) : ($request->kemampuan_uji_upt ?? 'Tersedia'),
            'metode_pengujian'        => implode(', ', (array) $request->metode_pengujian),
            'lab_uji'                 => $request->lab_uji,
            'target_uji'              => $request->target_uji ?? $total,
            'tw1'                     => $request->tw1 ?? 0,
            'tw2'                     => $request->tw2 ?? 0,
            'tw3'                     => $request->tw3 ?? 0,
            'tw4'                     => $request->tw4 ?? 0,
            'total_pengujian'         => $total,
            'rencana_lokasi'          => $request->rencana_lokasi,
            'rencana_jumlah_sampel'   => $request->rencana_jumlah_sampel ?? 0,
            'rencana_metode_sampling' => $request->rencana_metode_sampling,
            'status'                  => 'draft',
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // ── #4: Detail Perencanaan ────────────────────────────────────────────────
    public function show(Perencanaan $perencanaan)
    {
        $p = $perencanaan->load(['user', 'pelaksanaans.laboratorium', 'evaluasi']);
        return view('perencanaan.show', compact('p'));
    }

    // ── #5: Form Edit (hanya Draft, hanya pemilik) ────────────────────────────
    public function edit(Perencanaan $perencanaan)
    {
        $mediaPembawas  = cache()->remember('master_media_pembawa', 86400, fn () => MediaPembawa::aktif()->orderBy('nama')->get());
        $jenisPenyakits = cache()->remember('master_jenis_penyakit', 86400, fn () => JenisPenyakit::aktif()->orderBy('nama')->get());
        $metodeUjis    = cache()->remember('master_metode_uji', 86400, fn () => \App\Models\MetodeUji::aktif()->orderBy('nama')->get());

        return view('perencanaan.edit', compact('perencanaan', 'mediaPembawas', 'jenisPenyakits', 'metodeUjis'));
    }

    // ── #6: Update ────────────────────────────────────────────────────────────
    public function update(UpdatePerencanaanRequest $request, Perencanaan $perencanaan)
    {

        $user  = Auth::user();
        $total = (int) $request->tw1 + (int) $request->tw2 + (int) $request->tw3 + (int) $request->tw4;

        $provinsi = ($user->isPusat() || $user->isDeveloper()) ? $request->provinsi : ($user->upt_asal ?: $perencanaan->provinsi);

        $perencanaan->update([
            'provinsi'                => $provinsi,
            'kab_kota'                => $request->kab_kota,
            'jenis_mp'                => $request->jenis_mp,
            'jenis_hpik'              => implode(', ', (array) $request->jenis_hpik),
            'kemampuan_uji_upt'       => is_array($request->kemampuan_uji_upt) ? implode(', ', $request->kemampuan_uji_upt) : ($request->kemampuan_uji_upt ?? 'Tersedia'),
            'metode_pengujian'        => implode(', ', (array) $request->metode_pengujian),
            'lab_uji'                 => $request->lab_uji,
            'target_uji'              => $request->target_uji ?? $total,
            'tw1'                     => $request->tw1 ?? 0,
            'tw2'                     => $request->tw2 ?? 0,
            'tw3'                     => $request->tw3 ?? 0,
            'tw4'                     => $request->tw4 ?? 0,
            'total_pengujian'         => $total,
            'rencana_lokasi'          => $request->rencana_lokasi,
            'rencana_jumlah_sampel'   => $request->rencana_jumlah_sampel ?? 0,
            'rencana_metode_sampling' => $request->rencana_metode_sampling,
        ]);

        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil diperbarui!');
    }

    // ── #7: Hapus (hanya Draft, hanya pemilik / Pusat) ───────────────────────
    public function destroy(Perencanaan $perencanaan)
    {

        $id = $perencanaan->id;
        $perencanaan->delete();

        // Bersihkan notifikasi terkait
        Notifikasi::hapusTerkaitUrl("/perencanaan/{$id}");

        return redirect()->route('perencanaan.index')->with('success', 'Perencanaan berhasil dihapus.');
    }

    // ── #8: Export ────────────────────────────────────────────────────────────
    public function export()
    {
        return Excel::download(new PerencanaanExport(), 'perencanaan_hpik_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PerencanaanExport(true), 'template_import_perencanaan.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv. Pastikan menggunakan template yang diunduh dari sistem.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = new PerencanaanImport;
            Excel::import($import, $request->file('file'));

            $hasFailures = count($import->failures) > 0 || count($import->errors) > 0;

            // Cek apakah ada data yang berhasil masuk
            $newTotal = \App\Models\Perencanaan::count();

            if ($hasFailures) {
                $detail = $import->getFailureSummary();
                return redirect()->back()->with('warning',
                    'Import selesai dengan peringatan. Beberapa baris dilewati. ' .
                    ($detail ? "Detail: {$detail}" : 'Pastikan file menggunakan template yang benar (Unduh Template).')
                );
            }

            return redirect()->back()->with('success', 'Semua Data Perencanaan berhasil diimpor!');
        } catch (\Exception $e) {
            \Log::error('PerencanaanImport error: ' . $e->getMessage());
            return redirect()->back()->with('error',
                'Gagal mengimpor data. Pastikan file menggunakan template yang diunduh dari sistem. Error: ' . $e->getMessage()
            );
        }
    }

    // ── #9: Bulk Delete ───────────────────────────────────────────────────────
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang akan dihapus.');
        }

        $user  = Auth::user();
        $query = Perencanaan::whereIn('id', $ids);

        if (!$user->isPusat() && !$user->isDeveloper()) {
            $query->where('user_id', Auth::id())->where('status', 'draft');
        }

        $count = $query->delete();

        if ($count > 0) {
            foreach ($ids as $id) {
                Notifikasi::hapusTerkaitUrl("/perencanaan/{$id}");
            }
        }

        if ($count === 0) {
            return back()->with('error', 'Tidak ada data yang diizinkan untuk dihapus (Pastikan status masih Draft).');
        }

        return back()->with('success', $count . ' data perencanaan berhasil dihapus.');
    }

    // ── #10: Submit (BKHIT ajukan validasi: draft → waiting) ─────────────────
    public function submit(Perencanaan $perencanaan)
    {
        $this->authorize('submit', $perencanaan);

        $user = Auth::user();
        $oldStatus = $perencanaan->status;
        $perencanaan->update(['status' => 'waiting']);

        \App\Models\ActivityLog::create([
            'user_id'   => $user->id,
            'action'    => 'Submit',
            'model'     => 'Perencanaan',
            'model_id'  => $perencanaan->id,
            'old_value' => json_encode(['status' => $oldStatus]),
            'new_value' => json_encode(['status' => 'waiting']),
            'ip'        => request()->ip(),
        ]);

        // Kirim notifikasi ke BBKHIT koordinator & semua Admin Pusat
        $penerima = collect();
        if ($user->parent_id) {
            $penerima->push($user->parent_id);
        }
        User::where('role', 'pusat')->pluck('id')->each(fn($id) => $penerima->push($id));

        if ($penerima->isNotEmpty()) {
            Notifikasi::kirim(
                $penerima->unique()->values()->toArray(),
                'submit_perencanaan',
                "📋 Pengajuan Perencanaan Baru",
                "{$user->name} mengajukan perencanaan #{$perencanaan->id} ({$perencanaan->kab_kota}, {$perencanaan->provinsi}) untuk validasi.",
                route('perencanaan.show', $perencanaan->id),
                $user->id
            );
        }

        return back()->with('success', 'Perencanaan berhasil diajukan untuk validasi.');
    }

    // ── #11: Approve (BBKHIT/Pusat menyetujui: waiting → approved, Pusat juga bisa dari draft) ──
    public function approve($id)
    {
        $perencanaan = Perencanaan::findOrFail($id);
        $this->authorize('approve', $perencanaan);

        $user = Auth::user();

        // Pusat/Developer bisa approve dari status draft langsung (bypass waiting)
        $allowedStatuses = ($user->isPusat() || $user->isDeveloper()) ? ['waiting', 'draft'] : ['waiting'];

        if (!in_array($perencanaan->status, $allowedStatuses)) {
            return back()->with('error', 'Perencanaan tidak bisa disetujui dari status saat ini.');
        }

        $oldStatus = $perencanaan->status;
        $perencanaan->update(['status' => 'approved', 'alasan_penolakan' => null]);

        \App\Models\ActivityLog::create([
            'user_id'   => $user->id,
            'action'    => 'Approve',
            'model'     => 'Perencanaan',
            'model_id'  => $perencanaan->id,
            'old_value' => json_encode(['status' => $oldStatus]),
            'new_value' => json_encode(['status' => 'approved']),
            'ip'        => request()->ip(),
        ]);

        // Kirim notifikasi ke pemilik perencanaan
        if ($perencanaan->user_id !== $user->id) {
            Notifikasi::kirim(
                $perencanaan->user_id,
                'approve_perencanaan',
                "✅ Perencanaan Disetujui",
                "{$user->name} telah menyetujui perencanaan #{$perencanaan->id} ({$perencanaan->kab_kota}, {$perencanaan->provinsi}).",
                route('perencanaan.show', $perencanaan->id),
                $user->id
            );
        }

        return back()->with('success', 'Perencanaan telah disetujui!');
    }

    // ── #12: Reject (BBKHIT/Pusat menolak: waiting/approved → rejected) ───────
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $perencanaan = Perencanaan::findOrFail($id);
        $this->authorize('reject', $perencanaan);

        if (!in_array($perencanaan->status, ['waiting', 'approved'])) {
            return back()->with('error', 'Perencanaan tidak bisa ditolak karena statusnya tidak valid.');
        }

        $user = Auth::user();
        $oldStatus = $perencanaan->status;
        $perencanaan->update([
            'status'           => 'rejected',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        \App\Models\ActivityLog::create([
            'user_id'   => $user->id,
            'action'    => 'Reject',
            'model'     => 'Perencanaan',
            'model_id'  => $perencanaan->id,
            'old_value' => json_encode(['status' => $oldStatus]),
            'new_value' => json_encode(['status' => 'rejected', 'alasan_penolakan' => $request->alasan_penolakan]),
            'ip'        => filter_var(request()->ip(), FILTER_VALIDATE_IP) ?: null,
        ]);

        // Kirim notifikasi ke pemilik perencanaan
        if ($perencanaan->user_id !== $user->id) {
            Notifikasi::kirim(
                $perencanaan->user_id,
                'reject_perencanaan',
                "❌ Perencanaan Ditolak",
                "{$user->name} menolak perencanaan #{$perencanaan->id} ({$perencanaan->kab_kota}). Alasan: {$request->alasan_penolakan}",
                route('perencanaan.show', $perencanaan->id),
                $user->id
            );
        }

        return back()->with('success', 'Perencanaan telah ditolak beserta alasan penolakannya.');
    }
}