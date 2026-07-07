@extends('layouts.app')

@section('title', 'Detail Hasil Laboratorium')

@section('page_title', 'Hasil Lab: ' . ($lab->diagnosis_akhir ?? 'ANALYSIS PENDING'))
@section('page_subtitle', 'Kode: ' . $lab->kode_sampel . ' | Penguji: ' . ($lab->lab_penguji ?? '-'))

@section('page_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('laboratorium.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-2"></i>Kembali
    </a>
    <a href="{{ route('pelaksanaan.show', $lab->pelaksanaan_id) }}" class="btn btn-primary">
        <i class="ti ti-database-export me-2"></i>Data Lapangan
    </a>
    @if(Auth::user()->isPusat() || Auth::user()->isBbkhit() || (Auth::user()->isBkhit() && $lab->pelaksanaan->perencanaan->user_id == Auth::id()))
    <a href="{{ route('laboratorium.edit', $lab->id) }}" class="btn btn-warning">
        <i class="ti ti-edit me-2"></i>Edit
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="animate-fade-in px-2">

    <div class="row g-4">
        {{-- Kiri: Analysis Intelligence --}}
        <div class="col-lg-8">
            {{-- Main Diagnostic Board --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold text-muted small text-uppercase tracking-widest">
                            <i class="ti ti-microscope me-2 text-azure"></i> Rincian Pengujian Spesialis
                        </h3>
                        @php
                            $statusUji = trim($lab->hasil_uji);
                            $stColor = match(true) {
                                strcasecmp($statusUji, 'Positif') === 0     => 'danger',
                                strcasecmp($statusUji, 'Negatif') === 0 || strcasecmp($statusUji, 'NIHIL') === 0 => 'success',
                                strcasecmp($statusUji, 'Inkonklusif') === 0 => 'warning',
                                default                                     => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $stColor }} text-white px-3 py-1 rounded-pill fw-bold animate-pulse">{{ strtoupper($lab->hasil_uji) }}</span>
                    </div>
                    
                    <div class="row g-0">
                        <div class="col-md-7 border-end p-4">
                            <div class="info-group mb-4">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-2">Penetapan Diagnosis</label>
                                <div class="p-4 bg-{{ $stColor }}-lt rounded-4 border-start border-{{ $stColor }} border-4 shadow-sm mb-3">
                                    <div class="h2 fw-extrabold text-{{ $stColor }} mb-0">{{ $lab->diagnosis_akhir ?? 'N/A' }}</div>
                                    <div class="text-muted small fw-bold">FINAL DETERMINATION</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 h-100 border transition-all hover-shadow">
                                        <div class="text-muted small fw-bold mb-1">HPIK DIUJI</div>
                                        <div class="fw-bold text-dark">{{ $lab->jenis_hpik_diuji ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 h-100 border transition-all hover-shadow">
                                        <div class="text-muted small fw-bold mb-1">METODE UJI</div>
                                        <div class="fw-bold text-dark">{{ $lab->metode_uji ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 p-4 bg-light-soft">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-3">Timeline Pengujian</label>
                            <div class="vertical-timeline position-relative ps-4 mt-2">
                                <div class="timeline-item mb-4 position-relative">
                                    <div class="timeline-point bg-azure border-white border-4 rounded-circle position-absolute" style="width:16px;height:16px;left:-25px;top:2px;z-index:2;box-shadow:0 0 0 4px rgba(32,107,196,0.1)"></div>
                                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:0.65rem">MULAI PENELITIAN</div>
                                    <div class="fw-extrabold text-dark h4 mb-0">{{ $lab->tanggal_uji->format('d M Y') }}</div>
                                </div>
                                <div class="timeline-item position-relative">
                                    <div class="timeline-point bg-success border-white border-4 rounded-circle position-absolute" style="width:16px;height:16px;left:-25px;top:2px;z-index:2;box-shadow:0 0 0 4px rgba(47,179,68,0.1)"></div>
                                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:0.65rem">PENETAPAN HASIL</div>
                                    <div class="fw-extrabold text-dark h4 mb-0">{{ $lab->tanggal_hasil ? $lab->tanggal_hasil->format('d M Y') : 'Processing...' }}</div>
                                </div>
                                <div class="timeline-line position-absolute bg-light" style="width:2px;top:10px;bottom:0;left:-18px;height:40px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Contoh Uji --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-purple-lt border-bottom d-flex align-items-center border-purple border-top border-4">
                        <h3 class="mb-0 fw-bold text-purple small text-uppercase tracking-widest">
                            <i class="ti ti-ruler-measure me-2"></i> Data Contoh Uji
                        </h3>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">PANJANG (CM)</div>
                                    <div class="fw-bold text-dark">{{ $lab->panjang ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">BERAT (GRAM)</div>
                                    <div class="fw-bold text-dark">{{ $lab->berat ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">JUMLAH KEMATIAN</div>
                                    <div class="fw-bold text-dark">{{ $lab->jumlah_kematian ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">PADAT TEBAR</div>
                                    <div class="fw-bold text-dark">{{ $lab->padat_tebar ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">ASAL BENIH / INDUK</div>
                                    <div class="fw-bold text-dark">{{ $lab->asal_benih_induk ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-4 h-100 border transition-all">
                                    <div class="text-muted small fw-bold mb-1">GEJALA KLINIS</div>
                                    <div class="fw-bold text-dark">{{ $lab->gejala_klinis ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row for Matrix & Stats --}}
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    {{-- Pathogen Matrix --}}
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-dna me-2 text-red"></i> Deteksi Patogen
                            </h3>
                        </div>
                        <div class="card-body text-center p-4">
                            @php
                                $kelompok_patogen = null;
                                $icon = 'ti-shield-check';
                                $color = 'text-success';
                                if ($lab->hasil_parasit === 'Positif (+)') { $kelompok_patogen = 'Parasit'; $icon = 'ti-bug'; $color = 'text-danger'; }
                                elseif ($lab->hasil_bakteri === 'Positif (+)') { $kelompok_patogen = 'Bakteri'; $icon = 'ti-circle'; $color = 'text-danger'; }
                                elseif ($lab->hasil_virus === 'Positif (+)') { $kelompok_patogen = 'Virus'; $icon = 'ti-virus'; $color = 'text-danger'; }
                                elseif ($lab->hasil_jamur === 'Positif (+)') { $kelompok_patogen = 'Jamur'; $icon = 'ti-leaf'; $color = 'text-danger'; }
                                else { $kelompok_patogen = 'Nihil / Negatif'; }
                            @endphp
                            <div class="mb-2"><i class="ti {{ $icon }} {{ $color }}" style="font-size: 3.5rem;"></i></div>
                            <h3 class="fw-bold mb-0 {{ $color }}" style="letter-spacing: 0.05em;">{{ strtoupper($kelompok_patogen) }}</h3>
                            <div class="text-muted small mt-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Kelompok Patogen Ditemukan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    {{-- Statistical Indicators --}}
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-chart-bar me-2 text-azure"></i> Parameter Prevalensi
                            </h3>
                        </div>
                        <div class="card-body pt-4 p-4">
                            <div class="row g-4">
                                <div class="col-6">
                                    <div class="p-4 bg-azure-lt rounded-4 border border-azure animate-scale-up text-center h-100 shadow-sm">
                                        <div class="text-azure small fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">PREVALENSI</div>
                                        <div class="h1 fw-extrabold text-azure mb-0">{{ $lab->prevalensi ?? '0' }}<span class="fs-4 fw-normal">%</span></div>
                                        <div class="small fw-bold opacity-75 text-azure">TOTAL SAMPLE BASE</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-4 bg-indigo-lt rounded-4 border border-indigo animate-scale-up text-center h-100 shadow-sm" style="animation-delay: 0.1s">
                                        <div class="text-indigo small fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">INSIDENSI</div>
                                        <div class="h1 fw-extrabold text-indigo mb-0">{{ $lab->insidensi ?? '0' }}<span class="fs-4 fw-normal">%</span></div>
                                        <div class="small fw-bold opacity-75 text-indigo">SPREAD RATE ESTIMATE</div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="p-3 bg-light rounded-4 d-flex align-items-center justify-content-between border-dashed border border-muted">
                                         <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm"><i class="ti ti-users-group text-primary"></i></div>
                                            <div>
                                                <div class="text-muted small fw-bold">TOTAL SAMPEL DIUJI</div>
                                                <div class="fw-extrabold h3 mb-0">{{ $lab->jumlah_sampel_diperiksa ?? '0' }} SAMPEL</div>
                                            </div>
                                         </div>
                                         <div class="text-end">
                                             <div class="text-danger small fw-bold">TERINFEKSI</div>
                                             <div class="fw-extrabold h3 mb-0 text-danger">{{ $lab->jumlah_ikan_terinfeksi ?? '0' }} SAMPEL</div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Technical Oversight --}}
        <div class="col-lg-4">
            {{-- Technical Metadata --}}
            <div class="card card-premium mb-4 border-0 shadow-sm bg-white overflow-hidden animate-scale-up">
                 <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-settings me-2 text-indigo"></i> INFORMASI TEKNIS UJI
                    </h3>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="p-4 rounded-4 bg-indigo-lt border border-indigo mb-4 shadow-inner">
                        <i class="ti ti-archive text-indigo mb-2" style="font-size: 4rem;"></i>
                        <h1 class="fw-extrabold mb-0 text-dark">#{{ $lab->kode_sampel }}</h1>
                        <div class="badge bg-indigo text-white px-3 py-1 rounded-pill small mt-2">TECHNICAL SERIAL ID</div>
                    </div>

                    <div class="list-group list-group-flush text-start border rounded-4 overflow-hidden shadow-sm bg-white">
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted small fw-bold">KOLAM UJI</span>
                            <span class="badge bg-azure-lt">{{ $lab->jumlah_kolam_uji ?? '0' }} Unit</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted small fw-bold">PERIODE PENGAMATAN</span>
                            <span class="fw-bold text-dark">{{ $lab->periode_pengamatan ? $lab->periode_pengamatan . ' Hari' : '—' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted small fw-bold">PETUGAS PENGUJI</span>
                            <span class="fw-bold text-primary">
                                <i class="ti ti-user-check me-1"></i>{{ $lab->nama_petugas_uji ?? '—' }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted small fw-bold">SUMBER ASAL</span>
                            <span class="fw-bold text-primary">{{ $lab->pelaksanaan->jenis_ikan }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Tools --}}
            <div class="card card-premium border-0 shadow-sm bg-dark text-white overflow-hidden mb-4">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold text-uppercase small tracking-widest opacity-75 mb-4">
                        <i class="ti ti-bolt text-warning me-2"></i> Report Actions
                    </h3>
                    <div class="d-grid gap-3">
                        <button class="btn btn-white btn-pill fw-bold shadow-lg hvr-icon-forward" onclick="window.print()">
                            <i class="ti ti-printer me-2 text-primary"></i> PRINT LABORATORY REPORT
                        </button>
                        {{-- Export route logic removed as it does not exist yet --}}
                    </div>
                </div>
            </div>

            {{-- Quick Link Field --}}
            <div class="card card-premium border-0 shadow-sm bg-white overflow-hidden">
                <div class="card-body p-4 text-center">
                     <div class="text-muted small fw-bold text-uppercase mb-3 tracking-widest">RESEARCH LOCATION</div>
                     <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                        <i class="ti ti-map-pin text-danger fs-1"></i>
                        <div class="text-dark fw-bold h4 mb-0">{{ $lab->pelaksanaan->lokasi_pengambilan_sampel }}</div>
                     </div>
                     <a href="{{ route('pelaksanaan.show', $lab->pelaksanaan_id) }}" class="btn btn-ghost-azure btn-sm w-100">
                         View Source Field Laporan <i class="ti ti-chevron-right ms-1"></i>
                     </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

