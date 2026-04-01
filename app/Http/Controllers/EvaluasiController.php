<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\Evaluasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    // Daftar semua evaluasi (scoped by role)
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Evaluasi::with(['perencanaan.user'])->latest();

        // Scope berdasarkan role
        if ($user->isBkhit()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $query->whereHas('perencanaan', function ($q) use ($user) {
                $q->whereIn('user_id', function ($rq) use ($user) {
                    $rq->select('id')->from('users')
                       ->where('id', $user->id)
                       ->orWhere('parent_id', $user->id);
                });
            });
        }
        // Pusat: lihat semua

        // Filter kesimpulan
        if ($request->filled('kesimpulan')) {
            $query->where('kesimpulan', $request->kesimpulan);
        }

        // Filter status warna
        if ($request->filled('warna')) {
            $query->where('status_warna', $request->warna);
        }

        // Search wilayah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('perencanaan', function ($q) use ($search) {
                $q->where('provinsi', 'like', "%{$search}%")
                  ->orWhere('kab_kota', 'like', "%{$search}%");
            });
        }

        $evaluasis = $query->paginate(15)->withQueryString();

        return view('evaluasi.index', compact('evaluasis'));
    }

    // Form evaluasi untuk perencanaan tertentu
    public function create($id)
    {
        $perencanaan = Perencanaan::with(['pelaksanaans.laboratorium'])->findOrFail($id);

        // Validasi: harus ada minimal 1 pelaksanaan
        if ($perencanaan->pelaksanaans->isEmpty()) {
            return redirect()->route('perencanaan.show', $id)
                ->with('error', 'Belum ada data pelaksanaan lapangan. Tambahkan pelaksanaan terlebih dahulu sebelum membuat evaluasi.');
        }

        // Validasi: semua pelaksanaan harus sudah selesai uji laboratorium
        $belumSelesaiLab = $perencanaan->pelaksanaans->filter(fn($p) => !$p->laboratorium)->count();
        if ($belumSelesaiLab > 0) {
            return redirect()->route('perencanaan.show', $id)
                ->with('warning', "Masih ada {$belumSelesaiLab} pelaksanaan yang belum selesai pengujian laboratorium. Selesaikan seluruh pengujian lab terlebih dahulu sebelum menetapkan evaluasi.");
        }

        return view('evaluasi.create', compact('perencanaan'));
    }

    // Simpan hasil evaluasi
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_id'  => 'required|exists:perencanaans,id',
            'kesimpulan'      => 'required',
            'status_warna'    => 'required',
            'tanggal_evaluasi'=> 'required|date',
            'evaluator'       => 'required',
        ]);

        $perencanaan = Perencanaan::with('pelaksanaans.laboratorium')->findOrFail($request->perencanaan_id);

        // Defense in depth — validasi ulang sebelum simpan
        if ($perencanaan->pelaksanaans->isEmpty()) {
            return back()->with('error', 'Belum ada data pelaksanaan. Evaluasi tidak dapat disimpan.');
        }

        $belumSelesaiLab = $perencanaan->pelaksanaans->filter(fn($p) => !$p->laboratorium)->count();
        if ($belumSelesaiLab > 0) {
            return back()->with('warning', "Masih ada {$belumSelesaiLab} pelaksanaan yang belum selesai pengujian laboratorium.");
        }

        // Simpan evaluasi dengan hanya input tervalidasi (Mencegah Mass Assignment)
        Evaluasi::create($request->only([
            'perencanaan_id', 'kesimpulan', 'status_warna', 'tanggal_evaluasi',
            'evaluator', 'prevalensi', 'insidensi', 'rekomendasi'
        ]));

        return redirect()->route('perencanaan.show', $request->perencanaan_id)
            ->with('success', 'Evaluasi Akhir berhasil ditetapkan!');
    }
}
