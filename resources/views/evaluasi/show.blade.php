@extends('layouts.app')

@section('title', 'Detail Evaluasi HPIK')

@section('content')
<div class="animate-fade-in px-2">
    {{-- Premium Glassmorphism Header --}}
    <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-primary border-5">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-4">
                <div class="bg-primary text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                    <i class="ti ti-report-analytics fs-1"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-lt text-primary px-3 fs-6 rounded-pill">MODUL EVALUASI & KESIMPULAN</span>
                        <span class="badge bg-light text-muted px-3 fs-6 rounded-pill border">ID: #EVL-{{ str_pad($evaluasi->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">LAPORAN PENETAPAN STATUS</h1>
                    <div class="text-muted fs-3 d-flex align-items-center">
                        <i class="ti ti-map-pin me-2 text-danger"></i>{{ $evaluasi->perencanaan->kab_kota }}, {{ $evaluasi->perencanaan->provinsi }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                <a href="{{ route('evaluasi.index') }}" class="btn btn-white btn-pill px-4 border-0">
                    <i class="ti ti-list me-2"></i>Daftar
                </a>
                <a href="{{ route('perencanaan.show', $evaluasi->perencanaan_id) }}" class="btn btn-primary btn-pill px-4 border-0">
                    <i class="ti ti-file-certificate me-2 text-white"></i>Rencana
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Analysis Detail --}}
        <div class="col-lg-8">
            {{-- Main Result Terminal --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold text-muted small text-uppercase tracking-widest">
                            <i class="ti ti-certificate me-2 text-primary"></i> Hasil Penetapan Akhir
                        </h3>
                        <div class="small text-muted fw-bold">{{ $evaluasi->tanggal_evaluasi->format('d F Y') }}</div>
                    </div>
                    
                    <div class="p-5 text-center">
                        @php
                            $stMap = [
                                'hijau'  => ['color'=>'success', 'icon'=>'ti-shield-check', 'label'=>'BEBAS HPIK (AMAN)'],
                                'kuning' => ['color'=>'warning', 'icon'=>'ti-alert-triangle', 'label'=>'WASPADA (PANTAU)'],
                                'merah'  => ['color'=>'danger',  'icon'=>'ti-biohazard', 'label'=>'POSITIF HPIK (WABAH)']
                            ];
                            $st = $stMap[$evaluasi->status_warna] ?? ['color'=>'secondary', 'icon'=>'ti-info-circle', 'label'=>'PENDING'];
                        @endphp
                        
                        <div class="d-inline-flex p-4 rounded-circle bg-{{ $st['color'] }}-lt border border-{{ $st['color'] }} mb-4 animate-scale-up shadow-sm">
                            <i class="ti {{ $st['icon'] }} text-{{ $st['color'] }}" style="font-size: 6rem;"></i>
                        </div>
                        <h1 class="display-3 fw-extrabold text-{{ $st['color'] }} mb-1 tracking-tighter">{{ strtoupper($evaluasi->kesimpulan) }}</h1>
                        <div class="badge bg-{{ $st['color'] }} text-white px-4 py-2 rounded-pill fs-4 mb-4 shadow-sm">{{ $st['label'] }}</div>
                        
                        <p class="text-muted fs-4 max-w-lg mx-auto italic">
                           "Berdasarkan hasil analisis data lapangan dan laboratorium, wilayah ini ditetapkan dalam status {{ $evaluasi->kesimpulan }}."
                        </p>
                    </div>

                    {{-- Metrics Dashboard --}}
                    <div class="row g-0 border-top">
                        <div class="col-md-4 border-end p-4 text-center hover-bg-light transition-all">
                            <div class="text-muted small fw-bold text-uppercase mb-2 tracking-widest">Prevalensi</div>
                            <div class="h1 fw-extrabold text-primary mb-0">{{ $evaluasi->prevalensi ?? '0' }}%</div>
                            <div class="small text-muted fw-bold">TINGKAT KEJADIAN</div>
                        </div>
                        <div class="col-md-4 border-end p-4 text-center hover-bg-light transition-all">
                            <div class="text-muted small fw-bold text-uppercase mb-2 tracking-widest">Insidensi</div>
                            <div class="h1 fw-extrabold text-primary mb-0">{{ $evaluasi->insidensi ?? '0' }}%</div>
                            <div class="small text-muted fw-bold">RISIKO PENULARAN</div>
                        </div>
                        <div class="col-md-4 p-4 text-center hover-bg-light transition-all">
                            <div class="text-muted small fw-bold text-uppercase mb-2 tracking-widest">Realisasi Uji</div>
                            <div class="h1 fw-extrabold text-green mb-0">{{ $evaluasi->realisasi_uji ?? '0' }}</div>
                            <div class="small text-muted fw-bold">TOTAL SAMPEL VALID</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recommendation Board --}}
            @if($evaluasi->rekomendasi)
            <div class="card card-premium mb-4 border-0 shadow-sm bg-dark text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <h3 class="card-title fw-bold text-uppercase small tracking-widest opacity-75 mb-3">
                        <i class="ti ti-bulb me-2 text-warning"></i> Rekomendasi Tindak Lanjut
                    </h3>
                    <div class="fs-4 fw-medium lh-base position-relative" style="z-index: 2;">
                        {{ $evaluasi->rekomendasi }}
                    </div>
                    <i class="ti ti-message-2-share position-absolute bottom-0 end-0 opacity-10" style="font-size: 8rem; margin-right: -1rem; margin-bottom: -1rem;"></i>
                </div>
            </div>
            @endif

            {{-- Reference Board --}}
            <div class="card card-premium border-0 shadow-sm bg-white">
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-clipboard-list me-2 text-indigo"></i> Referensi Data Dasar
                    </h3>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4 align-items-center mt-2">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">MEDIA PEMBAWA</div>
                                        <div class="fw-extrabold fs-3 text-dark">{{ $evaluasi->perencanaan->jenis_mp }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">TARGET PATOGEN</div>
                                        <div class="fw-extrabold fs-3 text-red">{{ $evaluasi->perencanaan->jenis_hpik }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="p-3 bg-indigo-lt rounded-4 d-flex align-items-center justify-content-center h-100 flex-column border border-indigo animate-scale-up">
                                <div class="text-indigo small fw-bold mb-2">INTEGRITAS DATA</div>
                                <i class="ti ti-database-check text-indigo fs-1 mb-1"></i>
                                <div class="small fw-bold text-indigo">VALIDATED</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Sidebar Analytics --}}
        <div class="col-lg-4">
            {{-- Lab Completion Widget --}}
            <div class="card card-premium mb-4 border-0 shadow-sm bg-white overflow-hidden">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-flask me-2 text-azure"></i> PENYELESAIAN LAB
                    </h3>
                </div>
                <div class="card-body p-4 text-center">
                    @php
                        $selesai = $evaluasi->perencanaan->pelaksanaans->filter(fn($pl) => $pl->laboratorium !== null)->count();
                        $total = $evaluasi->perencanaan->pelaksanaans->count();
                        $persen = $total > 0 ? round(($selesai/$total)*100) : 0;
                    @endphp
                    
                    <div class="mb-4 position-relative d-inline-block">
                         <div class="display-1 fw-extrabold text-azure mb-0" style="font-size: 5rem;">{{ $persen }}<span class="fs-2">%</span></div>
                         <div class="text-muted small fw-extrabold tracking-widest">LAB COMPLETION</div>
                    </div>

                    <div class="progress progress-xl mb-4 shadow-sm" style="height: 14px; border-radius: 10px; background: #f1f5f9;">
                        <div class="progress-bar bg-azure progress-bar-striped progress-bar-animated" style="width: {{ $persen }}%"></div>
                    </div>
                    
                    <div class="p-3 bg-azure-lt rounded-4 d-flex justify-content-between align-items-center border border-azure">
                        <span class="text-azure small fw-bold">SAMPEL TERUJI:</span>
                        <span class="fw-extrabold h3 mb-0 text-azure">{{ $selesai }} <small class="text-muted fw-normal fs-6">/ {{ $total }}</small></span>
                    </div>
                </div>
            </div>

            {{-- Personnel Oversight --}}
            <div class="card card-premium mb-4 border-0 shadow-sm bg-white overflow-hidden">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-user-check me-2 text-indigo"></i> OTORITAS PENILAI
                    </h3>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="avatar avatar-xl rounded-4 bg-indigo text-white mb-3 shadow-sm">
                        {{ strtoupper(substr($evaluasi->evaluator, 0, 1)) }}
                    </div>
                    <div class="h3 fw-bold mb-0 text-dark">{{ $evaluasi->evaluator }}</div>
                    <div class="badge bg-indigo-lt p-2 px-3 rounded-pill small mt-2 fw-bold">EVALUATOR UTAMA</div>
                    
                    <div class="d-divider my-4"></div>
                    
                    <div class="text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                             <span class="text-muted small">WAKTU PENETAPAN</span>
                             <span class="fw-bold small">{{ $evaluasi->created_at->format('H:i T') }}</span>
                        </div>
                         <div class="d-flex justify-content-between align-items-center">
                             <span class="text-muted small">VERSI LAPORAN</span>
                             <span class="badge bg-light border text-muted">v2.1.Final</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Tools --}}
            <div class="card card-premium border-0 shadow-sm bg-dark text-white overflow-hidden">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold text-uppercase small tracking-widest opacity-75 mb-4">
                        <i class="ti ti-bolt text-warning me-2"></i> Report Actions
                    </h3>
                    <div class="d-grid gap-3">
                        <button class="btn btn-white btn-pill fw-bold shadow-lg hvr-icon-forward" onclick="window.print()">
                            <i class="ti ti-printer me-2 text-primary"></i> CETAK SERTIFIKAT HASIL
                        </button>
                        <a href="{{ route('evaluasi.export') }}" class="btn btn-outline-light btn-pill opacity-75">
                            <i class="ti ti-file-export me-2"></i>EKSPOR FORMULIR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

