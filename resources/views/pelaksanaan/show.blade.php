@extends('layouts.app')

@section('title', 'Detail Pelaksanaan Lapangan')

@section('content')
<div class="row detail-header align-items-center">
    <div class="col">
        <div class="detail-subtitle">Modul Pelaksanaan Lapangan</div>
        <h1 class="detail-title">{{ $item->jenis_ikan }}</h1>
        <div class="detail-subtitle">
            <i class="ti ti-map-pin me-1"></i>{{ $item->lokasi_pengambilan_sampel }}
        </div>
    </div>
    <div class="col-auto">
        <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-secondary btn-pill">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- ── Kiri: detail lapangan ── --}}
    <div class="col-lg-8">
        {{-- Card 1: Referensi Perencanaan --}}
        <div class="card bg-primary-lt border-0 mb-4 shadow-sm overflow-hidden">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <div class="bg-primary text-white p-2 rounded-3">
                            <i class="ti ti-clipboard-list" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.7rem;">Detail Perencanaan Terkait</div>
                        <div class="fw-bold text-primary">{{ $item->perencanaan->jenis_mp }} — {{ $item->perencanaan->kab_kota }}</div>
                        <div class="small text-muted">Target: {{ $item->perencanaan->jenis_hpik }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('perencanaan.show', $item->perencanaan_id) }}" class="btn btn-sm btn-white">Lihat Perencanaan</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Lokasi & Waktu --}}
        <div class="card card-premium mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Waktu & Lokasi Pengambilan</h3>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="info-item">
                            <div class="info-icon"><i class="ti ti-map-2"></i></div>
                            <div class="info-content">
                                <label>Lokasi Detail</label>
                                <span>{{ $item->lokasi_pengambilan_sampel }}</span>
                                <div class="sub-text">{{ $item->perencanaan->provinsi }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="info-item">
                            <div class="info-icon"><i class="ti ti-calendar-event"></i></div>
                            <div class="info-content">
                                <label>Tanggal Pemantauan</label>
                                <span>{{ optional($item->tanggal_pemantauan)->format('d F Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Data Ikan & Sampel --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card card-premium h-100">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Informasi Ikan</h3>
                    </div>
                    <div class="card-body pt-4">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon bg-azure-lt text-azure"><i class="ti ti-fish"></i></div>
                                <div class="info-content">
                                    <label>Jenis Ikan</label>
                                    <span>{{ $item->jenis_ikan ?? '-' }}</span>
                                    @if($item->nama_latin)
                                        <div class="sub-text fst-italic">{{ $item->nama_latin }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-6">
                                    <label class="small text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Panjang Rata2</label>
                                    <div class="fw-bold">{{ $item->panjang_cm ? $item->panjang_cm . ' cm' : '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Berat Rata2</label>
                                    <div class="fw-bold">{{ $item->berat_gram ? $item->berat_gram . ' gram' : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-premium h-100">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Detail Sampel</h3>
                    </div>
                    <div class="card-body pt-4">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon bg-orange-lt text-orange"><i class="ti ti-flask"></i></div>
                                <div class="info-content">
                                    <label>Jumlah Sampel</label>
                                    <span class="h3 mb-0 text-primary">{{ $item->jumlah_sampel }} <small class="fw-normal text-muted fs-6">Ekor</small></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon bg-red-lt text-red"><i class="ti ti-activity-heartbeat"></i></div>
                                <div class="info-content">
                                    <label>Kematian</label>
                                    <span class="{{ $item->jumlah_kematian > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        {{ $item->jumlah_kematian ?? 0 }} Ekor
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gejala Klinis --}}
        @if($item->gejala_klinis)
        <div class="card card-premium mb-4 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex">
                    <div class="me-3">
                        <div class="bg-warning-lt p-2 rounded-circle">
                            <i class="ti ti-alert-triangle text-warning" style="font-size: 1.25rem;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-warning fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Gejala Klinis Teramati</div>
                        <div class="mt-1 text-muted">{{ $item->gejala_klinis }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Pengambil Sampel --}}
        @if($item->pengambil_sampel && count($item->pengambil_sampel) > 0)
        <div class="card card-premium mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Petugas Pengambil Contoh Uji</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($item->pengambil_sampel as $nama)
                        <div class="badge bg-blue-lt px-3 py-2 fs-6">
                            <i class="ti ti-user-check me-1"></i>{{ $nama }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Kanan: Sidebar ── --}}
    <div class="col-lg-4">
        {{-- Hasil Laboratorium --}}
        <div class="card card-premium mb-4 overflow-hidden">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Status Laboratorium</h3>
            </div>
            <div class="card-body text-center pt-3">
                @if($item->laboratorium)
                    @php
                        $hasilColor = match($item->laboratorium->hasil_uji) {
                            'Positif' => ['bg-danger-lt text-danger', 'ti-circle-x', 'Terdeteksi'],
                            'Negatif' => ['bg-success-lt text-success', 'ti-circle-check', 'Aman'],
                            default   => ['bg-azure-lt text-azure', 'ti-circle', 'Hasil Uji'],
                        };
                    @endphp
                    <div class="p-4 rounded-4 {{ $hasilColor[0] }} mb-4">
                        <i class="ti {{ $hasilColor[1] }} mb-2" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold mb-0 text-uppercase">{{ $item->laboratorium->hasil_uji }}</h2>
                        <div class="small fw-bold opacity-75">{{ $hasilColor[2] }}</div>
                    </div>

                    <table class="table table-sm table-borderless text-start">
                        <tr>
                            <td class="text-muted py-1 small">No. Pengujian</td>
                            <td class="fw-bold py-1 text-end">{{ $item->laboratorium->no_pengujian ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 small">Metode Uji</td>
                            <td class="fw-bold py-1 text-end">{{ $item->laboratorium->metode_uji ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 small">Tgl Uji</td>
                            <td class="fw-bold py-1 text-end">{{ optional($item->laboratorium->tanggal_uji)->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    </table>

                    @if($item->laboratorium->keterangan)
                        <div class="mt-2 p-2 bg-light rounded-3 small text-muted italic">
                            "{{ $item->laboratorium->keterangan }}"
                        </div>
                    @endif
                @else
                    <div class="empty py-4">
                        <div class="empty-icon text-muted">
                            <i class="ti ti-flask-off" style="font-size: 3rem;"></i>
                        </div>
                        <p class="empty-title">Belum Diuji</p>
                        <p class="empty-subtitle">Sampel belum masuk ke tahap pengujian laboratorium.</p>
                        <div class="mt-3">
                            <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-azure w-100">
                                <i class="ti ti-flask me-1"></i>Input Hasil Lab
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mini Map --}}
        @if($item->latitude && $item->longitude)
        <div class="card card-premium mb-4 overflow-hidden p-0">
            <div id="mini-map" style="height:250px;"></div>
            <div class="card-body py-2 px-3 bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted fw-bold"><i class="ti ti-map-pin me-1"></i>KOORDINAT</span>
                    <span class="small fw-mono">{{ $item->latitude }}, {{ $item->longitude }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="card card-premium">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">ID Transaksi</span>
                    <span class="fw-mono small">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Waktu Input</span>
                    <span class="small">{{ $item->created_at->format('d/m/y H:i') }}</span>
                </div>
                <div class="d-divider my-2"></div>
                <div class="d-grid gap-2">
                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-ghost-secondary w-100">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($item->latitude && $item->longitude)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { background: #f8fafc; }
</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var miniMap = L.map('mini-map', { zoomControl: false, scrollWheelZoom: false })
        .setView([{{ $item->latitude }}, {{ $item->longitude }}], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, subdomains: 'abcd'
    }).addTo(miniMap);
    L.circleMarker([{{ $item->latitude }}, {{ $item->longitude }}], {
        radius: 8, fillColor: '#206bc4', color: 'white', weight: 3, fillOpacity: 1
    }).addTo(miniMap);
</script>
@endpush
@endif

