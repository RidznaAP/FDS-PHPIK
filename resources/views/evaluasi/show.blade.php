@extends('layouts.app')

@section('title', 'Detail Evaluasi HPIK')

@section('content')
<div class="row detail-header align-items-center">
    <div class="col">
        <div class="detail-subtitle">Modul Evaluasi & Kesimpulan</div>
        <h1 class="detail-title">Hasil Evaluasi Akhir</h1>
        <div class="detail-subtitle">
            <i class="ti ti-map-pin me-1"></i>{{ $evaluasi->perencanaan->kab_kota }}, {{ $evaluasi->perencanaan->provinsi }}
        </div>
    </div>
    <div class="col-auto">
        <a href="{{ route('evaluasi.index') }}" class="btn btn-outline-secondary btn-pill shadow-sm">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Card: Hasil Evaluasi Utama --}}
        <div class="card card-premium mb-4 overflow-hidden border-0">
            <div class="card-header border-0 pb-0 bg-transparent">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">
                    Ringkasan Hasil Evaluasi
                </h3>
                <div class="card-options">
                    <span class="badge badge-premium bg-{{ $evaluasi->warna }}-lt text-{{ $evaluasi->warna }}">
                        {{ $evaluasi->label_kesimpulan }}
                    </span>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-bulb"></i></div>
                                <div class="info-content">
                                    <label>Kesimpulan Strategis</label>
                                    <span class="h3 text-primary mb-0 fw-bold">{{ $evaluasi->kesimpulan }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon bg-blue-lt text-blue"><i class="ti ti-calendar-event"></i></div>
                                <div class="info-content">
                                    <label>Tanggal Evaluasi Selesai</label>
                                    <span>{{ $evaluasi->tanggal_evaluasi->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon bg-azure-lt text-azure"><i class="ti ti-user-check"></i></div>
                                <div class="info-content">
                                    <label>Evaluator Utama</label>
                                    <span class="fw-bold">{{ $evaluasi->evaluator }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="stat-box p-4 text-center bg-blue-lt border-0 rounded-4">
                            <div class="h1 mb-1 text-blue fw-bold">{{ $evaluasi->prevalensi ?? '-' }}%</div>
                            <div class="text-blue small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.65rem;">Prevalensi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box p-4 text-center bg-azure-lt border-0 rounded-4">
                            <div class="h1 mb-1 text-azure fw-bold">{{ $evaluasi->insidensi ?? '-' }}%</div>
                            <div class="text-azure small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.65rem;">Insidensi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box p-4 text-center bg-green-lt border-0 rounded-4">
                            <div class="h1 mb-1 text-green fw-bold">{{ $evaluasi->realisasi_uji ?? '-' }}</div>
                            <div class="text-green small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.65rem;">Realisasi Uji</div>
                        </div>
                    </div>
                </div>

                @if($evaluasi->catatan)
                <div class="mt-4 p-3 bg-light rounded-4 border-start border-primary border-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Catatan Tambahan</div>
                    <div class="text-muted italic">"{{ $evaluasi->catatan }}"</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card: Referensi Perencanaan --}}
        <div class="card card-premium overflow-hidden">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Referensi Perencanaan Dasar</h3>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small fw-bold">Media Pembawa</div>
                                <div class="fw-bold fs-4">{{ $evaluasi->perencanaan->jenis_mp }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small fw-bold">Target HPIK</div>
                                <div class="fw-bold fs-4">{{ $evaluasi->perencanaan->jenis_hpik }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('perencanaan.show', $evaluasi->perencanaan_id) }}" class="btn btn-outline-primary btn-pill shadow-sm">
                            <i class="ti ti-clipboard-list me-1"></i>Detail Perencanaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Statistik Lab Terkait --}}
        <div class="card card-premium mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Penyelesaian Lab</h3>
            </div>
            <div class="card-body text-center pt-3">
                @php
                    $selesai = $evaluasi->perencanaan->pelaksanaans->filter(fn($pl) => $pl->laboratorium !== null)->count();
                    $total = $evaluasi->perencanaan->pelaksanaans->count();
                    $persen = $total > 0 ? round(($selesai/$total)*100) : 0;
                @endphp
                
                <div class="mb-4">
                    <div class="display-4 fw-bold text-primary mb-0">{{ $persen }}%</div>
                    <div class="text-muted small fw-bold">TINGKAT PENYELESAIAN</div>
                </div>

                <div class="progress progress-xl mb-3 shadow-sm" style="height: 12px; border-radius: 10px;">
                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: {{ $persen }}%"></div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3">
                    <span class="text-muted small">Sampel Selesai</span>
                    <span class="fw-bold h4 mb-0 text-success">{{ $selesai }} <small class="text-muted fw-normal">/ {{ $total }}</small></span>
                </div>
                
                <p class="text-muted small mt-3 italic mb-0">Rasio sampel lapangan yang telah memiliki hasil pengujian laboratorium.</p>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="card card-premium">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">ID Evaluasi</span>
                    <span class="fw-mono small">#EVL-{{ str_pad($evaluasi->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="d-divider my-2"></div>
                <div class="d-grid gap-2">
                    <a href="{{ route('evaluasi.index') }}" class="btn btn-ghost-secondary w-100">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

