@extends('layouts.app')

@section('title', 'Daftar Evaluasi — SIP-HPIK')
@section('page_title', 'Evaluasi Penetapan')
@section('page_subtitle', 'Daftar penetapan status wilayah berdasarkan hasil uji laboratorium HPIK')

@section('styles')
<style>
.warna-badge { width: 12px; height: 12px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.warna-hijau  { background: #22c55e; }
.warna-kuning { background: #f59e0b; }
.warna-merah  { background: #ef4444; }
</style>
@endsection

@section('content')

{{-- Filter Bar --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('evaluasi.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari provinsi / kab-kota..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-3">
                <select name="kesimpulan" class="form-select">
                    <option value="">— Semua Kesimpulan —</option>
                    <option value="Bebas HPIK"   {{ request('kesimpulan') === 'Bebas HPIK'   ? 'selected' : '' }}>🟢 Bebas HPIK</option>
                    <option value="Waspada"      {{ request('kesimpulan') === 'Waspada'      ? 'selected' : '' }}>🟡 Waspada</option>
                    <option value="Positif HPIK" {{ request('kesimpulan') === 'Positif HPIK' ? 'selected' : '' }}>🔴 Positif HPIK</option>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="warna" class="form-select">
                    <option value="">— Semua Warna —</option>
                    <option value="hijau"  {{ request('warna') === 'hijau'  ? 'selected' : '' }}>🟢 Hijau (Aman)</option>
                    <option value="kuning" {{ request('warna') === 'kuning' ? 'selected' : '' }}>🟡 Kuning (Waspada)</option>
                    <option value="merah"  {{ request('warna') === 'merah'  ? 'selected' : '' }}>🔴 Merah (Positif)</option>
                </select>
            </div>
            <div class="col-sm-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="ti ti-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','kesimpulan','warna']))
                <a href="{{ route('evaluasi.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data --}}
<div class="card shadow-sm border-0">
    <div class="card-header d-flex align-items-center gap-2">
        <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-circle-check text-success"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Daftar Evaluasi Penetapan</div>
            <div class="text-muted small">Total: {{ $evaluasis->total() }} data</div>
        </div>
        @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
        <div class="ms-auto">
            <a href="{{ route('seminar.index', 'evaluasi') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-upload me-1"></i> Dokumen Seminar Evaluasi
            </a>
        </div>
        @endif
    </div>

    <div class="card-body p-0">
        @if($evaluasis->isEmpty())
        <div class="empty-state py-5">
            <div class="empty-state-icon">📋</div>
            <h4>Belum Ada Evaluasi</h4>
            <p>Belum ada evaluasi penetapan yang tercatat. Evaluasi dapat dibuat setelah semua pengujian laboratorium selesai.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table">
                <thead>
                    <tr>
                        <th style="width:40px;" class="ps-4">#</th>
                        <th>Wilayah</th>
                        <th>UPT (BKHIT)</th>
                        <th class="text-center">Status Warna</th>
                        <th>Kesimpulan</th>
                        <th>Evaluator</th>
                        <th>Tgl Penetapan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluasis as $i => $ev)
                    @php
                        $warnaClass = match($ev->status_warna) {
                            'hijau'  => ['badge' => 'bg-success-lt text-success', 'dot' => 'warna-hijau',  'label' => 'Hijau / Aman'],
                            'kuning' => ['badge' => 'bg-warning-lt text-warning', 'dot' => 'warna-kuning', 'label' => 'Kuning / Waspada'],
                            'merah'  => ['badge' => 'bg-danger-lt text-danger',   'dot' => 'warna-merah',  'label' => 'Merah / Positif'],
                            default  => ['badge' => 'bg-secondary-lt text-secondary', 'dot' => '', 'label' => $ev->status_warna],
                        };
                        $kesimpulanClass = match($ev->kesimpulan) {
                            'Bebas HPIK'   => 'bg-success text-white',
                            'Waspada'      => 'bg-warning text-white',
                            'Positif HPIK' => 'bg-danger text-white',
                            default        => 'bg-secondary text-white',
                        };
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">{{ $evaluasis->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $ev->perencanaan?->kab_kota ?? '—' }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $ev->perencanaan?->provinsi ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $ev->perencanaan?->user?->name ?? '—' }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $ev->perencanaan?->user?->upt_asal ?? '' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $warnaClass['badge'] }} d-inline-flex align-items-center gap-1 px-3">
                                <span class="warna-badge {{ $warnaClass['dot'] }}"></span>
                                {{ $warnaClass['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $kesimpulanClass }} px-3 py-2 btn-pill">
                                {{ $ev->kesimpulan }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $ev->evaluator }}</td>
                        <td class="small text-muted">
                            {{ $ev->tanggal_evaluasi ? \Carbon\Carbon::parse($ev->tanggal_evaluasi)->translatedFormat('d M Y') : '—' }}
                        </td>
                        <td class="text-end pe-4">
                            @if($ev->perencanaan)
                            <a href="{{ route('perencanaan.show', $ev->perencanaan_id) }}"
                               class="btn btn-sm btn-ghost-primary" title="Lihat Perencanaan">
                                <i class="ti ti-external-link"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($evaluasis->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between">
            <p class="m-0 text-muted small">
                Menampilkan {{ $evaluasis->firstItem() }}–{{ $evaluasis->lastItem() }} dari {{ $evaluasis->total() }} data
            </p>
            {{ $evaluasis->links() }}
        </div>
        @endif

        @endif
    </div>
</div>

@endsection
