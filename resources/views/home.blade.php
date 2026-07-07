@extends('layouts.app')

@section('title', 'Dashboard   SIP-HPIK')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan Nasional Pemantauan HPIK   Deputi Karantina Ikan')

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
/*   Dashboard Specific   */
.greeting-banner {
    background: linear-gradient(135deg, #0a1628 0%, #1a2f5e 50%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 10px 32px;
    position: relative;
    overflow: hidden;
}
.greeting-banner::before,
.greeting-banner::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.greeting-banner::before { width: 220px; height: 220px; top: -80px; right: -40px; }
.greeting-banner::after  { width: 160px; height: 160px; bottom: -60px; right: 100px; }

/* KPI Cards */
.kpi-card {
    border-radius: 14px;
    border: none;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.13) !important; }
.kpi-card .kpi-icon {
    width: 54px; height: 54px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.kpi-card .kpi-number {
    font-size: 2.4rem; font-weight: 800;
    line-height: 1; letter-spacing: -1.5px;
}
@media (max-width: 767.98px) {
    .kpi-card .kpi-number { font-size: 1.8rem; }
}
.kpi-card .kpi-trend {
    font-size: 0.75rem; font-weight: 600; display: inline-flex;
    align-items: center; gap: 3px; padding: 2px 8px;
    border-radius: 20px;
}
.kpi-stripe {
    position: absolute; right: 0; top: 0; bottom: 0; width: 5px;
    border-radius: 0 14px 14px 0;
}

/* Chart Cards */
.chart-card { border-radius: 14px; border: none; }
.chart-card .card-header {
    background: transparent; border-bottom: 1px solid #f1f5f9;
    padding: 18px 20px 14px;
}
.chart-header-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}

/* Map */
#map-dashboard {
    height: 420px; border-radius: 0 0 14px 14px;
    z-index: 1;
}
.leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

/* Alur Pemantauan */
.alur-step {
    display: flex; align-items: flex-start; gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f1f5f9;
}
.alur-step:last-child { border-bottom: none; padding-bottom: 0; }
.alur-num {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.9rem; flex-shrink: 0;
    color: #fff; background: linear-gradient(135deg, #3b82f6, #6366f1);
}
.alur-connector {
    width: 2px; height: 24px; background: #e2e8f0;
    margin-left: 18px; margin-top: -4px; margin-bottom: -8px;
}

/* Regulasi */
.reg-card {
    border-radius: 14px; border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.25s; overflow: hidden; background: #fff;
}
.reg-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,0.09) !important; }
.reg-top-bar { height: 4px; }

/* Top UPT List */
.upt-rank-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid #f8fafc;
}
.upt-rank-item:last-child { border-bottom: none; }
.rank-badge {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.8rem; flex-shrink: 0;
}

/* Progress bar inside chart */
.media-bar-row { margin-bottom: 12px; }
.media-bar-row:last-child { margin-bottom: 0; }

/* Banner Enhancements */
.bg-white-transparent { background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.1); }
.btn-white-transparent { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s; }
.btn-white-transparent:hover { background: #fff; color: #1e3a8a; }

.banner-illustration-wrapper {
    position: relative;
    padding: 10px;
    z-index: 1;
}
.banner-illu-img {
    filter: drop-shadow(0 15px 25px rgba(0,0,0,0.3));
    animation: floating 6s ease-in-out infinite;
    max-height: 160px;
    border-radius: 15px;
}

/* Isometric Map Styles from Login */
.map-visual-container {
    position: relative;
    width: 100%;
    height: 120px;
    overflow: hidden;
    border-radius: 20px;
}
.map-grid-overlay {
    position: absolute; top: 0; left: 0; width: 100%; height: 200%; z-index: 1;
    background-image: linear-gradient(rgba(56, 189, 248, 0.15) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(56, 189, 248, 0.15) 1px, transparent 1px);
    background-size: 40px 40px;
    transform: perspective(800px) rotateX(60deg) translateY(-50px) scale(1.5);
    opacity: 0.4;
}
.mini-ping {
    position: absolute; width: 6px; height: 6px; background: #38bdf8;
    border-radius: 50%; box-shadow: 0 0 10px 1px rgba(56, 189, 248, 0.8);
    z-index: 2;
}
.mini-ping::after {
    content: ''; position: absolute; top: -10px; left: -10px;
    width: 26px; height: 26px; border: 1.5px solid #38bdf8; border-radius: 50%;
    opacity: 0; animation: miniRadarPing 3s infinite cubic-bezier(0.1, 0.7, 0.1, 1);
}
@keyframes miniRadarPing {
    0% { transform: scale(0.1); opacity: 1; }
    100% { transform: scale(2.5); opacity: 0; }
}
.ping-red { background: #ef4444; box-shadow: 0 0 10px 1px rgba(239, 68, 68, 0.8); }
.ping-red::after { border-color: #ef4444; }

.glow-effect {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(59,130,246,0.3) 0%, transparent 70%);
    z-index: -1;
    filter: blur(20px);
    animation: pulse-glow 4s ease-in-out infinite;
}

@keyframes floating {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(2deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}
@keyframes pulse-glow {
    0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
    50% { opacity: 1; transform: translate(-50%, -50%) scale(1.3); }
}
.wave { display: inline-block; animation: wave-animation 2.5s infinite; transform-origin: 70% 70%; }
@keyframes wave-animation {
    0% { transform: rotate( 0.0deg) }
    10% { transform: rotate(14.0deg) }
    20% { transform: rotate(-8.0deg) }
    30% { transform: rotate(14.0deg) }
    40% { transform: rotate(-4.0deg) }
    50% { transform: rotate(10.0deg) }
    60% { transform: rotate( 0.0deg) }
    100% { transform: rotate( 0.0deg) }
}
.animate-pulse { animation: shadow-pulse 2s infinite; }
@keyframes shadow-pulse {
    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}
</style>
@endsection

@section('content')

@php
    $hour = (int) date('H');
    $greeting = $hour < 10 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $roleLabel = Auth::user()->isUpt() ? 'Admin BKHIT' : (Auth::user()->isBbkhit() ? 'Admin BBKHIT' : 'Admin Pusat');
    $progressPct = $totalPerencanaan > 0 ? round($totalApproved / $totalPerencanaan * 100) : 0;
    
    // Dynamic column for KPI cards
    $kpiColClass = Auth::user()->isBkhit() ? 'col-6 col-lg-4' : 'col-6 col-lg-3';
@endphp

{{--  
     GREETING BANNER
  --}}
<div class="greeting-banner mb-4 shadow-sm">
    <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
        <div class="col-lg-9">
            <div class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                <i class="ti ti-calendar fs-3"></i>
                <span class="fw-medium">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 class="text-white fw-extrabold mb-0" style="font-size:1.75rem; letter-spacing: -0.02em;">
                Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! <span class="wave"> </span>
            </h1>
            <p class="mb-0 small" style="color:rgba(255,255,255,0.7); white-space: nowrap;">
                SIP-HPIK: Sistem Informasi Pemantauan Hama & Penyakit Ikan Karantina &nbsp; &nbsp; Login: <span class="text-white fw-bold">{{ $roleLabel }}</span>
            </p>
        </div>
        <div class="col-lg-3 d-none d-lg-block text-end position-relative">
            <div class="banner-illustration-wrapper">
                <div class="map-visual-container">
                    <div class="map-grid-overlay"></div>
                    <div class="mini-ping" style="top:30%; left:40%;"></div>
                    <div class="mini-ping delay-2" style="top:60%; left:70%;"></div>
                    <div class="mini-ping ping-red" style="top:75%; left:30%;"></div>
                    <div class="mini-ping" style="top:20%; left:80%;"></div>
                </div>
                <div class="glow-effect"></div>
            </div>
        </div>
    </div>
</div>

{{--  
     ZONE 1   KPI STATS
  --}}
<div class="row g-3 mb-4">
    {{-- Total Perencanaan --}}
    <div class="{{ $kpiColClass }}">
        <div class="card kpi-card shadow-sm p-3">
            <div class="kpi-stripe bg-primary"></div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:#eff6ff;">
                    <i class="ti ti-clipboard-list text-primary"></i>
                </div>
                <div class="text-muted small fw-semibold">Total Perencanaan</div>
            </div>
            <div class="kpi-number text-dark">{{ number_format($totalPerencanaan) }}</div>
            <div class="mt-2">
                <span class="kpi-trend" style="background:#eff6ff;color:#1d4ed8;">
                    <i class="ti ti-chart-bar"></i> {{ Auth::user()->isBkhit() ? 'UPT Anda' : 'Nasional' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Total Pelaksanaan --}}
    <div class="{{ $kpiColClass }}">
        <div class="card kpi-card shadow-sm p-3">
            <div class="kpi-stripe bg-success"></div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:#f0fdf4;">
                    <i class="ti ti-map-search text-success"></i>
                </div>
                <div class="text-muted small fw-semibold">Total Pelaksanaan</div>
            </div>
            <div class="kpi-number text-dark">{{ number_format($totalPelaksanaan) }}</div>
            <div class="mt-2">
                <span class="kpi-trend" style="background:#f0fdf4;color:#16a34a;">
                    <i class="ti ti-map-pin"></i> {{ Auth::user()->isBkhit() ? 'Giat Lapangan' : 'Nasional' }}
                </span>
            </div>
        </div>
    </div>

    {{-- UPT Aktif (Hanya untuk BBKHIT & PUSAT) --}}
    @if(!Auth::user()->isBkhit())
    <div class="col-6 col-lg-3">
        <div class="card kpi-card shadow-sm p-3">
            <div class="kpi-stripe bg-warning"></div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:#fffbeb;">
                    <i class="ti ti-building-community text-warning"></i>
                </div>
                <div class="text-muted small fw-semibold">UPT Aktif</div>
            </div>
            <div class="kpi-number text-dark">{{ number_format($totalUptAktif) }}</div>
            <div class="mt-2">
                <span class="kpi-trend" style="background:#fffbeb;color:#ca8a04;">
                    <i class="ti ti-flask"></i> UPT Melakukan Uji
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- Positif Terdeteksi --}}
    <div class="{{ $kpiColClass }}">
        <div class="card kpi-card shadow-sm p-3">
            <div class="kpi-stripe bg-danger"></div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:#fef2f2;">
                    <i class="ti ti-alert-triangle text-danger"></i>
                </div>
                <div class="text-muted small fw-semibold">Positif Terdeteksi</div>
            </div>
            <div class="kpi-number text-dark">{{ number_format($rekapHasil['positif']) }}</div>
            <div class="mt-2">
                <span class="kpi-trend" style="background:#fef2f2;color:#dc2626;">
                    <i class="ti ti-virus"></i> Temuan Penyakit
                </span>
            </div>
        </div>
    </div>
</div>

{{--  
     ZONE 2   GRAFIK UTAMA (Pelaksanaan Bulanan + Media Pembawa)
  --}}
<div class="row g-3 mb-4">
    {{-- Pelaksanaan per Bulan --}}
    <div class="col-lg-7">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#eff6ff;">
                    <i class="ti ti-chart-bar text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Pelaksanaan Pemantauan per Bulan</div>
                    <div class="text-muted small">Data bulanan tahun <b>{{ $selectedYear }}</b>   semua UPT nasional</div>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <select class="form-select form-select-sm border-0 bg-primary-lt text-primary fw-bold" 
                            style="width: 100px; cursor: pointer;" 
                            onchange="window.location.href = '{{ route('home') }}?year=' + this.value">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <div class="badge bg-primary-lt text-primary px-3 d-none d-md-flex align-items-center">Agregat</div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="min-height: 220px; height: 220px; position: relative;">
                    <canvas id="chartBulan"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 5 Media Pembawa --}}
    <div class="col-lg-5">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#f0fdf4;">
                    <i class="ti ti-fish text-success"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Media Pembawa</div>
                    <div class="text-muted small">Media Pembawa paling banyak dipantau</div>
                </div>
            </div>
            <div class="card-body p-4">
                @if(count($chartMediaLabels) > 0)
                    @php
                        $maxMedia = max($chartMediaData) ?: 1;
                        $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'];
                    @endphp
                    @foreach($chartMediaLabels as $i => $label)
                    <div class="media-bar-row">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold text-dark">
                                {{ Str::limit($label, 28) }}
                            </span>
                            <span class="small text-muted fw-bold">{{ $chartMediaData[$i] }}</span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:4px;background:#f1f5f9;">
                            <div class="progress-bar"
                                 style="width:{{ round($chartMediaData[$i] / $maxMedia * 100) }}%;
                                        background:{{ $colors[$i % count($colors)] }};border-radius:4px;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state py-4">
                        <div class="empty-state-icon"> </div>
                        <p>Belum ada data media pembawa</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{--  
     ZONE 3   Mixed: Jenis Penyakit + Status + Top UPT
  --}}
<div class="row g-3 mb-4">
    {{-- Jenis Penyakit / HPIK Chart --}}
    <div class="col-lg-5">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#fff1f2;">
                    <i class="ti ti-virus text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">HPIK Terdeteksi</div>
                    <div class="text-muted small">Jenis penyakit paling banyak ditemukan</div>
                </div>
            </div>
            <div class="card-body p-3">
                @if(count($chartHpikLabels) > 0)
                    <div style="min-height: 300px; height: 300px; position: relative;">
                        <canvas id="chartHpik"></canvas>
                    </div>
                @else
                    <div class="empty-state py-4 text-center">
                        <div class="empty-state-icon" style="font-size: 2.5rem;"> </div>
                        <p class="text-muted">Belum ada data penyakit terdeteksi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Perencanaan Donut --}}
    <div class="col-lg-3">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#f5f3ff;">
                    <i class="ti ti-chart-donut text-purple"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Status Rencana</div>
                    <div class="text-muted small">Proporsi nasional</div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="min-height: 180px; height: 180px; position: relative; margin-bottom: 1rem;">
                    <canvas id="chartStatus"></canvas>
                </div>
                <div class="w-100">
                    @foreach($statusCounts as $label => $count)
                    @php
                        $dotColor = $label === 'Draft' ? '#64748b' : ($label === 'Menunggu' ? '#94a3b8' : '#22c55e');
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="d-flex align-items-center gap-2 small">
                            <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:{{ $dotColor }};flex-shrink:0;"></span>
                            {{ $label }}
                        </span>
                        <span class="fw-bold small">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Top 5 UPT Paling Aktif --}}
    <div class="col-lg-4">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#fffbeb;">
                    <i class="ti ti-trophy text-warning"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">UPT Aktif</div>
                    <div class="text-muted small">Berdasarkan hasil uji selesai</div>
                </div>
            </div>
            <div class="card-body px-4 py-3">
                @if($topUpt->isEmpty())
                    <div class="empty-state py-4">
                        <div class="empty-state-icon"> </div>
                        <p>Belum ada data UPT</p>
                    </div>
                @else
                    @php
                        $rankColors = ['#f59e0b','#94a3b8','#cd7f32','#64748b','#64748b'];
                        $rankLabels = [' ',' ',' ','4','5'];
                    @endphp
                    @foreach($topUpt as $i => $upt)
                    <div class="upt-rank-item">
                        <div class="rank-badge" style="background:{{ $rankColors[$i] }}1a;color:{{ $rankColors[$i] }};">
                            {{ $rankLabels[$i] }}
                        </div>
                        <div class="flex-fill min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $upt->name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $upt->upt_asal ?? 'BKHIT' }}</div>
                        </div>
                        <span class="badge bg-success-lt text-success fw-bold">{{ $upt->pelaksanaan_count }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{--  
     ZONE 4   PETA PEMANTAUAN (Hanya Admin Pusat)
  --}}
@if(Auth::user()->isPusat())
<div class="card chart-card shadow-sm mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <div class="chart-header-icon" style="background:#eff6ff;">
            <i class="ti ti-map-2 text-primary"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Peta Sebaran Pemantauan HPIK</div>
            <div class="text-muted small">Titik lokasi pelaksanaan pemantauan seluruh Indonesia</div>
        </div>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <div class="form-check form-switch m-0 me-3">
                <input class="form-check-input" type="checkbox" id="toggleHeatmap">
                <label class="form-check-label small fw-bold text-danger" for="toggleHeatmap">  Heatmap Penyakit</label>
            </div>
            <span class="badge bg-blue-lt text-blue px-3">
                <i class="ti ti-map-pin me-1"></i>{{ $petaData->count() }} titik
            </span>
        </div>
    </div>
    <div id="map-dashboard"></div>
</div>
@endif

{{--  
     ZONE 5   DASAR HUKUM + ALUR PEMANTAUAN
  --}}
<div class="row g-3">
    {{-- Aktivitas Terbaru --}}
    <div class="col-lg-8">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2" style="background:transparent;">
                <div class="chart-header-icon" style="background:#ecfeff;">
                    <i class="ti ti-activity text-cyan"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Aktivitas Terbaru</div>
                    <div class="text-muted small">5 pemantauan lapangan terakhir</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                    <thead>
                        <tr>
                            <th>Tgl Pelaksanaan</th>
                            @if(!Auth::user()->isBkhit()) <th>UPT</th> @endif
                            <th>Lokasi / Media Pembawa</th>
                            <th>Status Lab</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasTerbaru as $akt)
                        <tr>
                            <td class="text-nowrap">
                                <div>{{ $akt->tanggal_pemantauan ? \Carbon\Carbon::parse($akt->tanggal_pemantauan)->format('d M Y') : ' ' }}</div>
                                <div class="text-muted small">{{ $akt->created_at->diffForHumans() }}</div>
                            </td>
                            @if(!Auth::user()->isBkhit())
                            <td>
                                <div class="fw-semibold">{{ $akt->perencanaan?->user?->name ?? ' ' }}</div>
                            </td>
                            @endif
                            <td>
                                <div class="text-dark">{{ Str::limit($akt->lokasi_pengambilan_sampel, 30) }}</div>
                                <div class="text-muted small">{{ $akt->jenis_ikan }}</div>
                            </td>
                            <td>
                                @if($akt->laboratorium)
                                    <span class="badge bg-success-lt text-success">{{ $akt->laboratorium->hasil_uji }}</span>
                                @else
                                    <span class="badge bg-warning-lt text-warning"><i class="ti ti-clock me-1"></i>Belum Uji</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas pelaksanaan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Rekap Hasil Uji Lab --}}
    <div class="col-lg-4">
        <div class="card chart-card shadow-sm h-100">
             <div class="card-header d-flex align-items-center gap-2" style="background:transparent;">
                <div class="chart-header-icon" style="background:#f1f5f9;">
                    <i class="ti ti-flask text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Rekap Hasil Uji Lab</div>
                    <div class="text-muted small">Cakupan: <b>{{ Auth::user()->isBkhit() ? 'Unit Pelaksana' : (Auth::user()->isBbkhit() ? 'Regional' : 'Nasional Agregat') }}</b></div>
                </div>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center gap-3">
                {{-- Positif --}}
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:#fef2f2; border:1px solid #fee2e2;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;background:#ef4444;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;">
                            <i class="ti ti-alert-octagon fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-danger" style="font-size:1.1rem;">{{ number_format($rekapHasil['positif']) }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">Positif Penyakit</div>
                        </div>
                    </div>
                    <span class="badge bg-danger-lt px-2">High Risk</span>
                </div>
                
                {{-- Negatif --}}
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:#f0fdf4; border:1px solid #dcfce7;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;background:#22c55e;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;">
                            <i class="ti ti-shield-check fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-success" style="font-size:1.1rem;">{{ number_format($rekapHasil['negatif']) }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">Nihil (Negatif)</div>
                        </div>
                    </div>
                    <span class="badge bg-success-lt px-2">Safe</span>
                </div>
                
                <div class="mt-2 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small fw-bold">TOTAL SAMPEL DIUJI</div>
                    <div class="h3 mb-0 fw-extrabold text-primary">{{ number_format($rekapHasil['total']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
//  
// Chart.js Global Styling
//  
Chart.defaults.font.family = "'Inter', 'Geist', system-ui, sans-serif";
Chart.defaults.color = '#64748b';

//   1. Grafik Pelaksanaan per Bulan (Bar)  
const ctxBulan = document.getElementById('chartBulan');
if (ctxBulan) {
    new Chart(ctxBulan, {
        type: 'bar',
        data: {
            labels: @json($chartBulanLabels),
            datasets: [{
                label: 'Pelaksanaan',
                data: @json($chartBulanData),
                backgroundColor: 'rgba(59,130,246,0.8)',
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#3b82f6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f8fafc',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} pelaksanaan`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 },
                        callback: v => Number.isInteger(v) ? v : null
                    }
                }
            }
        }
    });
}

//   2. Jenis HPIK (Polar Area / Donut)  
const ctxHpik = document.getElementById('chartHpik');
if (ctxHpik) {
    const hpikColors = [
        '#ef4444','#f97316','#f59e0b','#84cc16',
        '#10b981','#3b82f6','#8b5cf6','#ec4899'
    ];
    new Chart(ctxHpik, {
        type: 'bar',
        data: {
            labels: @json($chartHpikLabels),
            datasets: [{
                label: 'Jumlah Terdeteksi',
                data: @json($chartHpikData),
                backgroundColor: hpikColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f8fafc',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    cornerRadius: 10,
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { stepSize: 1, font: { size: 10 } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' } }
                }
            }
        }
    });
}

//   3. Status Perencanaan (Donut kecil)  
const ctxStatus = document.getElementById('chartStatus');
if (ctxStatus) {
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($statusCounts)),
            datasets: [{
                data: @json(array_values($statusCounts)),
                backgroundColor: ['#64748b','#94a3b8','#22c55e'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 10,
                }
            }
        }
    });
}

//  
// PETA LEAFLET
//  
const mapEl = document.getElementById('map-dashboard');
if (mapEl) {
    const petaData = @json($petaData);
    const dominantProvinsi = @json($dominantProvinsi);
    const heatmapPoints = @json($heatmapData);

    const map = L.map('map-dashboard', {
        center: [-2.5, 118],
        zoom: 5,
        zoomControl: true,
        maxBounds: [[-15, 90], [15, 150]],
        minZoom: 4
    });

    const baseLight = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO', subdomains: 'abcd', maxZoom: 19
    });
    const baseLabels = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO', subdomains: 'abcd', maxZoom: 19, pane: 'shadowPane'
    });
    baseLight.addTo(map);
    baseLabels.addTo(map);

    // 1. Heatmap Layer
    const heatLayer = L.heatLayer(heatmapPoints, {
        radius: 25, blur: 15, maxZoom: 10,
        gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'}
    });

    // 2. GeoJSON Provinsi dengan Tooltip Dominan
    fetch('https://raw.githubusercontent.com/anshori/indonesia-geojson/master/indonesia.geojson')
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, {
                style: function(feature) {
                    let provName = (feature.properties.PROVINSI || '').toUpperCase().trim();
                    let color = 'transparent';
                    let fillOp = 0;
                    for (let key in dominantProvinsi) {
                        let bkKey = key.toUpperCase().trim();
                        if (provName === bkKey || provName.includes(bkKey) || bkKey.includes(provName)) {
                            color = dominantProvinsi[key].color;
                            fillOp = 0.5;
                            break;
                        }
                    }
                    return { fillColor: color, weight: 1, opacity: 1, color: fillOp > 0 ? '#ffffff' : '#cbd5e1', fillOpacity: fillOp };
                },
                onEachFeature: function(feature, layer) {
                    let provName = (feature.properties.PROVINSI || '').toUpperCase().trim();
                    let info = '';
                    for (let key in dominantProvinsi) {
                        let bkKey = key.toUpperCase().trim();
                        if (provName === bkKey || provName.includes(bkKey) || bkKey.includes(provName)) {
                            let d = dominantProvinsi[key];
                            if (d.status === 'nihil') {
                                info = `<br><span style="font-size:0.8rem;color:#64748b;">Status:</span> <b style="color:#22c55e;">Nihil / Aman</b> (${d.count} uji nihil)`;
                            } else {
                                info = `<br><span style="font-size:0.8rem;color:#64748b;">Dominan:</span> <b style="color:${d.color}">${d.dominant}</b> (${d.count} Positif)`;
                            }
                            break;
                        }
                    }
                    if (info !== '') {
                        layer.bindTooltip('<b>' + feature.properties.PROVINSI + '</b>' + info, { sticky: true });
                        layer.on({
                            mouseover: function(e) { e.target.setStyle({ fillOpacity: 0.8, weight: 2 }); },
                            mouseout: function(e)  { e.target.setStyle({ fillOpacity: 0.5, weight: 1 }); }
                        });
                    }
                }
            }).addTo(map);
        });

    // 3. Titik Lokasi (Markers)
    const markerGroup = L.layerGroup();
    if (petaData.length === 0) {
        const info = L.control({position: 'topright'});
        info.onAdd = () => {
            const div = L.DomUtil.create('div');
            div.innerHTML = `<div style="background:white;padding:8px 12px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);font-size:12px;color:#64748b;">Belum ada titik lokasi pengujian.</div>`;
            return div;
        };
        info.addTo(map);
    } else {
        petaData.forEach(p => {
            let hex = '#3b82f6';
            if (p.hasil_lab === 'Positif') hex = '#ef4444';
            else if (p.hasil_lab === 'Negatif') hex = '#22c55e';
            else if (p.hasil_lab === 'Inkonklusif') hex = '#f59e0b';
            else if (p.hasil_lab === 'Belum Diuji') hex = '#94a3b8';

            const markerIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;"><div style="width:6px;height:6px;border-radius:50%;background:${hex};box-shadow:0 0 4px ${hex};transition:opacity 0.3s;"></div></div>`,
                iconSize: [16, 16], iconAnchor: [8, 8]
            });

            L.marker([p.lat, p.lng], { icon: markerIcon })
                .bindPopup(`
                    <div style="min-width:220px;font-family:'Inter',sans-serif;">
                        <div style="background:${hex};color:white;padding:8px 12px;margin:-1px -1px 0 -1px;border-radius:6px 6px 0 0;font-weight:600;text-align:center;">
                            ${p.jenis_hpik}
                        </div>
                        <div style="padding:10px 12px;font-size:13px;line-height:1.7;">
                            <div><b>  Lokasi:</b> ${p.lokasi || '-'}</div>
                            <div><b>Wilayah:</b> ${p.kab_kota}, ${p.provinsi}</div>
                            <div><b>  UPT:</b> ${p.upt}</div>
                            <div><b>  Tanggal:</b> ${p.tanggal}</div>
                            <div><b>  Media:</b> ${p.komoditas}</div>
                            <div><b>  Hasil:</b> <span class="badge" style="background:${hex}; color:white; font-size:11px;">${p.hasil_raw}</span></div>
                            <a href="/pelaksanaan/${p.id}/detail" class="btn btn-primary btn-sm w-100 mt-2" style="font-size:11px; font-weight:600; color:white;">
                                <i class="ti ti-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                `, { maxWidth: 300 }).addTo(markerGroup);
        });
        markerGroup.addTo(map);
    }

    // 4. Toggle Heatmap Listener
    const toggleHeatmap = document.getElementById('toggleHeatmap');
    if (toggleHeatmap) {
        toggleHeatmap.addEventListener('change', function() {
            if (this.checked) {
                heatLayer.addTo(map);
                document.querySelectorAll('.custom-marker div').forEach(el => el.style.opacity = '0.4');
            } else {
                map.removeLayer(heatLayer);
                document.querySelectorAll('.custom-marker div').forEach(el => el.style.opacity = '1');
            }
        });
    }
}
</script>
@endsection
