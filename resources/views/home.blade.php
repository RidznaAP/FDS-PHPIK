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
    
    /* Regulation Cards Styling */
    .reg-card {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        background: #fff;
        height: 100%;
    }
    .reg-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.08) !important;
    }
    .reg-ribbon {
        position: absolute;
        top: 0;
        left: 0;
        width: 40px;
        height: 40px;
        clip-path: polygon(0 0, 100% 0, 0 100%);
    }
    .reg-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }
    .reg-number {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .reg-desc {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
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

<div class="row row-deck row-cards mb-4 animate-fade-in">
    {{-- Perencanaan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card card-premium shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px; font-size: 10px;">Perencanaan</div>
                        <div class="stat-number mt-1 text-primary">{{ $totalPerencanaan }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(32, 107, 196, 0.1); border: 1px solid rgba(32, 107, 196, 0.1);">
                        <i class="ti ti-clipboard-list text-primary"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted small">{{ $approved }} Disetujui</div>
                    <div class="text-muted small fw-bold">{{ $progressPct }}%</div>
                </div>
                <div class="progress" style="height:5px; border-radius:10px; background: rgba(32, 107, 196, 0.1);">
                    <div class="progress-bar bg-primary" style="width:{{ $progressPct }}%; border-radius:10px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pelaksanaan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card card-premium shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px; font-size: 10px;">Pelaksanaan</div>
                        <div class="stat-number mt-1 text-success">{{ $totalPelaksanaan }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(47, 179, 68, 0.1); border: 1px solid rgba(47, 179, 68, 0.1);">
                        <i class="ti ti-map-pin text-success"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted small px-1">{{ $labDone }} Lab Selesai</div>
                    <div class="text-muted small fw-bold">{{ $labProgress }}%</div>
                </div>
                <div class="progress" style="height:5px; border-radius:10px; background: rgba(47, 179, 68, 0.1);">
                    <div class="progress-bar bg-success" style="width:{{ $labProgress }}%; border-radius:10px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Titik GIS --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card card-premium shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px; font-size: 10px;">Titik Peta</div>
                        <div class="stat-number mt-1 text-azure">{{ $totalGis }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(66, 153, 225, 0.1); border: 1px solid rgba(66, 153, 225, 0.1);">
                        <i class="ti ti-world text-azure"></i>
                    </div>
                </div>
                <div class="text-muted small mb-2">Lokasi Terpetakan</div>
                <div class="progress" style="height:5px; border-radius:10px; background: rgba(66, 153, 225, 0.1);">
                    <div class="progress-bar bg-azure" style="width:{{ $totalPelaksanaan > 0 ? round($totalGis/$totalPelaksanaan*100) : 0 }}%; border-radius:10px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Section --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card card-premium shadow-sm {{ $waiting > 0 ? 'border-warning' : '' }}">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px; font-size: 10px;">Approval</div>
                        <div class="stat-number mt-1 {{ $waiting > 0 ? 'text-warning' : 'text-muted' }}">{{ $waiting }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.1);">
                        <i class="ti {{ $waiting > 0 ? 'ti-bell-ringing' : 'ti-bell-check' }} text-warning"></i>
                    </div>
                </div>
                @if($waiting > 0)
                    <a href="{{ route('perencanaan.index') }}?status=waiting" class="btn btn-warning btn-sm w-100 fw-bold shadow-sm">
                        <i class="ti ti-eye me-1"></i>PROSES DATA
                    </a>
                @else
                    <div class="text-muted small">Semua data valid</div>
                    <div class="progress mt-2" style="height:5px; border-radius:10px; background: rgba(43, 172, 83, 0.1);">
                        <div class="progress-bar bg-success" style="width:100%; border-radius:10px;"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Peta + Sidebar --}}
<div class="row row-cards mb-4 animate-fade-in" style="animation-delay: 0.1s;">
    {{-- Peta --}}
    <div class="col-lg-8">
        <div class="card card-premium shadow-sm overflow-hidden" style="border-radius:16px;">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <div>
                    <h3 class="card-title mb-0 fw-bold text-primary"><i class="ti ti-map-pms me-2"></i>Visualisasi Sebaran HPIK</h3>
                    <div class="text-muted small mt-1">{{ $totalGis }} titik aktif terpantau</div>
                </div>
                <a href="{{ route('peta.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="ti ti-maximize me-1"></i>Peta Penuh
                </a>
            </div>
            <div id="map"></div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card card-premium shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header py-3">
                <h3 class="card-title mb-0 fw-bold text-indigo"><i class="ti ti-layout-grid me-2"></i>Akses Pintas</h3>
            </div>
            <div class="card-body">
                <div class="vstack gap-3">
                    @if(Auth::user()->isUpt())
                        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary d-flex align-items-center justify-content-between py-2 shadow-sm">
                            <span><i class="ti ti-plus me-2"></i>Input Rencana Baru</span>
                            <i class="ti ti-chevron-right op-5"></i>
                        </a>
                    @endif
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-ghost-primary d-flex align-items-center justify-content-between py-2">
                        <span><i class="ti ti-list-check me-2"></i>Daftar Perencanaan</span>
                        <i class="ti ti-chevron-right op-5"></i>
                    </a>
                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-ghost-success d-flex align-items-center justify-content-between py-2">
                        <span><i class="ti ti-map-pins me-2"></i>Data Pelaksanaan</span>
                        <i class="ti ti-chevron-right op-5"></i>
                    </a>
                    <a href="{{ route('laboratorium.index') }}" class="btn btn-ghost-info d-flex align-items-center justify-content-between py-2">
                        <span><i class="ti ti-flask-2 me-2"></i>Cek Laboratorium</span>
                        <i class="ti ti-chevron-right op-5"></i>
                    </a>
                    @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                        <a href="{{ route('evaluasi.index') }}" class="btn btn-ghost-warning d-flex align-items-center justify-content-between py-2">
                            <span><i class="ti ti-chart-dots me-2"></i>Mulai Evaluasi</span>
                            <i class="ti ti-chevron-right op-5"></i>
                        </a>
                    @endif
                    <div class="hr-text my-1 text-muted">Pelaporan</div>
                    <a href="{{ route('laporan.index') }}" class="btn btn-ghost-secondary d-flex align-items-center justify-content-between py-2">
                        <span><i class="ti ti-report-analytics me-2"></i>Laporan & Ekspor</span>
                        <i class="ti ti-chevron-right op-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Analytics Row --}}
<div class="row row-cards mb-4 animate-fade-in" style="animation-delay: 0.15s;">
    <div class="col-lg-8">
        <div class="card card-premium shadow-sm" style="border-radius:16px;">
            <div class="card-header py-3">
                <h3 class="card-title mb-0 fw-bold text-azure"><i class="ti ti-chart-area-line me-2"></i>Tren Pemantauan Bulanan</h3>
            </div>
            <div class="card-body">
                <div id="chart-timeline" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-premium shadow-sm" style="border-radius:16px;">
            <div class="card-header py-3">
                <h3 class="card-title mb-0 fw-bold text-purple"><i class="ti ti-chart-pie me-2"></i>Status Perencanaan</h3>
            </div>
            <div class="card-body">
                <div id="chart-status" style="min-height: 300px;"></div>
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

{{-- Dasar Hukum Section --}}
<div class="mt-5 mb-5 animate-fade-in" style="animation-delay: 0.2s;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="ti ti-gavel me-2 text-primary"></i>Dasar Hukum</h2>
            <p class="text-muted small mb-0">Landasan regulasi pemantauan Hama dan Penyakit Ikan Karantina</p>
        </div>
    </div>

    <div class="row row-cards">
        @php
            $regulations = [
                [
                    'number' => 'PP Nomor 29 Tahun 2023',
                    'desc' => 'Peraturan Pelaksanaan Undang-Undang Nomor 21 Tahun 2019 tentang Karantina Hewan, Ikan dan Tumbuhan.',
                    'color' => '#3b82f6',
                    'icon' => 'ti-scale-outline'
                ],
                [
                    'number' => 'Perpres Nomor 45 Tahun 2023',
                    'desc' => 'Tentang Badan Karantina Indonesia (Lembaran Negara Republik Indonesia Tahun 2023 Nomor 97).',
                    'color' => '#64748b',
                    'icon' => 'ti-building-fortress'
                ],
                [
                    'number' => 'Peraturan Barantin No. 1 Tahun 2023',
                    'desc' => 'Tentang Organisasi dan Tata Kerja Badan Karantina Indonesia (SOTTK).',
                    'color' => '#10b981',
                    'icon' => 'ti-hierarchy-2'
                ],
                [
                    'number' => 'Peraturan Barantin No. 12 Tahun 2024',
                    'desc' => 'Tentang Tata Cara Pemantauan HPHK, HPIK, serta Organisme Pengganggu Tumbuhan Karantina.',
                    'color' => '#f59e0b',
                    'icon' => 'ti-search'
                ],
                [
                    'number' => 'Permen KP Nomor 7 Tahun 2024',
                    'desc' => 'Tentang Penyelenggaraan Karantina Ikan, Mutu dan Keamanan Hasil Perikanan dalam Tata Cara Permohonan dan Penerbitan Sertifikat.',
                    'color' => '#f97316',
                    'icon' => 'ti-file-certificate'
                ],
            ];
        @endphp

        @foreach($regulations as $reg)
            <div class="col-md-6 col-lg-4">
                <div class="card reg-card shadow-sm border-0">
                    <div class="reg-ribbon" style="background: {{ $reg['color'] }}"></div>
                    <div class="card-body p-4">
                        <div class="reg-icon" style="background: {{ $reg['color'] }}15; color: {{ $reg['color'] }}">
                            <i class="ti {{ $reg['icon'] }}"></i>
                        </div>
                        <span class="reg-number">{{ $reg['number'] }}</span>
                        <p class="reg-desc mb-0 small">{{ $reg['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 📊 1. Chart Timeline (Trend Bulanan)
    var optionsTimeline = {
        series: [{
            name: 'Jumlah Perencanaan',
            data: @json($chartMonthlyData)
        }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#206bc4'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            }
        },
        xaxis: {
            categories: @json($chartMonthlyLabels),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { show: false },
        tooltip: { theme: 'dark' },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        }
    };
    new ApexCharts(document.querySelector("#chart-timeline"), optionsTimeline).render();

    // 📊 2. Chart Status (Donut)
    var optionsStatus = {
        series: @json(array_values($statusCounts)),
        chart: {
            height: 320,
            type: 'donut',
        },
        labels: @json(array_keys($statusCounts)),
        colors: ['#64748b', '#f59e0b', '#2fb344'],
        legend: { position: 'bottom' },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark' }
    };
    new ApexCharts(document.querySelector("#chart-status"), optionsStatus).render();

    // 🗺️ Leaflet Map
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