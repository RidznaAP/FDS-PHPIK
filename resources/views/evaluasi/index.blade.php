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


{{-- Dokumen Seminar Evaluasi Terbaru --}}
@if($dokumenTerbaru->isNotEmpty())
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light d-flex align-items-center">
        <h5 class="card-title mb-0">
            <i class="ti ti-file-text text-primary me-2"></i> Dokumen Seminar Terbaru
        </h5>
        <div class="ms-auto">
            <a href="{{ route('seminar.index', 'evaluasi') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua Dokumen
            </a>
        </div>
    </div>
    <div class="list-group list-group-flush">
        @foreach($dokumenTerbaru as $dok)
        <div class="list-group-item d-flex align-items-center">
            <div class="me-3 text-muted">
                <i class="ti ti-file-description fs-2"></i>
            </div>
            <div class="flex-fill">
                <div class="fw-semibold text-dark">{{ $dok->judul }}</div>
                <div class="small text-muted">
                    Diunggah oleh: {{ $dok->user->name }} &bull; {{ $dok->created_at->diffForHumans() }}
                </div>
            </div>
            <div>
                <a href="{{ route('seminar.download', $dok->id) }}" class="btn btn-sm btn-ghost-primary" title="Download" target="_blank">
                    <i class="ti ti-download"></i> Unduh
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

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
        <div class="ms-auto">
            <a href="{{ route('seminar.index', 'evaluasi') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-upload me-1"></i> Dokumen Seminar Evaluasi
            </a>
        </div>
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

                        <th>Kesimpulan</th>
                        <th>Evaluator</th>
                        <th>Tgl Penetapan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluasis as $i => $ev)
                    @php

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
