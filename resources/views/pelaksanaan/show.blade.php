@extends('layouts.app')

@section('title', 'Detail Pelaksanaan Lapangan')

@section('breadcrumb')
<ol class="breadcrumb" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Perencanaan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('perencanaan.show', $item->perencanaan_id) }}">Detail Perencanaan</a></li>
    <li class="breadcrumb-item active">Pelaksanaan #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</li>
</ol>
@endsection

@section('page_title', 'Pelaksanaan: ' . $item->jenis_ikan)
@section('page_subtitle', $item->lokasi_pengambilan_sampel)

@section('page_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-2"></i>Kembali
    </a>
    <a href="{{ route('pelaksanaan.print', $item->id) }}" target="_blank" class="btn btn-ghost-primary">
        <i class="ti ti-printer me-2"></i>Cetak PDF
    </a>
    <a href="{{ route('perencanaan.show', $item->perencanaan_id) }}" class="btn btn-primary">
        <i class="ti ti-file-text me-2"></i>Lihat Rencana
    </a>
    @if(Auth::user()->isPusat() || Auth::user()->isDeveloper() || (Auth::user()->id === optional($item->perencanaan)->user_id))
    <a href="{{ route('pelaksanaan.edit', $item->id) }}" class="btn btn-warning">
        <i class="ti ti-edit me-2"></i>Edit
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="animate-fade-in px-2">

    <div class="row g-4 mb-4">
        {{-- Summary Cards Top --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-azure-lt p-3 rounded-circle me-3">
                                    <i class="ti ti-calendar-event fs-2 text-azure"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase">Tanggal Pelaksanaan</div>
                                    <div class="fw-bold fs-3 text-dark">{{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-start-md">
                            <div class="d-flex align-items-center ms-md-4">
                                <div class="bg-indigo-lt p-3 rounded-circle me-3">
                                    <i class="ti ti-building fs-2 text-indigo"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase">UPT Pelaksana</div>
                                    <div class="fw-bold fs-3 text-dark">{{ $item->perencanaan->user->upt_asal ?? $item->perencanaan->user->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-start-md text-end">
                            <div class="d-inline-flex align-items-center bg-light p-2 px-3 rounded-pill border">
                                <div class="avatar avatar-xs bg-primary text-white rounded-circle me-2 fw-bold" style="width: 24px; height: 24px; font-size: 0.6rem;">
                                    {{ strtoupper(substr($item->perencanaan->user->name, 0, 1)) }}
                                </div>
                                <div class="small fw-semibold text-muted">Petugas Input: {{ $item->perencanaan->user->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full Width Field Data Intelligence --}}
        <div class="col-lg-12">
            {{-- Scientific Identity Board --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold text-muted small text-uppercase tracking-widest">
                            <i class="ti ti-report-medical me-2 text-azure"></i> I. DATA PELAKSANAAN LAPANGAN (PENGAMBILAN SAMPEL)
                        </h3>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-6 border-end p-4">
                            <div class="info-group mb-4">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-2">Media Pembawa & Klasifikasi</label>
                                <div class="p-3 bg-light rounded-4 d-flex align-items-center mb-3 border">
                                    <div class="bg-azure text-white p-3 rounded-circle me-3"><i class="ti ti-fish fs-2"></i></div>
                                    <div>
                                        <div class="fw-extrabold fs-2">{{ $item->jenis_ikan }}</div>
                                        <div class="text-muted fst-italic">{{ $item->nama_latin ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-4 text-center border-end">
                                    <div class="text-muted small fw-bold">PANJANG RATA2</div>
                                    <div class="h2 fw-extrabold text-azure mb-0">{{ $item->laboratorium->panjang ?? $item->panjang_cm ?? '0' }} <span class="fs-6 fw-normal">cm</span></div>
                                </div>
                                <div class="col-4 text-center border-end">
                                    <div class="text-muted small fw-bold">BERAT RATA2</div>
                                    <div class="h2 fw-extrabold text-azure mb-0">{{ $item->laboratorium->berat ?? $item->berat_gram ?? '0' }} <span class="fs-6 fw-normal">g</span></div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="text-muted small fw-bold">PADAT TEBAR</div>
                                    <div class="h3 fw-extrabold text-azure mt-1 mb-0">{{ $item->laboratorium->padat_tebar ?? $item->padat_tebar ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 p-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Metrik Sampel & Asal</label>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="card bg-primary-lt border-0 rounded-4 p-3 shadow-inner">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="text-primary small fw-bold mb-1">ASAL BENIH / INDUK</div>
                                                <div class="fw-extrabold text-primary mb-0 fs-3">{{ $item->laboratorium->asal_benih_induk ?? $item->asal_benih_induk ?? 'Tidak Dicantumkan' }}</div>
                                            </div>
                                            <i class="ti ti-map-pin text-primary opacity-25" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-azure-lt border-0 rounded-4 p-3 h-100 shadow-inner">
                                        <div class="text-azure small fw-bold mb-1">JUMLAH SAMPEL</div>
                                        <div class="h2 fw-extrabold text-azure mb-0">{{ $item->jumlah_sampel }}</div>
                                    </div>
                                </div>
                                @php
                                    $kematianVal = $item->laboratorium->jumlah_kematian ?? $item->jumlah_kematian ?? 0;
                                @endphp
                                <div class="col-6">
                                    <div class="card {{ $kematianVal > 0 ? 'bg-red-lt' : 'bg-green-lt' }} border-0 rounded-4 p-3 h-100 shadow-inner">
                                        <div class="text-{{ $kematianVal > 0 ? 'danger' : 'success' }} small fw-bold mb-1">ANGKA KEMATIAN</div>
                                        <div class="h2 fw-extrabold text-{{ $kematianVal > 0 ? 'danger' : 'success' }} mb-0">{{ $kematianVal }} <span class="fs-6">Ekor</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clinical Observations & Personnel --}}
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-eye me-2 text-warning"></i> Observasi Klinis Di Lapangan
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-0">
                            @php
                                $gejala = $item->laboratorium->gejala_klinis ?? $item->gejala_klinis;
                            @endphp
                            @if($gejala)
                            <div class="p-3 bg-warning-lt rounded-4 border-start border-warning border-4 mt-2">
                                <div class="fw-bold text-warning mb-1 small text-uppercase tracking-wider">GEJALA TERAMATI:</div>
                                <p class="mb-0 text-dark-emphasis fw-medium italic">"{{ $gejala }}"</p>
                            </div>
                            @else
                            <div class="text-center py-4 bg-light rounded-4 border border-dashed text-muted fst-italic mt-2">
                                Tidak ada gejala klinis yang dilaporkan.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-users me-2 text-indigo"></i> NAMA PENGAMBIL SAMPEL LAPANGAN
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-0">
                             <div class="d-flex flex-wrap gap-2 mt-2">
                                @if($item->pengambil_sampel && count($item->pengambil_sampel) > 0)
                                    @foreach($item->pengambil_sampel as $nama)
                                    <span class="badge bg-indigo-lt p-2 px-3 fs-6 rounded-pill shadow-sm animate-scale-up border border-indigo-subtle">
                                        <i class="ti ti-user-check me-1"></i>{{ $nama }}
                                    </span>
                                    @endforeach
                                @else
                                    <div class="text-muted small italic">Daftar petugas tidak tersedia</div>
                                @endif
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Map Content --}}
            @if($item->latitude && $item->longitude)
            <div class="card card-premium border-0 shadow-sm overflow-hidden bg-white mb-4">
                 <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-map-pin me-2 text-red"></i> Lokasi Spesifik Pengambilan Sampel
                    </h3>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary btn-pill">
                        <i class="ti ti-external-link me-1"></i>Buka di G-Maps
                    </a>
                </div>
                <div class="card-body p-0">
                    <div id="full-map" style="height:350px; width:100%; border-top: 1px solid #f1f5f9;"></div>
                    <div class="p-3 bg-light-soft border-top">
                        <span class="text-muted small fw-bold">KOORDINAT:</span>
                        <span class="fw-mono ms-2 badge bg-white border text-dark">{{ $item->latitude }}, {{ $item->longitude }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- SEKSI LABORATORIUM — Terintegrasi penuh di bawah data lapangan        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="mt-4 animate-fade-in">
        @if($item->laboratorium)
            {{-- ── HASIL LAB SUDAH ADA: Tampilkan detail lengkap secara terintegrasi ── --}}
            @php
                $lab = $item->laboratorium;
                $statusUji = trim($lab->hasil_uji);
                $stColor = match(true) {
                    strcasecmp($statusUji, 'Positif') === 0     => 'danger',
                    strcasecmp($statusUji, 'Negatif') === 0 || strcasecmp($statusUji, 'NIHIL') === 0 => 'success',
                    strcasecmp($statusUji, 'Inkonklusif') === 0 => 'warning',
                    default                                     => 'secondary',
                };
                
                // Patogen logic
                $kelompok_patogen = $lab->kelompok_patogen;
                $pIcon = 'ti-shield-check';
                $pColor = 'text-success';
                if ($kelompok_patogen && $kelompok_patogen !== 'Nihil' && $kelompok_patogen !== 'Tidak Ada Patogen') {
                    $pColor = 'text-danger';
                    $pIcon = match($kelompok_patogen) {
                        'Parasit' => 'ti-bug',
                        'Bakteri' => 'ti-circle',
                        'Virus'   => 'ti-virus',
                        'Jamur'   => 'ti-leaf',
                        default   => 'ti-alert-circle',
                    };
                }
            @endphp

            <div class="card border-0 shadow-sm overflow-hidden mb-5">
                {{-- Header Hasil Uji --}}
                <div class="card-header bg-{{ $stColor }}-lt border-bottom d-flex align-items-center justify-content-between py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-{{ $stColor }} text-white p-2 rounded-3 shadow-sm">
                            <i class="ti ti-flask fs-3"></i>
                        </div>
                        <div>
                            <div class="text-{{ $stColor }} small fw-bold text-uppercase" style="letter-spacing:.05em;">Hasil Pemeriksaan Laboratorium</div>
                            <div class="fw-bold fs-4">Kode Sampel: {{ $lab->kode_sampel }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('pelaksanaan.print', $item->id) }}" target="_blank" class="btn btn-white btn-pill shadow-sm">
                            <i class="ti ti-printer me-1"></i> Cetak PDF
                        </a>
                        <span class="badge bg-{{ $stColor }} text-white px-4 py-2 fs-6 rounded-pill shadow-sm">
                            <i class="ti {{ $stColor === 'danger' ? 'ti-alert-octagon' : ($stColor === 'success' ? 'ti-shield-check' : 'ti-help-circle') }} me-1"></i> 
                            {{ strtoupper($lab->hasil_uji) }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- Diagnosa Utama --}}
                        <div class="col-md-7 border-end p-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Penetapan Diagnosis Akhir</label>
                            <div class="p-4 bg-{{ $stColor }}-lt rounded-4 border-start border-{{ $stColor }} border-4 shadow-sm mb-4">
                                <div class="h2 fw-extrabold text-{{ $stColor }} mb-0">{{ $lab->diagnosis_akhir ?? 'N/A' }}</div>
                                <div class="text-muted small fw-bold">DETERMINASI LABORATORIUM</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 h-100 border">
                                        <div class="text-muted small fw-bold mb-1">HPIK DIUJI</div>
                                        <div class="fw-bold text-dark">{{ $lab->jenis_hpik_diuji ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4 h-100 border">
                                        <div class="text-muted small fw-bold mb-1">METODE UJI</div>
                                        <div class="fw-bold text-dark">{{ $lab->metode_uji ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="text-muted small fw-bold mb-3 text-uppercase">Rincian Deteksi Per Patogen</div>
                                        <div class="row g-2">
                                            @php
                                                $patogens = [
                                                    ['label' => 'Parasit', 'val' => $lab->hasil_parasit, 'icon' => 'ti-bug'],
                                                    ['label' => 'Bakteri',  'val' => $lab->hasil_bakteri, 'icon' => 'ti-circle'],
                                                    ['label' => 'Virus',    'val' => $lab->hasil_virus,   'icon' => 'ti-virus'],
                                                    ['label' => 'Jamur',    'val' => $lab->hasil_jamur,   'icon' => 'ti-leaf'],
                                                ];
                                            @endphp
                                            @foreach($patogens as $p)
                                            <div class="col-6 col-md-3">
                                                <div class="text-center p-2 rounded-3 border bg-white shadow-sm">
                                                    <i class="ti {{ $p['icon'] }} {{ strpos($p['val'], 'Positif') !== false ? 'text-danger' : 'text-success' }} mb-1"></i>
                                                    <div class="small fw-bold">{{ $p['label'] }}</div>
                                                    <div class="small {{ strpos($p['val'], 'Positif') !== false ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size: 0.65rem;">
                                                        {{ $p['val'] ?: 'Negatif' }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $labPenguji = $lab->lab_penguji;
                                @endphp
                                <div class="col-12 mt-2">
                                    <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm"><i class="ti ti-building-lab text-primary fs-3"></i></div>
                                            <div class="me-4 border-end pe-4">
                                                <div class="text-muted small fw-bold">LABORATORIUM PENGUJI</div>
                                                <div class="fw-bold text-dark">{{ strtoupper($labPenguji) }}</div>
                                            </div>
                                            <div>
                                                <div class="text-muted small fw-bold">NAMA PETUGAS UJI</div>
                                                <div class="fw-bold text-dark">{{ strtoupper($lab->nama_petugas_uji ?? '-') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end pe-2">
                                            <div class="text-muted small fw-bold">KODE SAMPEL</div>
                                            <div class="small fw-semibold text-dark">{{ $lab->kode_sampel }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Statistik & Timeline --}}
                        <div class="col-md-5 p-4 bg-light-soft">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-3">Parameter & Timeline</label>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 bg-white rounded-4 border text-center shadow-sm">
                                        <div class="text-azure small fw-bold">PREVALENSI</div>
                                        <div class="h2 fw-extrabold text-azure mb-0">{{ $lab->prevalensi ?? '0' }}%</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-white rounded-4 border text-center shadow-sm">
                                        <div class="text-indigo small fw-bold">INSIDENSI</div>
                                        <div class="h2 fw-extrabold text-indigo mb-0">{{ $lab->insidensi ?? '0' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="vertical-timeline position-relative ps-4 mt-2">
                                <div class="timeline-item mb-4 position-relative">
                                    <div class="timeline-point bg-azure border-white border-4 rounded-circle position-absolute" style="width:16px;height:16px;left:-25px;top:2px;z-index:2;box-shadow:0 0 0 4px rgba(32,107,196,0.1)"></div>
                                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:0.65rem">MULAI PENELITIAN</div>
                                    <div class="fw-bold text-dark">{{ $lab->tanggal_uji->format('d M Y') }}</div>
                                </div>
                                <div class="timeline-item position-relative">
                                    <div class="timeline-point bg-success border-white border-4 rounded-circle position-absolute" style="width:16px;height:16px;left:-25px;top:2px;z-index:2;box-shadow:0 0 0 4px rgba(47,179,68,0.1)"></div>
                                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:0.65rem">PENETAPAN HASIL</div>
                                    <div class="fw-bold text-dark">{{ $lab->tanggal_hasil ? $lab->tanggal_hasil->format('d M Y') : 'Selesai' }}</div>
                                </div>
                                <div class="timeline-line position-absolute bg-light" style="width:2px;top:10px;bottom:0;left:-18px;height:40px"></div>
                            </div>

                            @if(Auth::user()->isPusat() || Auth::user()->isDeveloper() || Auth::user()->isBbkhit() || (Auth::user()->isBkhit() && $item->perencanaan->user_id == Auth::id()))
                            <div class="mt-4 pt-4 border-top d-flex gap-2">
                                <a href="{{ route('laboratorium.edit', $lab->id) }}" class="btn btn-warning btn-sm btn-pill px-3">
                                    <i class="ti ti-edit me-1"></i>Edit Hasil Lab
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-pill px-3"
                                    onclick="confirmAction('{{ route('laboratorium.destroy', $lab->id) }}', 'Hapus hasil lab ini?', 'DELETE', 'btn-danger')">
                                    <i class="ti ti-trash me-1"></i>Hapus
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- ── BELUM ADA HASIL LAB: Tampilkan form input ── --}}
            @php $canInputLab = Auth::user()->isBkhit() || Auth::user()->isBbkhit() || Auth::user()->isPusat() || Auth::user()->isDeveloper(); @endphp

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-azure-lt border-bottom d-flex align-items-center gap-3 py-3 px-4">
                    <div class="bg-azure text-white p-2 rounded-3 shadow-sm">
                        <i class="ti ti-flask fs-3"></i>
                    </div>
                    <div>
                        <div class="text-azure small fw-bold text-uppercase" style="letter-spacing:.05em;">Pemeriksaan Laboratorium</div>
                        <div class="fw-bold fs-4">Input Hasil Pengujian</div>
                    </div>
                    <span class="ms-auto badge bg-warning-lt text-warning px-3 py-2">
                        <i class="ti ti-clock me-1"></i> Menunggu Hasil Lab
                    </span>
                </div>

                @if($canInputLab)
                <form action="{{ route('laboratorium.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pelaksanaan_id" value="{{ $item->id }}">

                    <div class="card-body p-4">
                        {{-- Info ringkas sampel --}}
                        <div class="alert alert-info border-0 rounded-3 mb-4">
                            <div class="d-flex gap-3 flex-wrap">
                                <span><i class="ti ti-fish me-1"></i><strong>Media Pembawa:</strong> {{ $item->jenis_ikan }}</span>
                                <span><i class="ti ti-virus me-1"></i><strong>Target HPIK:</strong> {{ $item->perencanaan->jenis_hpik ?? '-' }}</span>
                                <span><i class="ti ti-flask me-1"></i><strong>Jumlah Sampel:</strong> {{ $item->jumlah_sampel }} sampel</span>
                                <span><i class="ti ti-building me-1"></i><strong>Lab Rencana:</strong> {{ $item->perencanaan->lab_uji ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Baris 1: Identitas Pengujian --}}
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3"><i class="ti ti-id-badge me-2"></i>Identitas Pengujian</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label required fw-bold">Kode Sampel Lab</label>
                                        <input type="text" name="kode_sampel" class="form-control @error('kode_sampel') is-invalid @enderror fw-bold"
                                            value="{{ old('kode_sampel', 'LAB-'.date('Y').'-') }}" required>
                                        @error('kode_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required fw-bold">Laboratorium Penguji</label>
                                        <input type="text" name="lab_penguji" class="form-control"
                                            value="{{ old('lab_penguji', $item->perencanaan->lab_uji ?? '') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required fw-bold">Tanggal Pengujian</label>
                                        <input type="date" name="tanggal_uji" class="form-control"
                                            value="{{ old('tanggal_uji', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required fw-bold">Nama Petugas Uji</label>
                                        <input type="text" name="nama_petugas_uji" class="form-control"
                                            placeholder="Nama analis..." value="{{ old('nama_petugas_uji') }}" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label required fw-bold mb-2">Jenis Target HPIK Diuji</label>
                                        @php 
                                            $targetValues = old('jenis_hpik_diuji') 
                                                ? (is_array(old('jenis_hpik_diuji')) ? old('jenis_hpik_diuji') : explode(',', old('jenis_hpik_diuji')))
                                                : array_map('trim', explode(',', $item->perencanaan->jenis_hpik ?? ''));
                                        @endphp
                                        <select name="jenis_hpik_diuji[]" id="jenis_hpik_diuji_select" class="form-control" multiple required placeholder="Cari dan Pilih Jenis Target HPIK...">
                                            @foreach($jenis_penyakits ?? [] as $jp)
                                                @php $val = $jp->organisme_penyebab ?: $jp->nama; @endphp
                                                <option value="{{ $val }}" {{ in_array($val, $targetValues) ? 'selected' : '' }}>
                                                    {{ $jp->nama }} / {{ $jp->organisme_penyebab ?: '-' }} / {{ $jp->golongan ?: '-' }}
                                                </option>
                                            @endforeach
                                            {{-- Handle values that might not be in master data --}}
                                            @foreach($targetValues as $s)
                                                @if(!collect($jenis_penyakits)->contains(fn($jp) => ($jp->organisme_penyebab ?: $jp->nama) === $s) && !empty($s))
                                                     <option value="{{ $s }}" selected>{{ $s }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label required fw-bold mb-2">Metode Uji Utama</label>
                                        <select name="metode_uji[]" id="metode_uji_select" class="form-control" multiple required>
                                            @php
                                                $selectedMetode = is_array(old('metode_uji')) ? old('metode_uji') : explode(',', old('metode_uji', ''));
                                                $selectedMetode = array_map('trim', $selectedMetode);
                                            @endphp
                                            @foreach($metode_ujis ?? [] as $metode)
                                                <option value="{{ $metode->nama }}" {{ in_array($metode->nama, $selectedMetode) ? 'selected' : '' }}>{{ $metode->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Tanggal Hasil Keluar</label>
                                        <input type="date" name="tanggal_hasil" class="form-control" value="{{ old('tanggal_hasil') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Data Contoh Uji --}}
                            <div class="col-12">
                                <h5 class="fw-bold text-purple mb-3"><i class="ti ti-ruler-measure me-2"></i>Data Contoh Uji</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Panjang (cm)</label>
                                        <input type="text" name="panjang" class="form-control" placeholder="e.g., 5" value="{{ old('panjang') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Berat (gram)</label>
                                        <input type="text" name="berat" class="form-control" placeholder="e.g., 6" value="{{ old('berat') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Jumlah Kematian</label>
                                        <input type="text" name="jumlah_kematian" class="form-control" placeholder="e.g., 10" value="{{ old('jumlah_kematian') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Padat Tebar</label>
                                        <input type="text" name="padat_tebar" class="form-control" placeholder="e.g., 500" value="{{ old('padat_tebar') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Asal Benih/Induk</label>
                                        <input type="text" name="asal_benih_induk" class="form-control" placeholder="..." value="{{ old('asal_benih_induk') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Gejala Klinis</label>
                                        <textarea name="gejala_klinis" class="form-control" rows="2" placeholder="Deskripsi...">{{ old('gejala_klinis') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Baris 2: Hasil Per Patogen + Hasil Akhir --}}
                            <div class="col-md-6">
                                <h5 class="fw-bold text-azure mb-3"><i class="ti ti-virus me-2"></i>Hasil Per Patogen</h5>
                                <div class="mb-4">
                                    <label class="form-label fw-bold required">Kelompok Patogen Ditemukan</label>
                                    <select name="kelompok_patogen" class="form-select @error('kelompok_patogen') is-invalid @enderror" required>
                                        <option value="">— Pilih Kelompok Patogen —</option>
                                        <option value="Parasit" {{ old('kelompok_patogen') == 'Parasit' ? 'selected' : '' }}>Parasit</option>
                                        <option value="Bakteri" {{ old('kelompok_patogen') == 'Bakteri' ? 'selected' : '' }}>Bakteri</option>
                                        <option value="Virus" {{ old('kelompok_patogen') == 'Virus' ? 'selected' : '' }}>Virus</option>
                                        <option value="Jamur" {{ old('kelompok_patogen') == 'Jamur' ? 'selected' : '' }}>Jamur</option>
                                        <option value="Nihil" {{ old('kelompok_patogen') == 'Nihil' ? 'selected' : '' }}>Nihil / Tidak Ada Patogen</option>
                                    </select>
                                    @error('kelompok_patogen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <label class="form-label required fw-bold">HASIL AKHIR KESELURUHAN</label>
                                    <select name="hasil_uji" class="form-select form-select-lg fw-bold" required>
                                        <option value="">— Pilih Hasil Akhir —</option>
                                         <option value="Negatif" {{ old('hasil_uji')==='Negatif' || old('hasil_uji')==='NIHIL' ?'selected':'' }}>✅ NEGATIF (Bebas HPIK)</option>
                                         <option value="Positif" {{ strcasecmp(old('hasil_uji'), 'Positif') === 0 ?'selected':'' }}>🔴 POSITIF (Terdeteksi HPIK)</option>
                                         <option value="Inkonklusif" {{ strcasecmp(old('hasil_uji'), 'Inkonklusif') === 0 ?'selected':'' }}>⚠️ INKONKLUSIF</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Baris 2: Kalkulator Prevalensi --}}
                            <div class="col-md-6">
                                <h5 class="fw-bold text-orange mb-3"><i class="ti ti-calculator me-2"></i>Kalkulator Prevalensi & Insidensi</h5>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Jumlah Ikan Infeksi</label>
                                        <div class="input-group">
                                            <input type="number" id="jml_terinfeksi" name="jumlah_ikan_terinfeksi"
                                                class="form-control" min="0" placeholder="0"
                                                value="{{ old('jumlah_ikan_terinfeksi') }}" oninput="hitungOtomatis()">
                                            <span class="input-group-text bg-primary-lt border-light text-primary fw-bold text-uppercase px-2" style="font-size: 0.7rem;">Sampel</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Sampel Diperiksa</label>
                                        <div class="input-group">
                                            <input type="number" id="jml_diperiksa" name="jumlah_sampel_diperiksa"
                                                class="form-control" min="1"
                                                value="{{ old('jumlah_sampel_diperiksa', $item->jumlah_sampel) }}" oninput="hitungOtomatis()">
                                            <span class="input-group-text bg-primary-lt border-light text-primary fw-bold text-uppercase px-2" style="font-size: 0.7rem;">Sampel</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Jumlah Kolam</label>
                                        <input type="number" id="jml_kolam" name="jumlah_kolam_uji" class="form-control" oninput="hitungOtomatis()">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Periode Pengamatan</label>
                                        <input type="number" id="periode" name="periode_pengamatan" class="form-control" oninput="hitungOtomatis()">
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 bg-azure text-white rounded-3 text-center">
                                            <div class="small fw-bold opacity-75">PREVALENSI</div>
                                            <div class="h3 fw-bold mb-0" id="display_prevalensi">0.00%</div>
                                            <input type="hidden" name="prevalensi" id="hasil_prevalensi" value="{{ old('prevalensi') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 border border-orange border-2 border-dashed rounded-3 text-center">
                                            <div class="small fw-bold text-orange">INSIDENSI</div>
                                            <div class="h4 fw-bold text-orange mb-0" id="display_insidensi">0.000000</div>
                                            <input type="hidden" name="insidensi" id="hasil_insidensi" value="{{ old('insidensi') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Diagnosis & Tombol Simpan --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Diagnosis Akhir / Keterangan</label>
                                <textarea name="diagnosis_akhir" class="form-control" rows="3"
                                    placeholder="Catatan tambahan hasil pengujian atau deviasi prosedur...">{{ old('diagnosis_akhir') }}</textarea>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="d-grid w-100 gap-2">
                                    <button type="submit" class="btn btn-primary btn-pill px-4 shadow-sm">
                                        <i class="ti ti-device-floppy me-2"></i>Simpan Hasil Lab
                                    </button>
                                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-link link-secondary text-center">Kembali ke Daftar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="card-body text-center py-5">
                    <div class="bg-light p-4 rounded-circle d-inline-block mb-3 opacity-50">
                        <i class="ti ti-microscope text-muted" style="font-size:4rem;"></i>
                    </div>
                    <h4 class="fw-bold text-muted">Menunggu Hasil Lab</h4>
                    <p class="text-muted small">Sampel telah diterima. Laboratorium sedang memproses hasil pengujian.</p>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    .ts-wrapper .ts-control { border-radius: 0.5rem; border: 1px solid #e2e8f0; }
    .ts-wrapper .item { background: #e0e7ff; color: #4338ca; border-radius: 100px; padding: 2px 10px; font-size: 0.8rem; margin: 2px; }
    .ts-wrapper .item .remove { margin-left: 5px; cursor: pointer; color: #4338ca; text-decoration: none; }
</style>
<script>
function hitungOtomatis() {
    var terinfeksi = parseFloat(document.getElementById('jml_terinfeksi')?.value);
    var diperiksa  = parseFloat(document.getElementById('jml_diperiksa')?.value);
    var kolam      = parseFloat(document.getElementById('jml_kolam')?.value);
    var periode    = parseFloat(document.getElementById('periode')?.value);
    var displayPrev = document.getElementById('display_prevalensi');
    var inputPrev   = document.getElementById('hasil_prevalensi');
    var displayIns  = document.getElementById('display_insidensi');
    var inputIns    = document.getElementById('hasil_insidensi');
    if (!displayPrev) return; // elemen tidak ada (lab sudah diinput)
    if (!isNaN(terinfeksi) && !isNaN(diperiksa) && diperiksa > 0) {
        var prev = (terinfeksi / diperiksa) * 100;
        displayPrev.textContent = prev.toFixed(2) + '%';
        inputPrev.value = prev.toFixed(2);
    } else {
        displayPrev.textContent = '0.00%';
        if (inputPrev) inputPrev.value = '';
    }
    if (!isNaN(terinfeksi) && !isNaN(kolam) && !isNaN(periode) && kolam > 0 && periode > 0) {
        var ins = terinfeksi / (kolam * periode);
        displayIns.textContent = ins.toFixed(6);
        if (inputIns) inputIns.value = ins.toFixed(6);
    } else {
        displayIns.textContent = '0.000000';
        if (inputIns) inputIns.value = '';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    hitungOtomatis();
    
    if(document.getElementById('metode_uji_select')) {
        new TomSelect('#metode_uji_select', {
            dropdownParent: 'body',
            maxOptions: 100,
            plugins: ['remove_button'],
            create: true,
            persist: false,
        });
    }
    
    if(document.getElementById('jenis_hpik_diuji_select')) {
        new TomSelect('#jenis_hpik_diuji_select', {
            dropdownParent: 'body',
            maxOptions: 100,
            plugins: ['remove_button'],
            create: true,
            persist: false,
            render: {
                item: function(data, escape) {
                    return '<div>' + escape(data.value) + '</div>';
                }
            }
        });
    }
});
</script>
@endpush

@if($item->latitude && $item->longitude)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #full-map {
        z-index: 10;
        /* Ocean / sea background colour */
        background: linear-gradient(135deg, #b8d9ed 0%, #9fcde6 50%, #87bedc 100%);
    }
    #full-map .leaflet-tile-pane { display: none !important; }
</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var markerPos = [{{ $item->latitude }}, {{ $item->longitude }}];
        var fullMap = L.map('full-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            attributionControl: false,
            maxBounds: [[-15, 88], [16, 152]],
            minZoom: 4
        }).setView([-2.5, 118], 5);

        // Attribution kecil
        L.control.attribution({ position: 'bottomleft', prefix: false })
            .addAttribution('Geometri: <a href="https://github.com/ardian28/GeoJson-Indonesia-38-Provinsi" target="_blank">Ardian28/BIG</a>')
            .addTo(fullMap);

        // Load GeoJSON Provinsi dari lokal
        fetch('{{ asset('geojson/indonesia-provinces.geojson') }}')
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    style: {
                        fillColor:   '#e8edf2',
                        fillOpacity: 0.7,
                        color:       '#94a3b8',
                        weight:      0.8
                    },
                    interactive: false
                }).addTo(fullMap);

                // Setelah provinsi dimuat, zoom ke marker
                fullMap.setView(markerPos, 10);
            })
            .catch(() => {
                // Fallback: langsung zoom jika GeoJSON gagal
                fullMap.setView(markerPos, 10);
            });

        // Marker lokasi pengambilan sampel
        L.circleMarker(markerPos, {
            radius: 12,
            fillColor: '#206bc4',
            color: '#fff',
            weight: 3,
            fillOpacity: 0.9
        }).addTo(fullMap)
          .bindPopup('<div class="fw-bold">Lokasi Pengambilan</div><div>{{ $item->lokasi_pengambilan_sampel }}</div>')
          .openPopup();

        // Fix leaflet map sizing in cards
        setTimeout(() => fullMap.invalidateSize(), 300);
    });
</script>
@endpush
@endif

