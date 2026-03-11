@extends('layouts.app')

@section('title', 'Dashboard — SIP-HPIK')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan Nasional Pemantauan HPIK — Deputi Karantina Ikan')

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
/* ── Dashboard Specific ──────────────────────────────── */
.greeting-banner {
    background: linear-gradient(135deg, #0a1628 0%, #1a2f5e 50%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 28px 32px;
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
</style>
@endsection

@section('content')

@php
    $hour = (int) date('H');
    $greeting = $hour < 10 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $roleLabel = Auth::user()->isUpt() ? 'Admin BKHIT' : (Auth::user()->isBbkhit() ? 'Admin BBKHIT' : 'Admin Pusat');
    $progressPct = $totalPerencanaan > 0 ? round($totalApproved / $totalPerencanaan * 100) : 0;
@endphp

{{-- ═══════════════════════════════════════════════════════════
     GREETING BANNER
═══════════════════════════════════════════════════════════ --}}
<div class="greeting-banner mb-4 shadow-sm">
    <div class="row align-items-center g-3">
        <div class="col">
            <div class="text-white-50 small mb-1">
                <i class="ti ti-calendar me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
            </div>
            <h2 class="text-white fw-bold mb-1" style="font-size:1.6rem;">
                {{ $greeting }}, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="mb-0 small" style="color:rgba(255,255,255,0.6);">
                Login sebagai <span class="fw-semibold text-white">{{ $roleLabel }}</span>
                &nbsp;·&nbsp; Data Nasional Agregat
                @if($unreadNotif > 0)
                &nbsp;·&nbsp;
                <a href="{{ route('notifikasi.index') }}" class="text-warning fw-semibold text-decoration-none">
                    🔔 {{ $unreadNotif }} notifikasi baru
                </a>
                @endif
            </p>
        </div>
        <div class="col-auto d-none d-lg-flex flex-column align-items-end gap-2">
            <div class="text-white-50 small">Tingkat Persetujuan</div>
            <div class="text-white fw-bold" style="font-size:2rem;">{{ $progressPct }}%</div>
            <div class="progress" style="width:120px; height:6px; background:rgba(255,255,255,0.15);">
                <div class="progress-bar bg-success" style="width:{{ $progressPct }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ZONE 1 — KPI STATS
═══════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Total Perencanaan --}}
    <div class="col-6 col-lg-3">
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
                    <i class="ti ti-chart-bar"></i> Nasional
                </span>
            </div>
        </div>
    </div>

    {{-- Total Pelaksanaan --}}
    <div class="col-6 col-lg-3">
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
                    <i class="ti ti-map-pin"></i> Pemantauan
                </span>
            </div>
        </div>
    </div>

    {{-- UPT Aktif --}}
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
                    <i class="ti ti-star"></i> Disetujui
                </span>
            </div>
        </div>
    </div>

    {{-- Perencanaan Disetujui --}}
    <div class="col-6 col-lg-3">
        <div class="card kpi-card shadow-sm p-3">
            <div class="kpi-stripe bg-purple"></div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:#f5f3ff;">
                    <i class="ti ti-checkbox text-purple"></i>
                </div>
                <div class="text-muted small fw-semibold">Rencana Disetujui</div>
            </div>
            <div class="kpi-number text-dark">{{ number_format($totalApproved) }}</div>
            <div class="mt-2">
                <span class="kpi-trend" style="background:#f5f3ff;color:#7c3aed;">
                    <i class="ti ti-check"></i> Approved
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ZONE 2 — GRAFIK UTAMA (Pelaksanaan Bulanan + Media Pembawa)
═══════════════════════════════════════════════════════════ --}}
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
                    <div class="text-muted small">12 bulan terakhir — semua UPT nasional</div>
                </div>
                <div class="ms-auto badge bg-primary-lt text-primary px-3">Agregat</div>
            </div>
            <div class="card-body p-3">
                <canvas id="chartBulan" height="220"></canvas>
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
                    <div class="fw-bold text-dark">Top 5 Media Pembawa</div>
                    <div class="text-muted small">Komoditas paling banyak dipantau</div>
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
                        <div class="empty-state-icon">🐟</div>
                        <p>Belum ada data media pembawa</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ZONE 3 — Mixed: Jenis Penyakit + Status + Top UPT
═══════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Jenis Penyakit / HPIK Chart --}}
    <div class="col-lg-5">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#fff1f2;">
                    <i class="ti ti-virus text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Distribusi Jenis HPIK</div>
                    <div class="text-muted small">Penyakit paling sering dipantau</div>
                </div>
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center">
                @if(count($chartHpikLabels) > 0)
                    <canvas id="chartHpik" style="max-height:260px;"></canvas>
                @else
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">🦠</div>
                        <p>Belum ada data jenis HPIK</p>
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
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center gap-3">
                <canvas id="chartStatus" style="max-height:180px;"></canvas>
                <div class="w-100">
                    @foreach($statusCounts as $label => $count)
                    @php
                        $dotColor = $label === 'Draft' ? '#64748b' : ($label === 'Menunggu' ? '#f59e0b' : '#22c55e');
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
                    <div class="fw-bold text-dark">Top UPT Aktif</div>
                    <div class="text-muted small">Berdasarkan rencana disetujui</div>
                </div>
            </div>
            <div class="card-body px-4 py-3">
                @if($topUpt->isEmpty())
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">🏅</div>
                        <p>Belum ada data UPT</p>
                    </div>
                @else
                    @php
                        $rankColors = ['#f59e0b','#94a3b8','#cd7f32','#64748b','#64748b'];
                        $rankLabels = ['🥇','🥈','🥉','4','5'];
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

{{-- ═══════════════════════════════════════════════════════════
     ZONE 4 — PETA PEMANTAUAN (Hanya Admin Pusat)
═══════════════════════════════════════════════════════════ --}}
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
            <span class="badge bg-blue-lt text-blue px-3">
                <i class="ti ti-map-pin me-1"></i>{{ $petaData->count() }} titik
            </span>
        </div>
    </div>
    <div id="map-dashboard"></div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     ZONE 5 — DASAR HUKUM + ALUR PEMANTAUAN
═══════════════════════════════════════════════════════════ --}}
<div class="row g-3">
    {{-- Dasar Hukum --}}
    <div class="col-lg-7">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#fff1f2;">
                    <i class="ti ti-certificate text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Dasar Hukum Pemantauan HPIK</div>
                    <div class="text-muted small">Regulasi & peraturan yang mengikat</div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @php
                    $regulasi = [
                        ['uu',  'UU No. 21 Tahun 2019',         'Tentang Karantina Hewan, Ikan, dan Tumbuhan',                                          '#ef4444'],
                        ['pp',  'PP No. 15 Tahun 2022',          'Tentang Perlakuan Terhadap Hewan Karantina, Ikan Karantina, dan Tumbuhan Karantina',  '#f59e0b'],
                        ['pm',  'PerMeKP No. 12 Tahun 2020',     'Tentang Pemantauan Hama Penyakit Ikan Karantina',                                     '#3b82f6'],
                        ['sk',  'Kepmen KP No. 26 Tahun 2019',   'Tentang Penetapan Hama dan Penyakit Ikan Karantina',                                  '#8b5cf6'],
                        ['sop', 'SOP Pemantauan HPIK',           'Standar Operasional Prosedur pelaksanaan pemantauan HPIK di lapangan',               '#10b981'],
                        ['juk', 'Juklak Pemantauan HPIK 2026',   'Petunjuk Pelaksanaan Pemantauan HPIK Tahun Anggaran 2026',                           '#f97316'],
                    ];
                    $regIcons = [
                        'uu' => '⚖️', 'pp' => '📋', 'pm' => '📜',
                        'sk' => '🔏', 'sop' => '📖', 'juk' => '📌',
                    ];
                    @endphp
                    @foreach($regulasi as $reg)
                    <div class="col-sm-6">
                        <div class="reg-card p-3">
                            <div class="reg-top-bar w-100 mb-3" style="background:{{ $reg[3] }};border-radius:4px 4px 0 0;margin-top:-12px;margin-left:-12px;width:calc(100% + 24px) !important;"></div>
                            <div class="d-flex align-items-start gap-2">
                                <div class="fs-4 flex-shrink-0">{{ $regIcons[$reg[0]] }}</div>
                                <div>
                                    <div class="fw-bold small" style="color:{{ $reg[3] }};">{{ $reg[1] }}</div>
                                    <div class="text-muted" style="font-size:.78rem;line-height:1.4;margin-top:3px;">{{ $reg[2] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Alur Pemantauan --}}
    <div class="col-lg-5">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="chart-header-icon" style="background:#f0fdf4;">
                    <i class="ti ti-arrows-right text-success"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Alur Pemantauan HPIK</div>
                    <div class="text-muted small">Tahapan pelaksanaan di lapangan</div>
                </div>
            </div>
            <div class="card-body px-4 py-3">
                @php
                $alurSteps = [
                    ['icon' => 'ti-file-invoice',   'title' => 'Perencanaan & Penganggaran',  'desc' => 'UPT menyusun rencana pemantauan HPIK tahunan dan mengajukan ke BBKHIT/Pusat untuk persetujuan anggaran.'],
                    ['icon' => 'ti-checkbox',        'title' => 'Verifikasi & Persetujuan',    'desc' => 'BBKHIT dan Admin Pusat memverifikasi rencana, memberi catatan atau menyetujui untuk pelaksanaan.'],
                    ['icon' => 'ti-map-pin-search',  'title' => 'Survei & Pengambilan Sampel', 'desc' => 'Tim lapangan melakukan survei ke lokasi pemantauan dan mengambil sampel komoditas target.'],
                    ['icon' => 'ti-microscope',      'title' => 'Pengujian Laboratorium',      'desc' => 'Sampel diuji di laboratorium terakreditasi menggunakan metode yang sesuai standar HPIK.'],
                    ['icon' => 'ti-report-analytics','title' => 'Pelaporan Hasil',              'desc' => 'Hasil uji laboratorium dilaporkan ke UPT, BBKHIT, dan Pusat melalui sistem informasi.'],
                    ['icon' => 'ti-chart-bar',       'title' => 'Evaluasi & Tindak Lanjut',    'desc' => 'Data dianalisis untuk menentukan tindak lanjut: peningkatan surveilans, karantina, atau pengendalian.'],
                ];
                @endphp
                @foreach($alurSteps as $i => $step)
                <div class="alur-step">
                    <div class="alur-num">{{ $i + 1 }}</div>
                    <div>
                        <div class="fw-semibold text-dark small d-flex align-items-center gap-2">
                            <i class="ti {{ $step['icon'] }} text-primary" style="font-size:0.95rem;"></i>
                            {{ $step['title'] }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:.78rem;line-height:1.5;">
                            {{ $step['desc'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ═══════════════════════════════════════════════════════════════
// Chart.js Global Styling
// ═══════════════════════════════════════════════════════════════
Chart.defaults.font.family = "'Inter', 'Geist', system-ui, sans-serif";
Chart.defaults.color = '#64748b';

// ── 1. Grafik Pelaksanaan per Bulan (Bar) ──────────────────────
const ctxBulan = document.getElementById('chartBulan');
if (ctxBulan) {
    new Chart(ctxBulan, {
        type: 'bar',
        data: {
            labels: @json($chartBulanLabels),
            datasets: [{
                label: 'Pelaksanaan',
                data: @json($chartBulanData),
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                    g.addColorStop(0, 'rgba(59,130,246,0.85)');
                    g.addColorStop(1, 'rgba(99,102,241,0.4)');
                    return g;
                },
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#3b82f6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
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

// ── 2. Jenis HPIK (Polar Area / Donut) ────────────────────────
const ctxHpik = document.getElementById('chartHpik');
if (ctxHpik) {
    const hpikColors = [
        '#ef4444','#f97316','#f59e0b','#84cc16',
        '#10b981','#3b82f6','#8b5cf6','#ec4899'
    ];
    new Chart(ctxHpik, {
        type: 'doughnut',
        data: {
            labels: @json($chartHpikLabels),
            datasets: [{
                data: @json($chartHpikData),
                backgroundColor: hpikColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '55%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 10, boxWidth: 12, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f8fafc',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    cornerRadius: 10,
                }
            }
        }
    });
}

// ── 3. Status Perencanaan (Donut kecil) ────────────────────────
const ctxStatus = document.getElementById('chartStatus');
if (ctxStatus) {
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($statusCounts)),
            datasets: [{
                data: @json(array_values($statusCounts)),
                backgroundColor: ['#94a3b8','#f59e0b','#22c55e'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
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

// ═══════════════════════════════════════════════════════════════
// PETA LEAFLET
// ═══════════════════════════════════════════════════════════════
const mapEl = document.getElementById('map-dashboard');
if (mapEl) {
    const map = L.map('map-dashboard', {
        center: [-2.5, 118],
        zoom: 5,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    const petaData = @json($petaData);

    if (petaData.length === 0) {
        // Tampilkan pesan jika tidak ada titik
        const info = L.control({position: 'topright'});
        info.onAdd = () => {
            const div = L.DomUtil.create('div');
            div.innerHTML = `<div style="background:white;padding:10px 15px;border-radius:10px;
                box-shadow:0 2px 10px rgba(0,0,0,0.15);font-size:13px;color:#64748b;">
                📍 Belum ada titik lokasi. UPT perlu memasukkan koordinat saat input pelaksanaan.
            </div>`;
            return div;
        };
        info.addTo(map);
    } else {
        // Custom marker icon
        const markerIcon = L.divIcon({
            className: '',
            html: `<div style="
                width:14px;height:14px;border-radius:50%;
                background:#3b82f6;border:3px solid #fff;
                box-shadow:0 2px 8px rgba(59,130,246,0.5);">
            </div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });

        const bounds = [];
        petaData.forEach(p => {
            bounds.push([p.lat, p.lng]);
            L.marker([p.lat, p.lng], { icon: markerIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="min-width:200px;font-family:'Inter',sans-serif;">
                        <div style="font-weight:700;font-size:.95rem;margin-bottom:8px;color:#1e293b;">
                            📍 ${p.lokasi}
                        </div>
                        <table style="font-size:.82rem;width:100%;color:#475569;">
                            <tr><td style="padding:2px 0;color:#94a3b8;">Provinsi</td><td style="padding:2px 0 2px 8px;font-weight:600;">${p.provinsi}</td></tr>
                            <tr><td style="padding:2px 0;color:#94a3b8;">Komoditas</td><td style="padding:2px 0 2px 8px;font-weight:600;">${p.komoditas}</td></tr>
                            <tr><td style="padding:2px 0;color:#94a3b8;">UPT</td><td style="padding:2px 0 2px 8px;font-weight:600;">${p.upt}</td></tr>
                            <tr><td style="padding:2px 0;color:#94a3b8;">Tanggal</td><td style="padding:2px 0 2px 8px;">${p.tanggal}</td></tr>
                        </table>
                    </div>
                `, { maxWidth: 260 });
        });

        // Memastikan frame peta default tetap 1 Indonesia (zoom: 5, center: -2.5, 118)
        // daripada fitBounds yang bisa jadi nge-zoom ke 1 titik secara ekstrim.
        // if (bounds.length > 0) map.fitBounds(bounds, { padding: [40, 40] });
    }
}
</script>
@endsection