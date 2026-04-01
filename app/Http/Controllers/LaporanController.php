<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\Laboratorium;
use App\Models\User;
use App\Exports\PerencanaanExport;
use App\Exports\PelaksanaanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Halaman pusat pelaporan & ekspor
    public function index()
    {
        $user = Auth::user();

        // Daftar BKHIT unik untuk filter formulir & PDF
        $bkhitListQuery = User::where('role', 'bkhit')
            ->whereNotNull('upt_asal');

        if ($user->isBbkhit()) {
            $bkhitListQuery->where('parent_id', $user->id);
        } elseif ($user->isBkhit()) {
            $bkhitListQuery->where('id', $user->id);
        }

        $bkhitList = $bkhitListQuery->orderBy('upt_asal')
            ->pluck('upt_asal')
            ->unique();

        return view('laporan.index', compact('bkhitList'));
    }

    // Export Excel: Data Perencanaan
    public function exportPerencanaan()
    {
        $filename = 'Laporan_Perencanaan_HPIK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new PerencanaanExport, $filename);
    }

    // Export Excel: Data Pelaksanaan + Laboratorium
    public function exportPelaksanaan()
    {
        $filename = 'Laporan_Pelaksanaan_HPIK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new PelaksanaanExport, $filename);
    }

    // ── #13: Export / Print PDF via browser print ──────────────────────────────
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $query = Perencanaan::with(['pelaksanaans', 'evaluasi', 'user'])->latest();

        // ── Auth-scoped Filtering ──────────────────────────────────────
        if ($user->isBkhit()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isBbkhit()) {
            $query->whereIn('user_id', function($q) use ($user) {
                $q->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }

        // Filter wilayah (#14)
        if ($request->filled('wilayah')) {
            $query->whereHas('user', fn($q) => $q->where('upt_asal', $request->wilayah));
        }
        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $perencanaans = $query->get();
        $totalPerencanaan = $perencanaans->count();

        // Stats calculation needs to be aware of the query results (already filtered)
        $totalPelaksanaan = 0;
        $labDone = 0;
        foreach($perencanaans as $p) {
            $totalPelaksanaan += $p->pelaksanaans->count();
            $labDone += $p->pelaksanaans->whereNotNull('laboratorium')->count();
        }

        $filterWilayah = $request->wilayah ?? 'Semua Wilayah';
        $filterTahun   = $request->tahun   ?? date('Y');

        return view('laporan.pdf', compact(
            'perencanaans', 'totalPerencanaan', 'totalPelaksanaan', 'labDone',
            'filterWilayah', 'filterTahun'
        ));
    }
    // ── Laporan Formulir Hasil Pemantauan HPIK (Sesuai Gambar) ────────────────
    public function exportFormulir(Request $request)
    {
        $user = Auth::user();
        $query = Pelaksanaan::with(['perencanaan.user', 'laboratorium'])->latest();

        // ── Auth-scoped Filtering ──────────────────────────────────────
        if ($user->isBkhit()) {
            $query->whereHas('perencanaan', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->isBbkhit()) {
            $query->whereHas('perencanaan', function($q) use ($user) {
                $q->whereIn('user_id', function($rq) use ($user) {
                    $rq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
                });
            });
        }

        if ($request->filled('wilayah')) {
            $query->whereHas('perencanaan.user', fn($q) => $q->where('upt_asal', $request->wilayah));
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pemantauan', $request->tahun);
        }

        $items = $query->get();
        $namaUpt = $request->wilayah ?? '...................';
        $tahun   = $request->tahun   ?? date('Y');

        return view('laporan.formulir', compact('items', 'namaUpt', 'tahun'));
    }
    // ───────────────────────────────────────────────────────────────────────────
}
