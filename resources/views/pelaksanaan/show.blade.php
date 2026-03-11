@extends('layouts.app')

@section('title', 'Detail Pelaksanaan Lapangan')

@section('content')
<div class="animate-fade-in px-2">
    {{-- High-End Page Header --}}
    <div class="row align-items-center mb-5 g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-4">
                <div class="bg-azure text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                    <i class="ti ti-broadcast fs-1"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-azure-lt text-azure px-3 fs-6 rounded-pill">LAPORAN LAPANGAN</span>
                        <span class="badge bg-light text-muted px-3 fs-6 rounded-pill border">ID: #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">{{ $item->jenis_ikan }}</h1>
                    <div class="text-muted fs-3 d-flex align-items-center">
                        <i class="ti ti-map-pin me-2 text-danger"></i>{{ $item->lokasi_pengambilan_sampel }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                <a href="{{ route('pelaksanaan.index') }}" class="btn btn-white btn-pill px-4 border-0">
                    <i class="ti ti-list me-2"></i>Daftar
                </a>
                <a href="{{ route('perencanaan.show', $item->perencanaan_id) }}" class="btn btn-primary btn-pill px-4 border-0">
                    <i class="ti ti-file-text me-2"></i>Lihat Rencana
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Field Data Intelligence --}}
        <div class="col-lg-8">
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
                                <div class="col-6 text-center border-end">
                                    <div class="text-muted small fw-bold">PANJANG RATA2</div>
                                    <div class="h2 fw-extrabold text-azure mb-0">{{ $item->panjang_cm ?? '0' }} <span class="fs-6 fw-normal">cm</span></div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="text-muted small fw-bold">BERAT RATA2</div>
                                    <div class="h2 fw-extrabold text-azure mb-0">{{ $item->berat_gram ?? '0' }} <span class="fs-6 fw-normal">g</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 p-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Metrik Pengambilan</label>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="card bg-primary-lt border-0 rounded-4 p-3 shadow-inner">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="text-primary small fw-bold mb-1">JUMLAH SAMPEL</div>
                                                <div class="h1 fw-extrabold text-primary mb-0">{{ $item->jumlah_sampel }} <span class="fs-4">Sampel</span></div>
                                            </div>
                                            <i class="ti ti-flask text-primary opacity-25" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card {{ $item->jumlah_kematian > 0 ? 'bg-red-lt' : 'bg-green-lt' }} border-0 rounded-4 p-3 shadow-inner">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="text-{{ $item->jumlah_kematian > 0 ? 'danger' : 'success' }} small fw-bold mb-1">ANGKA KEMATIAN</div>
                                                <div class="h1 fw-extrabold text-{{ $item->jumlah_kematian > 0 ? 'danger' : 'success' }} mb-0">{{ $item->jumlah_kematian ?? 0 }} <span class="fs-4">Ekor</span></div>
                                            </div>
                                            <i class="ti ti-activity-heartbeat text-{{ $item->jumlah_kematian > 0 ? 'danger' : 'success' }} opacity-25" style="font-size: 3rem;"></i>
                                        </div>
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
                                <i class="ti ti-eye me-2 text-warning"></i> Observasi Klinis Lapangan
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-0">
                            @if($item->gejala_klinis)
                            <div class="p-3 bg-warning-lt rounded-4 border-start border-warning border-4 mt-2">
                                <div class="fw-bold text-warning mb-1 small text-uppercase tracking-wider">GEJALA TERAMATI:</div>
                                <p class="mb-0 text-dark-emphasis fw-medium italic">"{{ $item->gejala_klinis }}"</p>
                            </div>
                            @else
                            <div class="text-center py-4 bg-light rounded-4 border border-dashed text-muted fst-italic">
                                Tidak ada gejala klinis yang dilaporkan oleh petugas.
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

        {{-- Kanan: Sidebar Analytics --}}
        <div class="col-lg-4">
            {{-- Hasil Laboratory Dashboard --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden border-top border-azure border-4 animate-scale-up">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                        <i class="ti ti-flask me-2 text-azure"></i> STATUS PENGUJIAN LAB
                    </h3>
                </div>
                <div class="card-body p-4 text-center">
                    @if($item->laboratorium)
                        @php
                            $labConfig = match($item->laboratorium->hasil_uji) {
                                'Positif' => ['bg'=>'danger',  'icon'=>'ti-alert-octagon', 'label'=>'TERDETEKSI / POSITIF'],
                                'Negatif' => ['bg'=>'success', 'icon'=>'ti-shield-check',  'label'=>'SAMPEL AMAN / NEGATIF'],
                                default   => ['bg'=>'warning', 'icon'=>'ti-help-circle',   'label'=>'DALAM PROSES / RAGU'],
                            };
                        @endphp
                        
                        <div class="mb-4">
                            <div class="p-4 rounded-4 bg-{{ $labConfig['bg'] }}-lt border border-{{ $labConfig['bg'] }} mb-3 shadow-inner">
                                <i class="ti {{ $labConfig['icon'] }} text-{{ $labConfig['bg'] }} mb-2" style="font-size: 5rem;"></i>
                                <h1 class="fw-extrabold mb-0 text-{{ $labConfig['bg'] }}">{{ strtoupper($item->laboratorium->hasil_uji) }}</h1>
                                <div class="badge bg-{{ $labConfig['bg'] }} text-white px-3 py-1 rounded-pill small mt-2">{{ $labConfig['label'] }}</div>
                            </div>
                        </div>

                        <div class="list-group list-group-flush text-start border rounded-4 overflow-hidden shadow-sm bg-white mb-3">
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small fw-bold">NO PENGUJIAN</span>
                                <span class="fw-mono">{{ $item->laboratorium->no_pengujian ?? '—' }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small fw-bold">METODE UJI</span>
                                <span class="badge bg-azure-lt">{{ $item->laboratorium->metode_uji ?? '—' }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small fw-bold">TANGGAL UJI</span>
                                <span>{{ $item->laboratorium->tanggal_uji->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if($item->laboratorium->keterangan)
                            <div class="text-start p-3 bg-light rounded-4 italic small text-muted border border-dashed">
                                <i class="ti ti-quote me-2"></i>{{ $item->laboratorium->keterangan }}
                            </div>
                        @endif
                    @else
                        <div class="py-5">
                            <div class="bg-light p-4 rounded-circle d-inline-block mb-3 opacity-50">
                                <i class="ti ti-microscope text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold text-muted">Awaiting Lab Analysis</h4>
                            <p class="text-muted small px-3">Sampel telah diterima. Laboratorium belum menerbitkan laporan hasil uji resmi.</p>
                            @if(Auth::user()->isUpt() || Auth::user()->isLaborat())
                            <div class="mt-4 px-3">
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-azure w-100 btn-pill shadow-sm fw-bold">
                                    <i class="ti ti-plus me-1"></i>INPUT HASIL LAB SEKARANG
                                </a>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Metadata & Actions --}}
            <div class="card card-premium shadow-sm border-0 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-bold text-uppercase tracking-wider">Metode Sampling:</span>
                        <span class="badge bg-light text-dark border">{{ $item->metode_pengambilan_sampel }}</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('pelaksanaan.export') }}" class="btn btn-outline-success btn-pill btn-md fw-bold">
                            <i class="ti ti-file-export me-2"></i>DOWBNLOAD LAPORAN
                        </a>
                        <button class="btn btn-ghost-secondary btn-pill btn-sm btn-md" onclick="window.print()">
                            <i class="ti ti-printer me-2"></i>PRINT PDF
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <span class="text-muted small">Input on {{ $item->created_at->format('d M Y, H:i') }}</span>
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
        }).setView(markerPos, 14);

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

