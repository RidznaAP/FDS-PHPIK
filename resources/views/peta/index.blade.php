@extends('layouts.app')

@section('title', 'Peta Pemantauan')
@section('page_title', 'Peta Sebaran Pemantauan HPIK')
@section('page_subtitle', $stats['total'] . ' titik lokasi terpetakan — Tahun ' . $selectedYear)

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ── Map Container ─────────────────────────────────────────────── */
    #map {
        height: 540px;
        width: 100%;
        border-radius: 0 0 8px 8px;
        /* Ocean / sea background colour */
        background: linear-gradient(135deg, #b8d9ed 0%, #9fcde6 50%, #87bedc 100%);
    }

    /* Hide default Leaflet tile pane (not used) */
    .leaflet-tile-pane { display: none !important; }

    /* Province label tooltips */
    .prov-label {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        font-size: 0.6rem;
        font-weight: 700;
        color: #334155;
        text-align: center;
        white-space: nowrap;
        pointer-events: none;
        text-shadow: 0 0 3px #fff, 0 0 3px #fff;
    }

    /* ── Filter Panel ──────────────────────────────────────────────── */
    .filter-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
    }
    .filter-panel .form-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #64748b;
        margin-bottom: 0.4rem;
    }
    .filter-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #eff6ff; color: #1d4ed8; border-radius: 20px;
        padding: 2px 10px; font-size: 0.75rem; font-weight: 600;
    }
    .legend-dot {
        width: 12px; height: 12px; border-radius: 50%;
        display: inline-block; flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.8);
    }

    /* ── GeoJSON badge (map info) ──────────────────────────────────── */
    .map-badge-geojson {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(4px);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.6rem;
        color: #64748b;
        z-index: 1000;
        pointer-events: none;
        border: 1px solid #e2e8f0;
    }

    @media print {
        body * { visibility: hidden; }
        .card, #map, #map * { visibility: visible; }
        #map { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; z-index: 9999; }
        .card-header, .row-cards, .navbar, .header, .filter-panel, .row.g-3.mb-3 { display: none !important; }
        .card { border: none !important; }
    }

    /* Clean Capture Helper */
    .capturing .leaflet-control-container,
    .capturing .card-header,
    .capturing .filter-panel,
    .capturing .btn-group-toggle {
        display: none !important;
    }
    .capturing #map { border-radius: 8px !important; }

    .btn-group-toggle .btn { font-size: 0.75rem; border-radius: 50px !important; padding: 5px 15px; }

    /* Fullscreen Mode Fix */
    #map-container-capture:fullscreen { background: #fff; width: 100vw; height: 100vh; }
    #map-container-capture:fullscreen #map { height: 100vh !important; width: 100vw !important; border-radius: 0; }
    #map-container-capture:fullscreen .leaflet-control-container { display: block !important; }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        #map { height: 380px; }
        .filter-panel { padding: 1rem; margin-bottom: 0.5rem; }
        .row-deck > .col-6 { margin-bottom: 0.5rem; }
    }
</style>
@endsection

@section('content')

{{-- ═══ STAT CARDS ═══ --}}
<div class="row row-deck row-cards mb-3">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-top: 3px solid #6366f1;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size:2.2rem; line-height:1;">🗺️</div>
                    <div>
                        <div class="h2 mb-0">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total Titik</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-top: 3px solid #ef4444;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size:2.2rem; line-height:1;">🔴</div>
                    <div>
                        <div class="h2 mb-0 text-danger">{{ $stats['positif'] }}</div>
                        <div class="text-muted small">Uji Positif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-top: 3px solid #22c55e;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size:2.2rem; line-height:1;">🟢</div>
                    <div>
                        <div class="h2 mb-0 text-success">{{ $stats['negatif'] }}</div>
                        <div class="text-muted small">Nihil</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-top: 3px solid #94a3b8;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size:2.2rem; line-height:1;">⚪</div>
                    <div>
                        <div class="h2 mb-0 text-secondary">{{ $stats['pending'] }}</div>
                        <div class="text-muted small">Belum Diproses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ═══ SIDEBAR FILTER ═══ --}}
    <div class="col-lg-3">
        <div class="filter-panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-bold text-dark" style="font-size:0.9rem;">
                    <i class="ti ti-filter me-1 text-primary"></i> Filter Peta
                </div>
                @if(count(array_filter($filters)))
                    <a href="{{ route('peta.index') }}" class="text-danger small text-decoration-none">
                        <i class="ti ti-x"></i> Reset
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('peta.index') }}" id="filter-form-peta">
                {{-- Filter Tahun --}}
                <div class="mb-3">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Provinsi --}}
                <div class="mb-3">
                    <label class="form-label">Provinsi</label>
                    <select name="provinsi" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinsiList as $prov)
                            <option value="{{ $prov }}" {{ isset($filters['provinsi']) && $filters['provinsi'] == $prov ? 'selected' : '' }}>
                                {{ $prov }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Hasil Lab --}}
                <div class="mb-3">
                    <label class="form-label">Hasil Lab</label>
                    <div class="d-flex flex-column gap-1 mt-1">
                        @php
                            $hasilOptions = [
                                '' => ['label' => 'Semua', 'color' => '#64748b'],
                                'Positif' => ['label' => 'Positif Penyakit', 'color' => '#ef4444'],
                                'Negatif' => ['label' => 'Nihil', 'color' => '#22c55e'],
                                'belum'   => ['label' => 'Belum Diuji', 'color' => '#94a3b8'],
                            ];
                            $activeHasil = $filters['hasil_lab'] ?? '';
                        @endphp
                        @foreach($hasilOptions as $val => $opt)
                        <label class="d-flex align-items-center gap-2 py-1 px-2 rounded cursor-pointer small fw-semibold" style="cursor:pointer; background: {{ $activeHasil == $val ? '#f0f9ff' : 'transparent' }}; border: 1px solid {{ $activeHasil == $val ? '#bae6fd' : 'transparent' }};">
                            <input type="radio" name="hasil_lab" value="{{ $val }}" class="form-check-input m-0"
                                   {{ $activeHasil == $val ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="legend-dot" style="background:{{ $opt['color'] }};"></span>
                            {{ $opt['label'] }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Filter HPIK --}}
                <div class="mb-3">
                    <label class="form-label">Jenis HPIK</label>
                    <select name="hpik" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua HPIK</option>
                        @foreach($hpikList as $h)
                            @foreach(array_map('trim', explode(',', $h)) as $hItem)
                                @if($hItem)
                                    <option value="{{ $hItem }}" {{ isset($filters['hpik']) && $filters['hpik'] == $hItem ? 'selected' : '' }}>
                                        {{ $hItem }}
                                    </option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                </div>

                {{-- Preserve filters saat submit manual jika diperlukan --}}
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="ti ti-search me-1"></i>Terapkan Filter
                </button>
            </form>

            {{-- Legenda --}}
            <hr class="my-3">
            <div class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:.06em;">Legenda Marker</div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 small"><span class="legend-dot" style="background:#ef4444;"></span> Positif Penyakit</div>
                <div class="d-flex align-items-center gap-2 small"><span class="legend-dot" style="background:#22c55e;"></span> Nihil (Negatif)</div>
                <div class="d-flex align-items-center gap-2 small"><span class="legend-dot" style="background:#94a3b8;"></span> Belum Diuji</div>
                <div class="d-flex align-items-center gap-2 small"><span class="legend-dot" style="background:#3b82f6;"></span> Titik Umum</div>
            </div>
        </div>

        {{-- Top Penyakit Ringkasan --}}
        @if(count($topPenyakit) > 0)
        <div class="filter-panel mt-3">
            <div class="fw-bold text-dark small mb-3">
                <i class="ti ti-virus me-1 text-danger"></i> Top HPIK Terdeteksi
            </div>
            @php $maxPos = max(array_column($topPenyakit, 'positif')) ?: 1; @endphp
            @foreach($topPenyakit as $nama => $data)
            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small fw-semibold text-truncate" style="max-width:130px;" title="{{ $nama }}">{{ $nama }}</span>
                    <span class="text-danger small fw-bold">{{ $data['positif'] }}+</span>
                </div>
                <div class="progress" style="height:5px;border-radius:4px;background:#f1f5f9;">
                    <div class="progress-bar bg-danger" style="width:{{ round(($data['positif']/$maxPos)*100) }}%;border-radius:4px;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- MODE TAMPILAN --}}
        <div class="filter-panel mt-3">
            <div class="fw-bold text-dark small mb-3">
                <i class="ti ti-layout me-1 text-primary"></i> Mode Tampilan Peta
            </div>
            <div class="btn-group w-100 btn-group-toggle mb-3" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btn-mode-marker" onclick="switchMode('marker')">
                    <i class="ti ti-map-pin me-1"></i>Titik
                </button>
                <button type="button" class="btn btn-outline-primary" id="btn-mode-wilayah" onclick="switchMode('wilayah')">
                    <i class="ti ti-map-2 me-1"></i>Wilayah
                </button>
            </div>
            
            <div id="panel-download-tematik" class="d-none animate-fade-in">
                <button onclick="downloadPetaTematik()" class="btn btn-teal text-white w-100 shadow-sm">
                    <i class="ti ti-download me-1"></i>Unduh Peta Wilayah
                </button>
                <p class="text-muted smaller mt-2 mb-0" style="font-size:0.65rem;">
                    *Menghasilkan peta bersih tanpa titik lokasi, ideal untuk lampiran laporan.
                </p>
            </div>
        </div>
    </div>

    {{-- ═══ PETA UTAMA ═══ --}}
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="card-title mb-0">Peta Interaktif</h3>
                    @if(count(array_filter($filters)))
                    <div class="mt-1 d-flex gap-1 flex-wrap">
                        @foreach(array_filter($filters) as $key => $val)
                            <span class="filter-badge">
                                {{ $key }}: {{ $val }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-lt text-primary px-3 d-none d-sm-inline">
                        <i class="ti ti-calendar me-1"></i>{{ $selectedYear }}
                    </span>
                    <span class="badge bg-blue-lt text-blue px-3 d-none d-sm-inline">
                        <i class="ti ti-map-pin me-1"></i>{{ $stats['total'] }} titik
                    </span>
                    <div class="btn-group">
                        <button onclick="toggleFullScreen()" class="btn btn-outline-primary btn-sm" title="Tampilan Layar Penuh">
                            <i class="ti ti-maximize me-1"></i>Full Screen
                        </button>
                        <button onclick="downloadPeta()" class="btn btn-primary btn-sm" id="btn-download">
                            <i class="ti ti-download me-1"></i>PNG (Aktual)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 position-relative" id="map-container-capture">
                <div id="map"></div>
                <div class="map-badge-geojson">🗺️ GeoJSON · BIG/Ardian28 · 38 Provinsi</div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ═══════════════════════════════════════════════════════════════
    // PETA BERBASIS GEOJSON MURNI — tanpa tile layer eksternal
    // Seluruh geometri wilayah dimuat dari file lokal.
    // ═══════════════════════════════════════════════════════════════

    // Inisialisasi peta — TANPA tile layer
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        boxZoom: false,
        dragging: true,
        maxBounds: [[-15, 88], [16, 152]],
        minZoom: 4,
        attributionControl: false   // kita buat sendiri
    }).setView([-2.0, 116.0], 5);

    // Posisi tombol zoom agar tidak menutupi filter
    map.zoomControl.setPosition('bottomright');

    // Attribution kecil di sudut kiri bawah
    L.control.attribution({ position: 'bottomleft', prefix: false })
        .addAttribution('Geometri: <a href="https://github.com/ardian28/GeoJson-Indonesia-38-Provinsi" target="_blank">Ardian28/BIG</a>')
        .addTo(map);

    // ── Warna marker sesuai hasil lab ──────────────────────────────
    function getMarkerColor(hasilLab) {
        if (hasilLab === 'Positif')    return '#ef4444';  // merah
        if (hasilLab === 'Negatif')    return '#22c55e';  // hijau
        if (hasilLab === 'Belum Diuji') return '#94a3b8'; // abu
        return '#3b82f6';                                 // biru
    }

    // ── Data dari Controller ────────────────────────────────────────
    const markers        = @json($markers);
    const dominantProvinsi = @json($dominantProvinsi);

    // ── Layer marker titik ─────────────────────────────────────────
    const markerLayer = L.layerGroup().addTo(map);

    markers.forEach(function(item) {
        const color = getMarkerColor(item.hasil_lab);

        const markerIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="width:18px;height:18px;display:flex;align-items:center;justify-content:center;">
                       <div style="width:9px;height:9px;border-radius:50%;
                            background:${color};
                            box-shadow:0 0 0 3px ${color}44, 0 0 6px ${color}88;
                            border:2px solid rgba(255,255,255,0.9);"></div>
                   </div>`,
            iconSize: [18, 18],
            iconAnchor: [9, 9],
        });

        const circle = L.marker([item.lat, item.lng], { icon: markerIcon });
        markerLayer.addLayer(circle);

        const hasilBadgeClass = item.hasil_lab === 'Positif' ? 'bg-danger' :
                               (item.hasil_lab === 'Negatif' ? 'bg-success' : 'bg-secondary');

        circle.bindPopup(`
            <div style="min-width:220px;font-family:'Inter',sans-serif;">
                <div style="background:${color};color:white;padding:8px 12px;margin:-1px -1px 0 -1px;border-radius:6px 6px 0 0;font-weight:600;text-align:center;">
                    ${item.jenis_hpik}
                </div>
                <div style="padding:10px 12px;font-size:13px;line-height:1.7;">
                    <div><b>📍 Lokasi:</b> ${item.lokasi || '-'}</div>
                    <div><b>Wilayah:</b> ${item.kab_kota}, ${item.provinsi}</div>
                    <div><b>🏢 UPT:</b> ${item.upt}</div>
                    <div><b>📅 Tanggal:</b> ${item.tanggal}</div>
                    <div><b>🐟 Media:</b> ${item.jenis_mp}</div>
                    <div><b>🔬 Hasil:</b> <span class="badge ${hasilBadgeClass}" style="font-size:11px;color:white;">${item.hasil_raw}</span></div>
                    <a href="/pelaksanaan/${item.id}/detail"
                       class="btn btn-primary btn-sm w-100 mt-2"
                       style="font-size:11px;font-weight:600;color:white;">
                        <i class="ti ti-eye me-1"></i>Lihat Detail Dokumen
                    </a>
                </div>
            </div>
        `, { maxWidth: 300 });

        circle.bindTooltip(
            `<b>${item.lokasi || item.kab_kota}</b><br><small style="color:${color};">${item.jenis_hpik}</small>`,
            { direction: 'top', offset: [0, -10] }
        );
    });

    // ═══════════════════════════════════════════════════════════════
    // GEOJSON PROVINSI — dimuat dari file lokal
    // ═══════════════════════════════════════════════════════════════
    const geoJsonLayer     = L.layerGroup().addTo(map);  // lapisan polygon
    const labelLayer       = L.layerGroup().addTo(map);  // lapisan label
    let   geoJsonRawData   = null;   // simpan untuk re-style mode
    let   geoJsonLeaflet   = null;   // instance L.geoJSON

    // Cocokkan nama provinsi antara GeoJSON dan data dominantProvinsi
    function matchProvince(provName) {
        const upper = (provName || '').toUpperCase().trim();
        for (let key in dominantProvinsi) {
            const bk = key.toUpperCase().trim();
            if (upper === bk || upper.includes(bk) || bk.includes(upper)) {
                return dominantProvinsi[key];
            }
        }
        return null;
    }

    // Style provinsi berdasarkan mode & data
    function getProvinceStyle(f, mode) {
        const d = matchProvince(f.properties.PROVINSI);
        if (mode === 'wilayah') {
            // Choropleth penuh
            if (d) {
                return {
                    fillColor:   d.color,
                    fillOpacity: 0.75,
                    color:       '#ffffff',
                    weight:      1.2
                };
            }
            return { fillColor: '#dde3ea', fillOpacity: 0.65, color: '#a0aec0', weight: 0.8 };
        } else {
            // Mode Titik — polygon tipis sebagai kontur
            if (d) {
                return {
                    fillColor:   d.color,
                    fillOpacity: 0.18,
                    color:       '#94a3b8',
                    weight:      0.8
                };
            }
            return { fillColor: '#e8edf2', fillOpacity: 0.55, color: '#a0aec0', weight: 0.7 };
        }
    }

    // Buat label (tooltip permanen) untuk mode wilayah
    function addProvinceLabels(data) {
        labelLayer.clearLayers();
        data.features.forEach(function(f) {
            try {
                const name = f.properties.PROVINSI || '';
                if (!name) return;
                // Hitung centroid sederhana dari bbox
                const coords = f.geometry.coordinates;
                const geomType = f.geometry.type;
                let pts = [];
                if (geomType === 'Polygon') pts = coords[0];
                else if (geomType === 'MultiPolygon') {
                    // ambil polygon terbesar
                    let maxLen = 0;
                    coords.forEach(poly => { if (poly[0].length > maxLen) { maxLen = poly[0].length; pts = poly[0]; } });
                }
                if (!pts.length) return;
                const lngs = pts.map(p => p[0]);
                const lats = pts.map(p => p[1]);
                const clat = (Math.min(...lats) + Math.max(...lats)) / 2;
                const clng = (Math.min(...lngs) + Math.max(...lngs)) / 2;

                const shortName = name.replace(/^PROVINSI\s*/i, '')
                                      .replace('KEPULAUAN ', 'KEP. ')
                                      .replace('KALIMANTAN ', 'KAL. ')
                                      .replace('SULAWESI ', 'SUL. ');

                L.tooltip({ permanent: true, direction: 'center', className: 'prov-label', interactive: false })
                    .setLatLng([clat, clng])
                    .setContent(shortName)
                    .addTo(labelLayer);
            } catch(e) { /* skip jika ada feature bermasalah */ }
        });
    }

    // Muat GeoJSON dari file lokal
    function loadProvincialOverlay() {
        fetch('{{ asset('geojson/indonesia-provinces.geojson') }}')
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat GeoJSON: ' + res.status);
                return res.json();
            })
            .then(data => {
                geoJsonRawData = data;

                geoJsonLeaflet = L.geoJSON(data, {
                    style: f => getProvinceStyle(f, currentMode),
                    onEachFeature: function(f, l) {
                        const d = matchProvince(f.properties.PROVINSI);
                        let info = '';
                        if (d) {
                            if (d.status === 'nihil') {
                                info = `<br><span style="font-size:0.8rem;color:#64748b;">Status:</span> <b style="color:#22c55e;">Nihil / Aman</b> (${d.count} uji nihil)`;
                            } else {
                                info = `<br><span style="font-size:0.8rem;color:#64748b;">Dominan:</span> <b style="color:${d.color}">${d.dominant}</b> (${d.count} Uji Positif)`;
                            }
                        }

                        l.bindTooltip('<b>' + f.properties.PROVINSI + '</b>' + info, { sticky: true });

                        l.on({
                            mouseover: function(e) {
                                e.target.setStyle({ fillOpacity: Math.min((e.target.options.fillOpacity || 0.5) + 0.25, 0.92), weight: 2 });
                            },
                            mouseout: function(e) {
                                geoJsonLeaflet.resetStyle(e.target);
                            }
                        });
                    }
                }).addTo(geoJsonLayer);
            })
            .catch(err => {
                console.error('GeoJSON load error:', err);
            });
    }

    loadProvincialOverlay();

    // ═══════════════════════════════════════════════════════════════
    // MODE TAMPILAN
    // ═══════════════════════════════════════════════════════════════
    let currentMode = 'marker';

    function switchMode(mode) {
        currentMode = mode;
        const btnMarker = document.getElementById('btn-mode-marker');
        const btnWilayah = document.getElementById('btn-mode-wilayah');
        const panelDl   = document.getElementById('panel-download-tematik');

        if (btnMarker) btnMarker.classList.toggle('active', mode === 'marker');
        if (btnWilayah) btnWilayah.classList.toggle('active', mode === 'wilayah');
        if (panelDl)   panelDl.classList.toggle('d-none', mode !== 'wilayah');

        // Re-style semua polygon
        if (geoJsonLeaflet) {
            geoJsonLeaflet.eachLayer(l => {
                if (l.feature) l.setStyle(getProvinceStyle(l.feature, mode));
            });
        }

        if (mode === 'marker') {
            markerLayer.addTo(map);
            labelLayer.clearLayers();   // hapus label provinsi
        } else {
            markerLayer.remove();
            if (geoJsonRawData) addProvinceLabels(geoJsonRawData);  // tampilkan label
        }
    }

    // ── Download ───────────────────────────────────────────────────
    function downloadPetaTematik() { captureMap('Peta_Wilayah_HPIK_'); }
    function downloadPeta()        { captureMap('Peta_Titik_HPIK_');   }

    function captureMap(filenamePrefix) {
        const mapContainer = document.getElementById('map-container-capture');
        const oldCenter = map.getCenter();
        const oldZoom   = map.getZoom();

        const overlay = document.createElement('div');
        overlay.style = 'position:absolute;top:0;left:0;right:0;bottom:0;' +
                        'background:rgba(255,255,255,0.85);z-index:10000;' +
                        'display:flex;align-items:center;justify-content:center;' +
                        'font-weight:bold;color:#206bc4;border-radius:8px;';
        overlay.innerHTML = '<div class="text-center"><div class="spinner-border mb-2 text-primary"></div><br>Sedang Menyiapkan Gambar Peta...</div>';
        mapContainer.appendChild(overlay);

        map.setView([-2.0, 116.0], 5, { animate: false });

        setTimeout(() => {
            mapContainer.classList.add('capturing');
            html2canvas(mapContainer, {
                useCORS: true,
                scale: 2,
                logging: false,
                backgroundColor: '#9fcde6',  // warna laut
                allowTaint: true,
                ignoreElements: el => el === overlay
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = filenamePrefix + new Date().toISOString().slice(0, 10) + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                mapContainer.classList.remove('capturing');
                if (overlay.parentNode) mapContainer.removeChild(overlay);
                map.setView(oldCenter, oldZoom, { animate: false });
            }).catch(err => {
                console.error('Capture Error:', err);
                if (overlay.parentNode) mapContainer.removeChild(overlay);
                alert('Gagal mengunduh peta. Silakan gunakan fitur \'Print\' browser sebagai alternatif.');
            });
        }, 1500);
    }

    // ── Fullscreen ─────────────────────────────────────────────────
    function toggleFullScreen() {
        const mapElem = document.getElementById('map-container-capture');
        if (!document.fullscreenElement) {
            mapElem.requestFullscreen().catch(err => alert(`Error: ${err.message}`));
        } else {
            document.exitFullscreen();
        }
    }

    document.addEventListener('fullscreenchange', () => {
        setTimeout(() => map.invalidateSize(), 300);
    });
</script>
@endsection
