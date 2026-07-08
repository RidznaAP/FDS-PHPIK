@extends('layouts.app')

@section('title', 'Laporan & Ekspor')
@section('page_title', 'Pusat Pelaporan & Ekspor Data')
@section('page_subtitle', 'Export data, cetak laporan, dan unduh peta sebaran HPIK')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- Ringkasan Statistik                                               --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Filter Wilayah / UPT</label>
                        <select name="wilayah" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Wilayah</option>
                            @foreach($bkhitList as $w)
                                <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Filter Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ request('tahun', date('Y')) }}" placeholder="Tahun" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i> Filter</button>
                    </div>
                    @if(request('wilayah') || request('tahun'))
                    <div class="col-md-2">
                        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@php
    $selectedWilayah = request('wilayah');
    $selectedTahun = request('tahun', date('Y'));
    
    $totalP = \App\Models\Perencanaan::query()
        ->when($user->isBkhit(), fn($q) => $q->where('user_id', $user->id))
        ->when($user->isBbkhit(), fn($q) => $q->whereIn('user_id', function($sq) use ($user) {
            $sq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
        }))
        ->when($selectedWilayah, fn($q) => $q->whereHas('user', fn($uq) => $uq->where('upt_asal', $selectedWilayah)))
        ->when($selectedTahun, fn($q) => $q->whereYear('perencanaans.created_at', $selectedTahun))
        ->count();

    $totalPl = \App\Models\Pelaksanaan::query()
        ->when($user->isBkhit(), fn($q) => $q->whereHas('perencanaan', fn($r) => $r->where('user_id', $user->id)))
        ->when($user->isBbkhit(), fn($q) => $q->whereHas('perencanaan', function($r) use ($user) {
            $r->whereIn('user_id', function($sq) use ($user) {
                $sq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }))
        ->when($selectedWilayah, fn($q) => $q->whereHas('perencanaan.user', fn($uq) => $uq->where('upt_asal', $selectedWilayah)))
        ->when($selectedTahun, fn($q) => $q->whereYear('pelaksanaans.tanggal_pemantauan', $selectedTahun))
        ->count();

    $totalLab = \App\Models\Laboratorium::query()
        ->whereHas('perencanaan', function($q) use ($user, $selectedWilayah, $selectedTahun) {
            $q->when($user->isBkhit(), fn($sq) => $sq->where('user_id', $user->id))
              ->when($user->isBbkhit(), fn($sq) => $sq->whereIn('user_id', function($ssq) use ($user) {
                  $ssq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
              }))
              ->when($selectedWilayah, fn($sq) => $sq->whereHas('user', fn($uq) => $uq->where('upt_asal', $selectedWilayah)))
              ->when($selectedTahun, fn($sq) => $sq->whereYear('perencanaans.created_at', $selectedTahun));
        })->count();

    $totalPeta = \App\Models\Pelaksanaan::query()
        ->whereNotNull('latitude')->whereNotNull('longitude')
        ->when($user->isBkhit(), fn($q) => $q->whereHas('perencanaan', fn($r) => $r->where('user_id', $user->id)))
        ->when($user->isBbkhit(), fn($q) => $q->whereHas('perencanaan', function($r) use ($user) {
            $r->whereIn('user_id', function($sq) use ($user) {
                $sq->select('id')->from('users')->where('id', $user->id)->orWhere('parent_id', $user->id);
            });
        }))
        ->when($selectedWilayah, fn($q) => $q->whereHas('perencanaan.user', fn($uq) => $uq->where('upt_asal', $selectedWilayah)))
        ->when($selectedTahun, fn($q) => $q->whereYear('pelaksanaans.tanggal_pemantauan', $selectedTahun))
        ->count();
@endphp

<div class="row row-deck row-cards mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-top:3px solid #206bc4 !important;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-blue-lt p-2 rounded-3"><i class="ti ti-clipboard-list text-blue fs-2"></i></div>
                <div>
                    <div class="h2 mb-0 fw-bold">{{ $totalP }}</div>
                    <div class="text-muted small">Total Perencanaan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-top:3px solid #2fb344 !important;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-green-lt p-2 rounded-3"><i class="ti ti-map-pin text-green fs-2"></i></div>
                <div>
                    <div class="h2 mb-0 fw-bold">{{ $totalPl }}</div>
                    <div class="text-muted small">Total Pelaksanaan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-top:3px solid #0891b2 !important;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-cyan-lt p-2 rounded-3"><i class="ti ti-flask text-cyan fs-2"></i></div>
                <div>
                    <div class="h2 mb-0 fw-bold">{{ $totalLab }}</div>
                    <div class="text-muted small">Hasil Lab Terinput</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-top:3px solid #f59f00 !important;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-yellow-lt p-2 rounded-3"><i class="ti ti-map text-yellow fs-2"></i></div>
                <div>
                    <div class="h2 mb-0 fw-bold">{{ $totalPeta }}</div>
                    <div class="text-muted small">Titik Peta Pemantauan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- Section 1: Export Data Excel                                       --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-blue-lt border-0 py-3 px-4 d-flex align-items-center gap-3">
        <div class="bg-blue text-white p-2 rounded-3 shadow-sm">
            <i class="ti ti-file-spreadsheet fs-3"></i>
        </div>
        <div>
            <div class="text-blue small fw-bold text-uppercase" style="letter-spacing:.05em;">Section 1</div>
            <div class="fw-bold fs-4">Export Data Excel</div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">

            {{-- Export Perencanaan --}}
            <div class="col-md-6">
                <div class="card h-100 border shadow-sm position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-blue-lt p-3 rounded-3 me-3">
                                <i class="ti ti-clipboard-list text-blue" style="font-size:1.75rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Laporan Perencanaan</div>
                                <div class="text-muted small">Dokumen rencana HPIK & status validasi</div>
                            </div>
                        </div>
                        <ul class="list-unstyled small text-muted mb-4">
                            <li><i class="ti ti-check text-success me-1"></i>Data wilayah & Media Pembawa</li>
                            <li><i class="ti ti-check text-success me-1"></i>Target operasional per kuartal</li>
                            <li><i class="ti ti-check text-success me-1"></i>Status validasi BBKHIT/Pusat</li>
                        </ul>
                        <a href="{{ route('laporan.export.perencanaan', ['wilayah' => request('wilayah'), 'tahun' => $selectedTahun]) }}" class="btn btn-primary w-100 btn-pill fw-bold shadow-sm" data-turbo="false">
                            <i class="ti ti-download me-2"></i>Download Excel Perencanaan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Export Pelaksanaan + Lab --}}
            <div class="col-md-6">
                <div class="card h-100 border shadow-sm position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-green-lt p-3 rounded-3 me-3">
                                <i class="ti ti-flask text-green" style="font-size:1.75rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Laporan Output Hasil</div>
                                <div class="text-muted small">Pelaksanaan lapangan + hasil uji lab</div>
                            </div>
                        </div>
                        <ul class="list-unstyled small text-muted mb-4">
                            <li><i class="ti ti-check text-success me-1"></i>Data realisasi lapangan</li>
                            <li><i class="ti ti-check text-success me-1"></i>Hasil lab (Positif/Negatif)</li>
                            <li><i class="ti ti-check text-success me-1"></i>Prevalensi & insidensi HPIK</li>
                        </ul>
                        <a href="{{ route('laporan.export.pelaksanaan', ['wilayah' => request('wilayah'), 'tahun' => $selectedTahun]) }}" class="btn btn-success w-100 btn-pill fw-bold shadow-sm" data-turbo="false">
                            <i class="ti ti-download me-2"></i>Download Excel Pelaksanaan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if(Auth::user()->isPusat() || Auth::user()->isDeveloper())
{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- Section 2: Download / Cetak Peta                                  --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-teal-lt border-0 py-3 px-4 d-flex align-items-center gap-3">
        <div class="bg-teal text-white p-2 rounded-3 shadow-sm">
            <i class="ti ti-map fs-3"></i>
        </div>
        <div>
            <div class="text-teal small fw-bold text-uppercase" style="letter-spacing:.05em;">Section 2</div>
            <div class="fw-bold fs-4">Unduh & Cetak Peta Pemantauan</div>
        </div>
        <div class="ms-auto">
            <span class="badge bg-teal-lt text-teal px-3 py-2">
                <i class="ti ti-map-pin me-1"></i> {{ $totalPeta }} Titik Terpetakan
            </span>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">

            {{-- Buka Peta Interaktif --}}
            <div class="col-md-6">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-teal-lt p-3 rounded-circle d-inline-block mb-3">
                            <i class="ti ti-map text-teal" style="font-size:2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold">Peta Interaktif</h5>
                        <p class="text-muted small mb-4">Lihat sebaran titik pemantauan seluruh Indonesia secara interaktif dengan zoom & filter warna status.</p>
                        <a href="{{ route('peta.index', ['tahun' => $selectedTahun, 'wilayah' => request('wilayah')]) }}" class="btn btn-teal text-white w-100 btn-pill fw-bold shadow-sm">
                            <i class="ti ti-map me-2"></i>Buka Peta Pemantauan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Cetak Peta ke PDF --}}
            <div class="col-md-6">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-orange-lt p-3 rounded-circle d-inline-block mb-3">
                            <i class="ti ti-printer text-orange" style="font-size:2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold">Cetak Peta (PDF)</h5>
                        <p class="text-muted small mb-4">Buka peta lalu gunakan Print → Save as PDF untuk menyimpan seluruh peta sebaran sebagai file PDF.</p>
                        <a href="{{ route('peta.index', ['tahun' => $selectedTahun, 'wilayah' => request('wilayah')]) }}" target="_blank" class="btn btn-orange text-white w-100 btn-pill fw-bold shadow-sm">
                            <i class="ti ti-external-link me-2"></i>Buka & Cetak Peta
                        </a>
                        <div class="text-muted mt-2" style="font-size:.75rem;">
                            <i class="ti ti-info-circle me-1"></i>Di halaman peta klik tombol "Download Peta (PDF)"
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- Section 3: Info & Panduan                                          --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="card border-0 shadow-sm bg-dark text-white overflow-hidden">
    <div class="card-body p-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-white bg-opacity-10 p-3 rounded-3 flex-shrink-0">
                        <i class="ti ti-help-circle text-white" style="font-size:2rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white mb-1">Panduan Alur Pelaporan</h5>
                        <ol class="small mb-0 text-white-50 ps-3">
                            <li class="mb-1"><strong class="text-white">Perencanaan BKHIT</strong> → Input rencana pemantauan (media pembawa, target, lokasi).</li>
                            <li class="mb-1"><strong class="text-white">Validasi BBKHIT</strong> → Verifikasi dan setujui perencanaan yang diajukan.</li>
                            <li class="mb-1"><strong class="text-white">Pelaksanaan BKHIT</strong> → Input realisasi lapangan beserta hasil uji laboratorium.</li>
                            <li class="mb-1"><strong class="text-white">Evaluasi BBKHIT/Pusat</strong> → Tetapkan kesimpulan akhir & status warna peta (🟢🔴).</li>
                            <li><strong class="text-white">Pelaporan</strong> → Export data Excel, cetak formulir resmi, atau unduh peta sebaran.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.15);">
                            <div class="h3 fw-bold text-white mb-0">{{ $totalP }}</div>
                            <div class="small" style="color:rgba(255,255,255,0.7);">Perencanaan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.15);">
                            <div class="h3 fw-bold text-white mb-0">{{ $totalPl }}</div>
                            <div class="small" style="color:rgba(255,255,255,0.7);">Pelaksanaan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.15);">
                            <div class="h3 fw-bold text-white mb-0">{{ $totalLab }}</div>
                            <div class="small" style="color:rgba(255,255,255,0.7);">Hasil Lab</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.15);">
                            <div class="h3 fw-bold text-white mb-0">{{ $totalPeta }}</div>
                            <div class="small" style="color:rgba(255,255,255,0.7);">Titik Peta</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
