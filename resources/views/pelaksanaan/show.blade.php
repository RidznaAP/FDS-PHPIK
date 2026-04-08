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
    <a href="{{ route('perencanaan.show', $item->perencanaan_id) }}" class="btn btn-primary">
        <i class="ti ti-file-text me-2"></i>Lihat Rencana
    </a>
    @if(Auth::user()->isPusat() || (Auth::user()->id === optional($item->perencanaan)->user_id))
    <a href="{{ route('pelaksanaan.edit', $item->id) }}" class="btn btn-warning">
        <i class="ti ti-edit me-2"></i>Edit
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="animate-fade-in px-2">

    <div class="row g-4">
        {{-- Full Width Field Data Intelligence --}}
        <div class="col-lg-12">
            {{-- Scientific Identity Board --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold text-muted small text-uppercase tracking-widest">
                            <i class="ti ti-microscope me-2 text-azure"></i> Informasi Biologis & Sampel
                        </h3>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-6 border-end p-4">
                            <div class="info-group mb-4">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-2">Klasifikasi Ikan</label>
                                <div class="p-3 bg-light rounded-4 d-flex align-items-center mb-3">
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
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Metrik Pengambilan & Asal</label>
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
                                <i class="ti ti-eye me-2 text-warning"></i> Observasi Klinis
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
                                <i class="ti ti-users me-2 text-indigo"></i> Petugas Pelaksana
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-0">
                             <div class="d-flex flex-wrap gap-2 mt-2">
                                @if($item->pengambil_sampel && count($item->pengambil_sampel) > 0)
                                    @foreach($item->pengambil_sampel as $nama)
                                    <span class="badge bg-indigo-lt p-2 px-3 fs-6 rounded-pill shadow-sm animate-scale-up">
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
                 <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-map-pin me-2 text-red"></i> Geo-Tagging Lokasi Pelaksanaan
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div id="full-map" style="height:400px; width:100%; border-top: 1px solid #f1f5f9;"></div>
                    <div class="p-3 bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold">LATITUDE:</span>
                            <span class="fw-mono ms-2 me-4">{{ $item->latitude }}</span>
                            <span class="text-muted small fw-bold">LONGITUDE:</span>
                            <span class="fw-mono ms-2">{{ $item->longitude }}</span>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-sm btn-white btn-pill">
                            <i class="ti ti-external-link me-1"></i>Buka di G-Maps
                        </a>
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
            {{-- ── HASIL LAB SUDAH ADA: Tampilkan detail lengkap ── --}}
            @php
                $lab = $item->laboratorium;
                $labConfig = match($lab->hasil_uji) {
                    'Positif'     => ['bg'=>'danger',  'icon'=>'ti-alert-octagon', 'label'=>'TERDETEKSI / POSITIF'],
                    'Negatif'     => ['bg'=>'success', 'icon'=>'ti-shield-check',  'label'=>'SAMPEL AMAN / NEGATIF'],
                    'Inkonklusif' => ['bg'=>'warning', 'icon'=>'ti-help-circle',   'label'=>'DALAM EVALUASI'],
                    default       => ['bg'=>'secondary','icon'=>'ti-clock',         'label'=>'BELUM ADA STATUS'],
                };
                $patogenMap = [
                    'hasil_parasit' => ['label'=>'Parasit', 'icon'=>'ti-bug'],
                    'hasil_bakteri' => ['label'=>'Bakteri',  'icon'=>'ti-circle'],
                    'hasil_virus'   => ['label'=>'Virus',    'icon'=>'ti-virus'],
                    'hasil_jamur'   => ['label'=>'Jamur',    'icon'=>'ti-leaf'],
                ];
            @endphp

            <div class="card border-0 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="card-header bg-{{ $labConfig['bg'] }}-lt border-bottom d-flex align-items-center justify-content-between py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-{{ $labConfig['bg'] }} text-white p-2 rounded-3 shadow-sm">
                            <i class="ti ti-flask fs-3"></i>
                        </div>
                        <div>
                            <div class="text-{{ $labConfig['bg'] }} small fw-bold text-uppercase" style="letter-spacing:.05em;">Hasil Pemeriksaan Laboratorium</div>
                            <div class="fw-bold fs-4">Kode: {{ $lab->kode_sampel }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $labConfig['bg'] }} text-white px-4 py-2 fs-6 rounded-pill shadow-sm">
                            <i class="ti {{ $labConfig['icon'] }} me-1"></i> {{ $labConfig['label'] }}
                        </span>
                        @if(Auth::user()->isPusat() || Auth::user()->isBbkhit() || (Auth::user()->isBkhit() && $item->perencanaan->user_id == Auth::id()))
                        <div class="mt-2 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="confirmAction('{{ route('laboratorium.destroy', $lab->id) }}', 'Hapus hasil lab ini?', 'DELETE', 'btn-danger')">
                                <i class="ti ti-trash me-1"></i>Hapus Lab
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-3">
                        <i class="ti ti-microscope text-muted opacity-50" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Hasil Uji Laboratorium Tersedia</h3>
                    <p class="text-muted mb-4">Pengujian telah selesai dilaksanakan oleh <strong>{{ $lab->lab_penguji }}</strong> pada {{ $lab->tanggal_uji->format('d M Y') }}.</p>
                    
                    <a href="{{ route('laboratorium.show', $lab->id) }}" class="btn btn-lg btn-{{ $labConfig['bg'] }} btn-pill px-5 shadow-sm">
                        <i class="ti ti-external-link me-2"></i>Lihat Detail Lengkap Lab
                    </a>
                </div>
            </div>

        @else
            {{-- ── BELUM ADA HASIL LAB: Tampilkan form input ── --}}
            @php $canInputLab = Auth::user()->isBkhit() || Auth::user()->isBbkhit() || Auth::user()->isPusat(); @endphp

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
                                <span><i class="ti ti-fish me-1"></i><strong>Komoditas:</strong> {{ $item->jenis_ikan }}</span>
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
                                        <label class="form-label required fw-bold">Jenis HPIK Diuji</label>
                                        <input type="text" name="jenis_hpik_diuji" class="form-control"
                                            value="{{ old('jenis_hpik_diuji', $item->perencanaan->jenis_hpik ?? '') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required fw-bold">Metode Uji Utama</label>
                                        <select name="metode_uji" class="form-select" required>
                                            <option value="">— Pilih Metode —</option>
                                            @foreach(['PCR Konvensional','Real Time PCR (qPCR)','ELISA','Kultur Bakteri','Histopatologi','Lainnya'] as $m)
                                            <option value="{{ $m }}" {{ old('metode_uji') === $m ? 'selected' : '' }}>{{ $m }}</option>
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
                                        <option value="Negatif" {{ old('hasil_uji')==='Negatif'?'selected':'' }}>✅ NEGATIF (Bebas HPIK)</option>
                                        <option value="Positif" {{ old('hasil_uji')==='Positif'?'selected':'' }}>🔴 POSITIF (Terdeteksi HPIK)</option>
                                        <option value="Inkonklusif" {{ old('hasil_uji')==='Inkonklusif'?'selected':'' }}>⚠️ INKONKLUSIF</option>
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

@push('scripts')
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
document.addEventListener('DOMContentLoaded', hitungOtomatis);
</script>
@endpush

@if($item->latitude && $item->longitude)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { background: #f8fafc; border-radius: 0; }
    #full-map { z-index: 10; }
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
            attributionControl: false
        }).setView([-2.5, 118], 5);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19, subdomains: 'abcd'
        }).addTo(fullMap);

        // Custom Marker
        L.circleMarker(markerPos, {
            radius: 12,
            fillColor: '#206bc4',
            color: '#fff',
            weight: 3,
            fillOpacity: 0.9
        }).addTo(fullMap).bindPopup('<div class="fw-bold">Lokasi Pengambilan</div><div>{{ $item->lokasi_pengambilan_sampel }}</div>').openPopup();

        // Fix leaflet map sizing in cards
        setTimeout(() => {
            fullMap.invalidateSize();
        }, 300);
    });
</script>
@endpush
@endif

