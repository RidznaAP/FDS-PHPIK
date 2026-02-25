@extends('layouts.app')

@section('title', 'Detail Pelaksanaan Lapangan')
@section('page_title', 'Detail Pelaksanaan Lapangan')
@section('page_subtitle', $item->jenis_ikan . ' — ' . Str::limit($item->lokasi_pengambilan_sampel, 50))

@section('page_actions')
<a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-1"></i>Kembali
</a>
@endsection

@section('content')
<div class="row g-3">

    {{-- ── Kiri: detail lapangan ── --}}
    <div class="col-lg-8">

        {{-- Card 1: Referensi Perencanaan --}}
        <div class="card mb-3 bg-blue-lt">
            <div class="card-body py-2">
                <div class="row g-2 text-sm">
                    <div class="col-sm-3">
                        <div class="text-muted small">Wilayah</div>
                        <div class="fw-semibold">{{ $item->perencanaan->kab_kota }}, {{ $item->perencanaan->provinsi }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Media Pembawa</div>
                        <div class="fw-semibold">{{ $item->perencanaan->jenis_mp }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Target HPIK</div>
                        <div class="fw-semibold">{{ $item->perencanaan->jenis_hpik }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Status Perencanaan</div>
                        <span class="badge bg-success-lt text-success">{{ ucfirst($item->perencanaan->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Lokasi & Waktu --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-calendar me-2"></i>Lokasi & Waktu Pemantauan</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="text-muted small">Lokasi Pengambilan Sampel</div>
                        <div class="fw-semibold">{{ $item->lokasi_pengambilan_sampel }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Tanggal Pemantauan</div>
                        <div class="fw-semibold">{{ optional($item->tanggal_pemantauan)->format('d M Y') ?? '-' }}</div>
                    </div>
                    @if($item->latitude && $item->longitude)
                    <div class="col-12">
                        <div class="text-muted small mb-1">Koordinat GPS</div>
                        <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="badge bg-azure-lt text-azure text-decoration-none">
                            <i class="ti ti-map-pin me-1"></i>
                            {{ $item->latitude }}, {{ $item->longitude }}
                            <i class="ti ti-external-link ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Data Ikan --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-fish me-2"></i>Data Ikan (Contoh Uji)</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Jenis Ikan (Nama Lokal)</div>
                        <div class="fw-semibold">{{ $item->jenis_ikan ?? '-' }}</div>
                        @if($item->nama_latin)
                            <div class="text-muted small fst-italic">{{ $item->nama_latin }}</div>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Panjang Rata-rata</div>
                        <div class="fw-semibold">{{ $item->panjang_cm ? $item->panjang_cm . ' cm' : '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Berat Rata-rata</div>
                        <div class="fw-semibold">{{ $item->berat_gram ? $item->berat_gram . ' gram' : '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Asal Benih / Induk</div>
                        <div class="fw-semibold">{{ $item->asal_benih_induk ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Padat Tebar</div>
                        <div class="fw-semibold">{{ $item->padat_tebar ? $item->padat_tebar . ' ekor/m²' : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Sampel --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-test-pipe me-2"></i>Data Sampel</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Jumlah Sampel</div>
                        <div class="h4 text-primary">{{ $item->jumlah_sampel }} <small class="fs-6 text-muted">ekor</small></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Metode Pengambilan</div>
                        <div class="fw-semibold">{{ $item->metode_pengambilan_sampel }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Jumlah Kematian</div>
                        @if($item->jumlah_kematian > 0)
                            <div class="h4 text-danger">{{ $item->jumlah_kematian }} <small class="fs-6 text-muted">ekor</small></div>
                        @else
                            <div class="fw-semibold text-success">0 ekor</div>
                        @endif
                    </div>
                    @if($item->gejala_klinis)
                    <div class="col-12">
                        <div class="text-muted small">Gejala Klinis</div>
                        <div class="alert alert-warning small mb-0 mt-1">{{ $item->gejala_klinis }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 5: Pengambil Sampel --}}
        @if($item->pengambil_sampel && count($item->pengambil_sampel) > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-user-check me-2"></i>D. Identitas Pengambil Contoh Uji</h3>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    @foreach($item->pengambil_sampel as $nama)
                        <li class="fw-semibold">{{ $nama }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Kanan: Sidebar ── --}}
    <div class="col-lg-4">

        {{-- Hasil Laboratorium --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-flask me-2"></i>Hasil Laboratorium</h3>
            </div>
            <div class="card-body">
                @if($item->laboratorium)
                    @php
                        $hasilColor = match($item->laboratorium->hasil_uji) {
                            'Positif' => 'bg-danger text-white',
                            'Negatif' => 'bg-success text-white',
                            default   => 'bg-secondary text-white',
                        };
                    @endphp
                    <div class="text-center mb-3">
                        <span class="badge fs-5 px-4 py-2 {{ $hasilColor }}">
                            {{ $item->laboratorium->hasil_uji }}
                        </span>
                    </div>
                    <table class="table table-sm">
                        <tr><td class="text-muted">No. Pengujian</td><td class="fw-semibold">{{ $item->laboratorium->no_pengujian ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Metode Uji</td><td class="fw-semibold">{{ $item->laboratorium->metode_uji ?? '-' }}</td></tr>
                        <tr>
                            <td class="text-muted">Tanggal Uji</td>
                            <td class="fw-semibold">{{ optional($item->laboratorium->tanggal_uji)->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Terima</td>
                            <td class="fw-semibold">{{ optional($item->laboratorium->tanggal_terima)->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    </table>
                    @if($item->laboratorium->keterangan)
                        <div class="alert alert-info small mb-0">{{ $item->laboratorium->keterangan }}</div>
                    @endif
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="ti ti-flask-off" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>
                        Belum ada hasil laboratorium.
                    </div>
                    <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-primary w-100">
                        <i class="ti ti-flask me-1"></i>Input Hasil Lab
                    </a>
                @endif
            </div>
        </div>

        {{-- GPS Mini Map --}}
        @if($item->latitude && $item->longitude)
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title"><i class="ti ti-map-pin me-2"></i>Lokasi GPS</h3></div>
            <div class="card-body p-0" style="height:200px;">
                <div id="mini-map" style="height:100%;border-radius:0 0 6px 6px;"></div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Aksi</h3></div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('perencanaan.show', $item->perencanaan_id) }}" class="btn btn-outline-secondary">
                    <i class="ti ti-clipboard-list me-1"></i>Lihat Perencanaan
                </a>
                <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-list me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@if($item->latitude && $item->longitude)
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var miniMap = L.map('mini-map', { zoomControl: false, scrollWheelZoom: false })
    .setView([{{ $item->latitude }}, {{ $item->longitude }}], 13);
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 19, subdomains: 'abcd'
}).addTo(miniMap);
L.circleMarker([{{ $item->latitude }}, {{ $item->longitude }}], {
    radius: 10, fillColor: '#3b82f6', color: 'white', weight: 3, fillOpacity: 1
}).addTo(miniMap).bindPopup('{{ $item->lokasi_pengambilan_sampel }}').openPopup();
</script>
@endsection
@endif
