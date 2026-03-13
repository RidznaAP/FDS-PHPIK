@extends('layouts.app')

@section('title', 'Detail Perencanaan')

@section('content')
<div class="animate-fade-in">
    {{-- Glassmorphism Page Header --}}
    <div class="row align-items-center mb-5 g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-4">
                <div class="bg-primary text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                    <i class="ti ti-report-analytics fs-1"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-lt text-primary px-3 fs-6 rounded-pill">MODUL PERENCANAAN</span>
                        @php
                            $statusMap = [
                                'draft'    => ['label'=>'Drafting Phase',     'class'=>'bg-secondary-lt text-secondary', 'icon' => 'ti-pencil'],
                                'waiting'  => ['label'=>'Pending Validation', 'class'=>'bg-warning-lt text-warning',   'icon' => 'ti-hourglass-low'],
                                'approved' => ['label'=>'Approved / Active',  'class'=>'bg-success-lt text-success',   'icon' => 'ti-checkbox'],
                            ];
                            $s = $statusMap[$p->status] ?? $statusMap['draft'];
                        @endphp
                        <span class="badge {{ $s['class'] }} px-3 fs-6 rounded-pill">
                            <i class="ti {{ $s['icon'] }} me-1"></i> {{ $s['label'] }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-1">
                        @foreach(array_map('trim', explode(',', $p->jenis_mp)) as $mp)
                            <h1 class="display-5 fw-bold text-dark mb-0 tracking-tight">{{ $mp }}{{ !$loop->last ? ',' : '' }}</h1>
                        @endforeach
                    </div>
                    <div class="text-muted fs-3 d-flex align-items-center">
                        <i class="ti ti-map-pin me-2 text-red"></i>{{ $p->kab_kota }}, {{ $p->provinsi }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                <a href="{{ route('perencanaan.index') }}" class="btn btn-white btn-pill px-4 border-0">
                    <i class="ti ti-arrow-left me-2"></i>Kembali
                </a>
                @if($p->status === 'draft')
                <a href="{{ route('perencanaan.edit', $p->id) }}" class="btn btn-primary btn-pill px-4 border-0">
                    <i class="ti ti-edit me-2"></i>Edit Rencana
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Core Intelligence --}}
        <div class="col-lg-8">
            {{-- Data Board --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold text-muted small text-uppercase tracking-widest">
                            <i class="ti ti-info-circle me-2 text-primary"></i> Identitas Rencana Pemantauan
                        </h3>
                        <div class="small text-muted fw-mono">ID: #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    
                    <div class="row g-0">
                        <div class="col-md-6 border-end border-bottom-md-0 p-4">
                            <div class="info-group">
                                <div class="mb-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Jenis HPIK</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-red-lt text-red p-2 rounded-3 me-3"><i class="ti ti-virus fs-3"></i></div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_map('trim', explode(',', $p->jenis_hpik)) as $hpik)
                                                <span class="badge bg-red-lt text-red border border-red-subtle px-2 py-1">{{ $hpik }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Kemampuan Uji UPT</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-indigo-lt text-indigo p-2 rounded-3 me-3"><i class="ti ti-building-community fs-3"></i></div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_map('trim', explode(',', $p->kemampuan_uji_upt)) as $uji)
                                                <span class="badge bg-indigo-lt text-indigo border border-indigo-subtle px-2 py-1">{{ $uji }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 p-4">
                            <div class="mb-4">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Metode & Lab Penguji</label>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-purple-lt text-purple p-2 rounded-3 me-3"><i class="ti ti-flask fs-3"></i></div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_map('trim', explode(',', $p->metode_pengujian)) as $metode)
                                            <span class="badge bg-purple-lt text-purple border border-purple-subtle px-2 py-1">{{ $metode }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="badge bg-light text-dark px-3 py-2 rounded-3 border w-100 text-center">
                                    <i class="ti ti-flask me-2"></i> {{ $p->lab_uji }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 border-top p-3 px-4 bg-light-soft">
                            <div class="row align-items-center g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Lokasi Pengambilan Sampel</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-azure-lt text-azure p-2 rounded-3 me-3"><i class="ti ti-map-2 fs-4"></i></div>
                                        <div class="fw-bold text-azure fs-4">{{ $p->rencana_lokasi ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 border-start-md px-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Jumlah Sampel</label>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold fs-3 text-dark">{{ $p->rencana_jumlah_sampel ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 border-start-md px-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Metode Sampling</label>
                                    <div class="badge bg-green-lt text-green fs-6 px-3 py-1 mt-1 border border-green-subtle">
                                        {{ mb_strtoupper($p->rencana_metode_sampling ?? '-') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 border-top p-3 px-4 bg-white d-flex justify-content-end">
                            <div class="d-inline-flex align-items-center p-2 bg-light rounded-3 border shadow-sm">
                                <div class="avatar avatar-sm rounded-circle me-3 bg-primary text-white shadow-sm">
                                    {{ strtoupper(substr(optional($p->user)->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="text-start">
                                    <div class="small text-muted fw-bold" style="font-size: 0.65rem;">Petugas Input:</div>
                                    <div class="fw-bold small">{{ optional($p->user)->name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Target Kuartal Visualization --}}
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                    <h3 class="fw-bold mb-0 text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-chart-dots me-2 text-azure"></i> Alokasi Target Per Kuartal
                    </h3>
                    <div class="d-flex gap-2">
                        <div class="badge bg-indigo-lt text-indigo px-3 py-2 rounded-pill shadow-sm border border-indigo-subtle">
                            TARGET UJI: {{ $p->target_uji }}
                        </div>
                        <div class="badge bg-azure text-white px-3 py-2 rounded-pill shadow-sm">
                            TOTAL TW: {{ $p->total_pengujian }}
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    {{-- Periode 1 --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-blue-lt">
                            <div class="card-body p-3">
                                <label class="badge bg-blue text-white mb-2 fs-6 px-3 rounded-pill">PERIODE 1 (Q1 & Q2)</label>
                                <div class="row g-2 mt-1">
                                    @foreach(['TW 1'=>$p->tw1, 'TW 2'=>$p->tw2] as $label => $val)
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded-4 text-center border shadow-sm">
                                            <div class="text-uppercase small fw-bold mb-1 tracking-widest text-muted">{{ $label }}</div>
                                            <div class="h2 fw-extrabold mb-0 text-blue">{{ $val ?? 0 }}</div>
                                            <div class="small text-muted opacity-75">SAMPEL</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Periode 2 --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-azure-lt">
                            <div class="card-body p-3">
                                <label class="badge bg-azure text-white mb-2 fs-6 px-3 rounded-pill">PERIODE 2 (Q3 & Q4)</label>
                                <div class="row g-2 mt-1">
                                    @foreach(['TW 3'=>$p->tw3, 'TW 4'=>$p->tw4] as $label => $val)
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded-4 text-center border shadow-sm">
                                            <div class="text-uppercase small fw-bold mb-1 tracking-widest text-muted">{{ $label }}</div>
                                            <div class="h2 fw-extrabold mb-0 text-azure">{{ $val ?? 0 }}</div>
                                            <div class="small text-muted opacity-75">SAMPEL</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Realisasi List --}}
            <div class="card card-premium border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest mb-0">
                        <i class="ti ti-list-check me-2 text-green"></i> Realisasi Pelaksanaan Lapangan
                    </h3>
                    <div class="badge bg-green-lt px-3 py-2 rounded-pill fw-bold">{{ $p->pelaksanaans->count() }} Kegiatan</div>
                </div>
                <div class="card-body p-0">
                    @if($p->pelaksanaans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover card-table">
                            <thead>
                                <tr class="bg-light text-muted small fw-bold text-uppercase">
                                    <th class="ps-4">Tanggal & Lokasi</th>
                                    <th class="text-center">Sampel</th>
                                    <th class="text-center">Hasil Lab</th>
                                    <th class="w-1 pe-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($p->pelaksanaans as $pel)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-blue-lt p-2 rounded-circle me-3">
                                                <i class="ti ti-map-2 text-blue"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $pel->lokasi_pengambilan_sampel }}</div>
                                                <div class="small text-muted">{{ \Carbon\Carbon::parse($pel->tanggal_pemantauan)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold fs-4">{{ $pel->jumlah_sampel }}</div>
                                        <div class="small text-muted fw-bold">EKOR</div>
                                    </td>
                                    <td class="text-center">
                                        @if($pel->laboratorium)
                                            @php
                                                $labRes = $pel->laboratorium->hasil_uji;
                                                $labClass = $labRes === 'Negatif' ? 'bg-success text-white' : ($labRes === 'Positif' ? 'bg-danger text-white' : 'bg-warning text-white');
                                            @endphp
                                            <span class="badge {{ $labClass }} px-3 py-2 btn-pill shadow-sm">
                                                <i class="ti ti-microscope me-1"></i> {{ strtoupper($labRes) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted px-3 py-2 btn-pill border fst-italic">BELUM DIUJI</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('pelaksanaan.show', $pel->id) }}" class="btn btn-icon btn-ghost-primary rounded-circle border-0 shadow-none">
                                            <i class="ti ti-chevron-right fs-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="bg-light p-4 rounded-circle d-inline-block mb-3">
                            <i class="ti ti-clipboard-off text-muted opacity-50" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-muted">Belum ada realisasi</h4>
                        <p class="text-muted small px-5">Data pengambilan sampel belum tercatat. Hubungi tim lapangan untuk pembaruan data.</p>
                        @if(Auth::user()->isUpt() && $p->status === 'approved')
                        <div class="mt-4">
                            @if($p->pelaksanaans->count() < $p->target_uji)
                                <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-outline-primary btn-pill">
                                    <i class="ti ti-plus me-2"></i>TAMBAH PELAKSANAAN
                                </a>
                            @else
                                <div class="badge bg-green text-white px-3 py-2 rounded-pill shadow-sm"><i class="ti ti-check me-2"></i>Target Uji Terpenuhi ({{ $p->pelaksanaans->count() }}/{{ $p->target_uji }})</div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kanan: Context & Insights --}}
        <div class="col-lg-4">
            {{-- Action Palette --}}
            <div class="card card-premium shadow-sm border-0 mb-4 bg-dark text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <h3 class="card-title fw-bold text-uppercase small tracking-widest opacity-75 mb-4">
                        <i class="ti ti-bolt me-2 text-warning"></i> Quick Operations
                    </h3>
                    <div class="d-grid gap-3 position-relative" style="z-index: 2;">
                        @if(Auth::user()->isUpt() && $p->status === 'approved')
                            @if($p->pelaksanaans->count() < $p->target_uji)
                                <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-white btn-pill w-100 fw-bold border-0 shadow-lg">
                                    <i class="ti ti-plus me-2 text-primary"></i>PELAKSANAAN BARU
                                </a>
                            @else
                                <div class="badge bg-green text-white px-3 py-2 rounded-pill shadow-sm w-100"><i class="ti ti-check me-2"></i>Target Uji Terpenuhi</div>
                            @endif
                        @endif
                        
                        <a href="{{ route('perencanaan.export') }}" class="btn btn-outline-light btn-pill w-100 opacity-75 hover-opacity-100">
                            <i class="ti ti-file-export me-2"></i>EKSPOR LAPORAN
                        </a>
                    </div>
                    <i class="ti ti-rocket position-absolute bottom-0 end-0 opacity-10" style="font-size: 10rem; margin-bottom: -2rem; margin-right: -2rem;"></i>
                </div>
            </div>

            {{-- Resi Tracking Timeline --}}
            <div class="card card-premium border-0 shadow-sm bg-white timeline-card">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4 text-muted small text-uppercase tracking-widest border-bottom pb-3">
                        <i class="ti ti-route me-2 text-indigo"></i> Jejak Linimasa (Tracking)
                    </h3>
                    
                    @php
                        $hasPelaksanaan = $p->pelaksanaans->count() > 0;
                        $hasLab = false;
                        foreach($p->pelaksanaans as $pel) { if ($pel->laboratorium) $hasLab = true; }
                        $hasEval = $p->evaluasi ? true : false;
                        
                        $step1_active = true;
                        $step2_active = $hasPelaksanaan;
                        $step3_active = $hasLab;
                        $step4_active = $hasEval;
                    @endphp

                    <div class="vertical-timeline position-relative ps-4 py-2">
                        <!-- Garis penyambung utama -->
                        <div class="timeline-line position-absolute top-0 bottom-0 ms-1 bg-light border-start border-2 border-primary" style="left: 14px; opacity: 0.2;"></div>

                        <!-- 1. Perencanaan -->
                        <div class="timeline-item position-relative mb-4">
                            <div class="timeline-icon position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-primary text-white" style="width: 32px; height: 32px; left: -16px; top: -4px;">
                                <i class="ti ti-check" style="font-size: 1rem;"></i>
                            </div>
                            <div class="timeline-content ps-4">
                                <div class="fw-bold text-dark fs-5 text-uppercase">1. Rencana Digagas</div>
                                <div class="text-muted small mt-1"><i class="ti ti-calendar-event me-1"></i>{{ $p->created_at->format('d/m/Y H:i') }} | {{ optional($p->user)->name ?? 'Admin' }}</div>
                                @if($p->status == 'approved')
                                    <div class="text-success small fw-bold mt-1"><i class="ti ti-rosette-discount-check me-1"></i>Telah Disetujui</div>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Pelaksanaan -->
                        <div class="timeline-item position-relative mb-4">
                            <div class="timeline-icon position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center {{ $step2_active ? 'bg-blue text-white' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px; left: -16px; top: -4px;">
                                <i class="ti {{ $step2_active ? 'ti-map-pin' : 'ti-dots' }}" style="font-size: 1rem;"></i>
                            </div>
                            <div class="timeline-content ps-4 {{ !$step2_active ? 'opacity-50' : '' }}">
                                <div class="fw-bold text-dark fs-5 text-uppercase">2. Proses Lapangan</div>
                                @if($step2_active)
                                    <div class="text-muted small mt-1"><i class="ti ti-box me-1"></i>{{ $p->pelaksanaans->count() }} Sampel diambil</div>
                                @else
                                    <div class="text-muted small mt-1">Belum ada sampel lapangan</div>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Laboratorium -->
                        <div class="timeline-item position-relative mb-4">
                            <div class="timeline-icon position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center {{ $step3_active ? 'bg-purple text-white' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px; left: -16px; top: -4px;">
                                <i class="ti {{ $step3_active ? 'ti-microscope' : 'ti-dots' }}" style="font-size: 1rem;"></i>
                            </div>
                            <div class="timeline-content ps-4 {{ !$step3_active ? 'opacity-50' : '' }}">
                                <div class="fw-bold text-dark fs-5 text-uppercase">3. Proses Uji Lab</div>
                                @if($step3_active)
                                    <div class="text-muted small mt-1"><i class="ti ti-flask me-1"></i>Telah masuk Laboratorium</div>
                                @else
                                    <div class="text-muted small mt-1">Belum ada pengujian lab</div>
                                @endif
                            </div>
                        </div>

                        <!-- 4. Evaluasi -->
                        <div class="timeline-item position-relative">
                            <div class="timeline-icon position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center {{ $step4_active ? 'bg-green text-white' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px; left: -16px; top: -4px;">
                                <i class="ti {{ $step4_active ? 'ti-flag' : 'ti-dots' }}" style="font-size: 1rem;"></i>
                            </div>
                            <div class="timeline-content ps-4 {{ !$step4_active ? 'opacity-50' : '' }}">
                                <div class="fw-bold text-dark fs-5 text-uppercase">4. Evaluasi Selesai</div>
                                @if($step4_active)
                                    <div class="text-muted small mt-1"><i class="ti ti-chart-bar me-1"></i>Laporan Kesimpulan Terbit</div>
                                @else
                                    <div class="text-muted small mt-1">Belum dievaluasi final</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


