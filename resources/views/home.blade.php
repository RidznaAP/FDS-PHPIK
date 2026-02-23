@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Sistem Pemantauan HPIK — Deputi Karantina Ikan')

@section('page_actions')
    <span class="badge fs-6 px-3 py-2
        @if(Auth::user()->isUpt()) bg-success
        @elseif(Auth::user()->isBbkhit()) bg-warning text-dark
        @else bg-purple text-white @endif">
        {{ strtoupper(Auth::user()->role) }}
    </span>
@endsection

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 420px; border-radius: 0 0 8px 8px; }

    /* Animated stat cards */
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
    }
    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }
    .stat-card .stat-number {
        font-size: 2.4rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -1px;
    }
    .greeting-banner {
        background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 50%, #0d6efd 100%);
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .greeting-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .greeting-banner::before {
        content: '';
        position: absolute;
        right: 60px;
        bottom: -50px;
        width: 140px;
        height: 140px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .quick-action-btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.18s ease;
    }
    .quick-action-btn:hover {
        transform: translateX(3px);
    }
    .step-modern {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .step-modern:last-child { border-bottom: none; }
    .step-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')

{{-- Greeting Banner --}}
@php
    $hour = (int) date('H');
    if ($hour < 10) $greeting = 'Selamat Pagi';
    elseif ($hour < 15) $greeting = 'Selamat Siang';
    elseif ($hour < 18) $greeting = 'Selamat Sore';
    else $greeting = 'Selamat Malam';
@endphp
<div class="greeting-banner mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col">
            <div class="text-white-50 small mb-1">{{ date('l, d F Y') }}</div>
            <h2 class="text-white fw-bold mb-1">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-white-50 mb-0 small">
                Anda login sebagai
                <span class="fw-semibold text-white">
                    @if(Auth::user()->isBkhit()) Admin BKHIT @elseif(Auth::user()->isBbkhit()) Admin BBKHIT @else Admin Pusat @endif
                </span>
            </p>
        </div>
        <div class="col-auto d-none d-md-block">
            <span style="font-size: 4rem; opacity: 0.25;">🐟</span>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
@php
    $isBkhitDash = Auth::user()->isBkhit();
    $uid = Auth::id();

    $waiting = \App\Models\Perencanaan::where('status', 'waiting')
        ->when($isBkhitDash, fn($q) => $q->where('user_id', $uid))
        ->count();
    $approved = \App\Models\Perencanaan::where('status', 'approved')
        ->when($isBkhitDash, fn($q) => $q->where('user_id', $uid))
        ->count();
    $progressPct = $totalPerencanaan > 0 ? round($approved / $totalPerencanaan * 100) : 0;

    $labDone = \App\Models\Laboratorium::when($isBkhitDash, fn($q) =>
        $q->whereHas('pelaksanaan', fn($rq) =>
            $rq->whereHas('perencanaan', fn($rrq) => $rrq->where('user_id', $uid))
        )
    )->count();
    $labProgress = $totalPelaksanaan > 0 ? round($labDone / $totalPelaksanaan * 100) : 0;
@endphp

<div class="row row-deck row-cards mb-4">
    {{-- Perencanaan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;">Perencanaan</div>
                        <div class="stat-number mt-1">{{ $totalPerencanaan }}</div>
                    </div>
                    <div class="stat-icon" style="background:#dbeafe;">
                        <i class="ti ti-clipboard-list" style="color:#2563eb;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small">{{ $approved }} disetujui</div>
                    <div class="text-muted small fw-semibold">{{ $progressPct }}%</div>
                </div>
                <div class="progress" style="height:6px;border-radius:4px;">
                    <div class="progress-bar" style="width:{{ $progressPct }}%;background:#2563eb;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pelaksanaan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;">Pelaksanaan</div>
                        <div class="stat-number mt-1">{{ $totalPelaksanaan }}</div>
                    </div>
                    <div class="stat-icon" style="background:#dcfce7;">
                        <i class="ti ti-map-pin" style="color:#16a34a;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small">{{ $labDone }} sudah diuji lab</div>
                    <div class="text-muted small fw-semibold">{{ $labProgress }}%</div>
                </div>
                <div class="progress" style="height:6px;border-radius:4px;">
                    <div class="progress-bar" style="width:{{ $labProgress }}%;background:#16a34a;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Titik GIS --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;">Titik GIS</div>
                        <div class="stat-number mt-1">{{ $totalGis }}</div>
                    </div>
                    <div class="stat-icon" style="background:#cffafe;">
                        <i class="ti ti-world" style="color:#0891b2;"></i>
                    </div>
                </div>
                <div class="text-muted small mb-1">Lokasi GPS terpetakan</div>
                <div class="progress" style="height:6px;border-radius:4px;">
                    <div class="progress-bar" style="width:{{ $totalPelaksanaan > 0 ? round($totalGis/$totalPelaksanaan*100) : 0 }}%;background:#0891b2;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menunggu Validasi --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm {{ $waiting > 0 ? 'border border-warning' : '' }}">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;">Menunggu Approval</div>
                        <div class="stat-number mt-1 {{ $waiting > 0 ? 'text-warning' : '' }}">{{ $waiting }}</div>
                    </div>
                    <div class="stat-icon" style="background:#fef9c3;">
                        @if($waiting > 0)
                            <i class="ti ti-bell-ringing" style="color:#ca8a04; animation: ring 1.5s ease infinite;"></i>
                        @else
                            <i class="ti ti-bell-check" style="color:#ca8a04;"></i>
                        @endif
                    </div>
                </div>
                @if($waiting > 0)
                    <a href="{{ route('perencanaan.index') }}?status=waiting" class="btn btn-sm btn-warning w-100">
                        <i class="ti ti-eye me-1"></i>Lihat Permintaan
                    </a>
                @else
                    <div class="text-muted small">Tidak ada yang perlu disetujui</div>
                    <div class="progress mt-1" style="height:6px;border-radius:4px;">
                        <div class="progress-bar bg-success" style="width:100%;border-radius:4px;"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Peta + Sidebar --}}
<div class="row row-cards mb-4">
    {{-- Peta --}}
    <div class="col-lg-8">
        <div class="card shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="card-header d-flex align-items-center justify-content-between" style="background:#f8fafc;">
                <div>
                    <h3 class="card-title mb-0"><i class="ti ti-map me-2 text-primary"></i>Peta Sebaran HPIK</h3>
                    <div class="text-muted small">{{ $totalGis }} titik lokasi terpetakan</div>
                </div>
                <a href="{{ route('peta.index') }}" class="btn btn-sm btn-primary">
                    <i class="ti ti-external-link me-1"></i>Peta Penuh
                </a>
            </div>
            <div id="map"></div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header" style="background:#f8fafc;">
                <h3 class="card-title mb-0"><i class="ti ti-bolt me-2 text-warning"></i>Menu Cepat</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(Auth::user()->isUpt())
                        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary quick-action-btn">
                            <i class="ti ti-plus me-2"></i>Buat Perencanaan Baru
                        </a>
                    @endif
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-primary quick-action-btn">
                        <i class="ti ti-clipboard-list me-2"></i>Daftar Perencanaan
                    </a>
                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-success quick-action-btn">
                        <i class="ti ti-map-pin me-2"></i>Daftar Pelaksanaan
                    </a>
                    <a href="{{ route('laboratorium.index') }}" class="btn btn-outline-info quick-action-btn">
                        <i class="ti ti-flask me-2"></i>Laboratorium
                    </a>
                    @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                        <a href="{{ route('evaluasi.index') }}" class="btn btn-outline-warning quick-action-btn">
                            <i class="ti ti-chart-bar me-2"></i>Evaluasi
                        </a>
                    @endif
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary quick-action-btn">
                        <i class="ti ti-file-spreadsheet me-2"></i>Laporan & Ekspor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alur Kerja --}}
<div class="card shadow-sm" style="border-radius:12px;">
    <div class="card-header" style="background:#f8fafc;">
        <h3 class="card-title mb-0"><i class="ti ti-route me-2 text-teal"></i>Alur Kerja SIP-HPIK</h3>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
                $steps = [
                    ['num'=>1,'icon'=>'ti-clipboard-list','color'=>'#2563eb','bg'=>'#dbeafe','title'=>'Perencanaan','desc'=>'BKHIT membuat rencana pemantauan HPIK dan mengajukan untuk validasi.','roles'=>['bkhit']],
                    ['num'=>2,'icon'=>'ti-map-pin','color'=>'#16a34a','bg'=>'#dcfce7','title'=>'Pelaksanaan Lapangan','desc'=>'BKHIT input data lapangan — lokasi, jumlah sampel, dan koordinat GPS.','roles'=>['bkhit']],
                    ['num'=>3,'icon'=>'ti-flask','color'=>'#0891b2','bg'=>'#cffafe','title'=>'Uji Laboratorium','desc'=>'Input hasil uji laboratorium — metode, hasil (Positif/Negatif), diagnosis.','roles'=>['bkhit','bbkhit']],
                    ['num'=>4,'icon'=>'ti-check','color'=>'#7c3aed','bg'=>'#ede9fe','title'=>'Evaluasi Akhir','desc'=>'BBKHIT/Pusat menetapkan kesimpulan dan status warna GIS (🟢🟡🔴).','roles'=>['bbkhit','pusat']],
                ];
                $myRole = Auth::user()->role;
            @endphp
            @foreach($steps as $step)
            <div class="col-sm-6 col-lg-3">
                <div class="p-3 rounded-3 h-100" style="background:{{ in_array($myRole, $step['roles']) ? $step['bg'] : '#f8fafc' }}; border: 1px solid {{ in_array($myRole,$step['roles']) ? $step['color'].'33' : '#e2e8f0' }};">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="step-circle" style="background:{{ $step['bg'] }};color:{{ $step['color'] }};border:2px solid {{ $step['color'] }}20;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">{{ $step['num'] }}</div>
                        <div class="fw-bold" style="color:{{ $step['color'] }};font-size:.9rem;">{{ $step['title'] }}</div>
                    </div>
                    <p class="text-muted small mb-0">{{ $step['desc'] }}</p>
                    @if(in_array($myRole, $step['roles']))
                        <div class="mt-2"><span class="badge" style="background:{{ $step['color'] }}22;color:{{ $step['color'] }};font-size:.7rem;">Peran Anda</span></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inisialisasi peta dengan CartoDB Voyager (lebih modern dari OSM)
    var map = L.map('map', { zoomControl: true, scrollWheelZoom: false }).setView([-2.5, 118], 5);

    // CartoDB Voyager — tile modern, bersih, HD
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Custom marker icon
    function makeIcon(color) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width: 14px; height: 14px;
                background: ${color};
                border: 3px solid white;
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(0,0,0,0.35);
            "></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
            popupAnchor: [0, -10]
        });
    }

    var locations = @json($listPelaksanaan);

    locations.forEach(function(loc) {
        if (loc.latitude && loc.longitude) {
            L.marker([loc.latitude, loc.longitude], { icon: makeIcon('#3b82f6') })
             .addTo(map)
             .bindPopup(
                `<div style="min-width:180px;font-family:'Inter',sans-serif;">
                    <div style="font-weight:600;margin-bottom:4px;">${loc.perencanaan ? loc.perencanaan.jenis_mp : '-'}</div>
                    <div style="color:#666;font-size:12px;"><b>Lokasi:</b> ${loc.lokasi_pengambilan_sampel}</div>
                    <div style="color:#666;font-size:12px;"><b>Metode:</b> ${loc.metode_pengambilan_sampel ?? '-'}</div>
                </div>`
             );
        }
    });

    // Fit bounds jika ada marker
    if (locations.length > 0) {
        var validLocs = locations.filter(l => l.latitude && l.longitude);
        if (validLocs.length > 0) {
            var group = L.featureGroup(validLocs.map(l => L.marker([l.latitude, l.longitude])));
            map.fitBounds(group.getBounds().pad(0.3));
        }
    }
</script>

<style>
@keyframes ring {
    0%,100% { transform: rotate(0deg); }
    10%,30% { transform: rotate(-15deg); }
    20%,40% { transform: rotate(15deg); }
    50% { transform: rotate(0deg); }
}
</style>
@endsection