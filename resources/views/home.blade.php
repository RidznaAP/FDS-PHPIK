@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('page_actions')
    <span class="badge fs-6 px-3 py-2
        @if(Auth::user()->isUpt()) bg-success
        @elseif(Auth::user()->isBbkhit()) bg-warning text-dark
        @else bg-purple text-white @endif">
        {{ strtoupper(Auth::user()->role) }}
    </span>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; border-radius: 15px; }
</style>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Perencanaan</div>
                </div>
                <div class="h1 mb-3 mt-1">{{ $totalPerencanaan }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted small">Rencana pemantauan HPIK terdaftar</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Pelaksanaan</div>
                <div class="h1 mb-3 mt-1">{{ $totalPelaksanaan }}</div>
                <div class="text-muted small">Data lapangan masuk</div>
                <div class="progress progress-sm mt-2">
                    <div class="progress-bar bg-green" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Titik GIS</div>
                <div class="h1 mb-3 mt-1">{{ $totalGis }}</div>
                <div class="text-muted small">Lokasi dengan koordinat GPS</div>
                <div class="progress progress-sm mt-2">
                    <div class="progress-bar bg-cyan" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Menunggu Validasi</div>
                @php $waiting = \App\Models\Perencanaan::where('status', 'waiting')->count(); @endphp
                <div class="h1 mb-3 mt-1">{{ $waiting }}</div>
                <div class="text-muted small">Perencanaan perlu disetujui</div>
                <div class="progress progress-sm mt-2">
                    <div class="progress-bar bg-yellow" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Peta Sebaran Pemantauan HPIK</h5>
            </div>
            <div class="card-body">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions + Info --}}
<div class="row row-cards">
    {{-- Quick Actions --}}
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Menu Cepat</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(Auth::user()->isUpt())
                        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Buat Perencanaan Baru
                        </a>
                    @endif
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-clipboard-list me-1"></i> Daftar Perencanaan
                    </a>
                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-map-pin me-1"></i> Daftar Pelaksanaan
                    </a>
                    <a href="{{ route('laboratorium.index') }}" class="btn btn-outline-cyan">
                        <i class="ti ti-flask me-1"></i> Laboratorium
                    </a>
                    @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                        <a href="{{ route('evaluasi.index') }}" class="btn btn-outline-orange">
                            <i class="ti ti-chart-bar me-1"></i> Evaluasi
                        </a>
                    @endif
                    <a href="{{ route('peta.index') }}" class="btn btn-outline-teal">
                        <i class="ti ti-map me-1"></i> Peta GIS
                    </a>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-file-spreadsheet me-1"></i> Laporan & Ekspor
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alur Kerja --}}
    <div class="col-md-6 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Alur Kerja FDS-HPIK</h3>
            </div>
            <div class="card-body">
                <div class="steps steps-counter steps-blue">
                    <a class="step-item @if(Auth::user()->isUpt()) active @endif">
                        <div class="h5 mb-1">Perencanaan</div>
                        <p class="text-muted small">UPT membuat rencana pemantauan HPIK dan mengajukan validasi ke BBKHIT/Pusat</p>
                    </a>
                    <a class="step-item @if(Auth::user()->isUpt()) active @endif">
                        <div class="h5 mb-1">Pelaksanaan</div>
                        <p class="text-muted small">UPT input data lapangan — lokasi, jumlah sampel, dan koordinat GPS</p>
                    </a>
                    <a class="step-item @if(Auth::user()->isUpt() || Auth::user()->isBbkhit()) active @endif">
                        <div class="h5 mb-1">Laboratorium</div>
                        <p class="text-muted small">Input hasil uji laboratorium — metode, hasil (Pos/Neg), dan diagnosis</p>
                    </a>
                    <a class="step-item @if(Auth::user()->isBbkhit() || Auth::user()->isPusat()) active @endif">
                        <div class="h5 mb-1">Evaluasi</div>
                        <p class="text-muted small">BBKHIT/Pusat menetapkan kesimpulan akhir dan status warna GIS (🟢🟡🔴)</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inisialisasi peta (Fokus ke wilayah Indonesia)
    var map = L.map('map').setView([-2.5, 118], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Ambil data dari Laravel ke JavaScript
    var locations = @json($listPelaksanaan);

    locations.forEach(function(loc) {
        if(loc.latitude && loc.longitude) {
            L.marker([loc.latitude, loc.longitude])
             .addTo(map)
             .bindPopup(
                "<b>" + loc.perencanaan.jenis_mp + "</b><br>" +
                "Lokasi: " + loc.lokasi_pengambilan_sampel + "<br>" +
                "Petugas: " + loc.metode_pengambilan_sampel
             );
        }
    });
</script>