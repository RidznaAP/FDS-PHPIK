@extends('layouts.app')

@section('title', 'Detail Evaluasi HPIK')
@section('page_title', 'Detail Evaluasi HPIK')
@section('page_subtitle', $evaluasi->perencanaan->kab_kota . ', ' . $evaluasi->perencanaan->provinsi)

@section('page_actions')
<a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-1"></i>Kembali
</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        {{-- Card: Hasil Evaluasi Utama --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Hasil Evaluasi Akhir</h3>
                <div class="card-options">
                    <span class="badge bg-{{ $evaluasi->warna }}-lt text-{{ $evaluasi->warna }} fs-6">
                        {{ $evaluasi->label_kesimpulan }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Kesimpulan Akhir</div>
                        <div class="h3 mb-0">{{ $evaluasi->kesimpulan }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Evaluasi</div>
                        <div class="fw-semibold">{{ $evaluasi->tanggal_evaluasi->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-blue-lt rounded text-center">
                            <div class="h2 mb-0 text-blue">{{ $evaluasi->prevalensi ?? '-' }}%</div>
                            <div class="text-muted small">Prevalensi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-azure-lt rounded text-center">
                            <div class="h2 mb-0 text-azure">{{ $evaluasi->insidensi ?? '-' }}%</div>
                            <div class="text-muted small">Insidensi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-green-lt rounded text-center">
                            <div class="h2 mb-0 text-green">{{ $evaluasi->realisasi_uji ?? '-' }}</div>
                            <div class="text-muted small">Realisasi Uji</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Catatan / Keterangan</div>
                        <div class="alert alert-info py-2 mb-0 mt-1">
                            {{ $evaluasi->catatan ?? 'Tidak ada catatan tambahan.' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Evaluator</div>
                        <div class="fw-semibold">{{ $evaluasi->evaluator }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Referensi Perencanaan --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-clipboard-list me-2"></i>Referensi Perencanaan</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-sm-4">
                        <div class="text-muted small">Media Pembawa</div>
                        <div class="fw-semibold">{{ $evaluasi->perencanaan->jenis_mp }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Target HPIK</div>
                        <div class="fw-semibold">{{ $evaluasi->perencanaan->jenis_hpik }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Target Uji</div>
                        <div class="fw-semibold">{{ $evaluasi->perencanaan->target_uji }} sampel</div>
                    </div>
                    <div class="col-12 mt-2">
                        <a href="{{ route('perencanaan.show', $evaluasi->perencanaan_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-eye me-1"></i>Lihat Detail Perencanaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Statistik Lab Terkait --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Status Uji Lab</h3></div>
            <div class="card-body">
                @php
                    $selesai = $evaluasi->perencanaan->pelaksanaans->filter(fn($pl) => $pl->laboratorium !== null)->count();
                    $total = $evaluasi->perencanaan->pelaksanaans->count();
                    $persen = $total > 0 ? round(($selesai/$total)*100) : 0;
                @endphp
                <div class="text-center mb-3">
                    <div class="h2 mb-1">{{ $selesai }}/{{ $total }}</div>
                    <div class="text-muted small">Sampel Selesai Diuji</div>
                </div>
                <div class="progress progress-lg mb-2">
                    <div class="progress-bar bg-success" style="width: {{ $persen }}%">{{ $persen }}%</div>
                </div>
                <div class="text-muted small text-center">Progress penyelesaian pengujian lab</div>
            </div>
        </div>
    </div>
</div>
@endsection
