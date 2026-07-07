@extends('layouts.app')

@section('title', 'Peta Pemantauan')
@section('page_title', 'Peta Sebaran Pemantauan HPIK')
@section('page_subtitle', $stats['total'] . ' titik lokasi terpetakan — Tahun ' . $selectedYear)

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 540px; width: 100%; border-radius: 0 0 8px 8px; }

    /* Filter Panel */
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
            <div class="card-body p-0" id="map-container-capture">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // === TILE LAYERS ===
    const cartoLight = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 19, crossOrigin: true
    });
    const baseLabels = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 19, crossOrigin: true,
        pane: 'shadowPane'
    });
    const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 20, crossOrigin: true
    });
    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'],
        attribution: '&copy; Google Maps'
    });
    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'],
        attribution: '&copy; Google Maps'
    });

    const map = L.map('map', { 
        zoomControl: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        boxZoom: false,
        dragging: true,
        maxBounds: [[-15, 90], [15, 150]],
        minZoom: 4
    }).setView([-2.0, 116.0], 5);
    
    cartoLight.addTo(map);
    baseLabels.addTo(map);

    // ── Load World GeoJSON (Fokus Indonesia) ────────────────────────
    fetch('https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json')
        .then(res => res.json())
        .then(worldData => {
            L.geoJSON(worldData, {
                style: function(f) {
                    const isIndo = f.id === 'IDN' || f.properties.name === 'Indonesia';
                    return {
                        fillColor: isIndo ? 'transparent' : '#f1f5f9',
                        fillOpacity: isIndo ? 0 : 0.9,
                        color: 'transparent',
                        weight: 0,
                        interactive: false
                    };
                }
            }).addTo(map);
        });

    L.control.layers({
        '🗺️ Carto Light (Dashboard)': cartoLight,
        '🗺️ Voyager': voyager,
        '🛰️ Google Hybrid': googleHybrid,
        '📍 Google Streets': googleStreets,
    }, {}, { position: 'topright', collapsed: true }).addTo(map);

    // === COLOR MAP ===
    function getMarkerColor(hasilLab) {
        if (hasilLab === 'Positif') return '#ef4444';      // Red
        if (hasilLab === 'Negatif') return '#22c55e';      // Green
        if (hasilLab === 'Belum Diuji') return '#94a3b8';  // Grey
        return '#3b82f6';                                  // Blue
    }

    // === DATA MARKERS ===
    const markers = @json($markers);
    const markerLayer = L.layerGroup().addTo(map);
    const dominantProvinsi = @json($dominantProvinsi);

    markers.forEach(function(item) {
        const color = getMarkerColor(item.hasil_lab);

        const markerIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;">
                       <div style="width:6px;height:6px;border-radius:50%;background:${color};box-shadow:0 0 4px ${color};"></div>
                   </div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
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
                    <div><b>🔬 Hasil:</b> <span class="badge ${hasilBadgeClass}" style="font-size:11px; color:white;">${item.hasil_raw}</span></div>
                    <a href="/pelaksanaan/${item.id}/detail" class="btn btn-primary btn-sm w-100 mt-2" style="font-size:11px; font-weight:600; color:white;">
                        <i class="ti ti-eye me-1"></i>Lihat Detail Dokumen
                    </a>
                </div>
            </div>
        `, { maxWidth: 300 });

        circle.bindTooltip(
            `<b>${item.lokasi || item.kab_kota}</b><br><small style="color:${color};">${item.jenis_hpik}</small>`,
            { direction: 'top', offset: [0, -8] }
        );
    });

    // === GEOJSON PROVINSI (Same as Dashboard) ===
    const geoJsonLayer = L.layerGroup().addTo(map);
    
    function loadProvincialOverlay() {
        fetch('https://raw.githubusercontent.com/ardian28/GeoJson-Indonesia-38-Provinsi/master/Provinsi/38%20Provinsi%20Indonesia%20-%20Provinsi.json')
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function(f) {
                        let provName = (f.properties.PROVINSI || '').toUpperCase().trim();
                        let color = 'transparent';
                        let fillOp = 0;
                        
                        for(let key in dominantProvinsi) {
                            let bkKey = key.toUpperCase().trim();
                            if (provName === bkKey || provName.includes(bkKey) || bkKey.includes(provName)) {
                                color = dominantProvinsi[key].color;
                                fillOp = 0.5;
                                break;
                            }
                        }
                        return { fillColor: color, weight: 1, opacity: 1, color: fillOp > 0 ? '#ffffff' : '#cbd5e1', fillOpacity: fillOp };
                    },
                    onEachFeature: function(f, l) {
                        let provName = (f.properties.PROVINSI || '').toUpperCase().trim();
                        let info = '';
                        for(let key in dominantProvinsi) {
                            let bkKey = key.toUpperCase().trim();
                            if (provName === bkKey || provName.includes(bkKey) || bkKey.includes(provName)) {
                                let d = dominantProvinsi[key];
                                if (d.status === 'nihil') {
                                    info = `<br><span style="font-size:0.8rem;color:#64748b;">Status:</span> <b style="color:#22c55e;">Nihil / Aman</b> (${d.count} uji nihil)`;
                                } else {
                                    info = `<br><span style="font-size:0.8rem;color:#64748b;">Dominan:</span> <b style="color:${d.color}">${d.dominant}</b> (${d.count} Uji Positif)`;
                                }
                                break;
                            }
                        }
                        if (info !== '') {
                            l.bindTooltip('<b>' + f.properties.PROVINSI + '</b>' + info, { sticky: true });
                            l.on({
                                mouseover: function(e) { e.target.setStyle({ fillOpacity: 0.8, weight: 2 }); },
                                mouseout: function(e)  { e.target.setStyle({ fillOpacity: 0.5, weight: 1 }); }
                            });
                        }
                    }
                }).addTo(geoJsonLayer);
            });
    }

    loadProvincialOverlay();

    // === MODE TAMPILAN (Adjusted for consistency) ===
    let currentMode = 'marker';
    function switchMode(mode) {
        currentMode = mode;
        const btnMarker = document.getElementById('btn-mode-marker');
        const btnWilayah = document.getElementById('btn-mode-wilayah');
        const panelDl = document.getElementById('panel-download-tematik');
        
        if(btnMarker) btnMarker.classList.toggle('active', mode === 'marker');
        if(btnWilayah) btnWilayah.classList.toggle('active', mode === 'wilayah');
        if(panelDl) panelDl.classList.toggle('d-none', mode !== 'wilayah');

        if (mode === 'marker') {
            markerLayer.addTo(map);
            geoJsonLayer.eachLayer(layer => {
                layer.setStyle({ fillOpacity: 0.5 });
            });
        } else {
            // "Wilayah" mode in detailed view usually means thematic map only
            markerLayer.remove();
            geoJsonLayer.eachLayer(layer => {
                layer.setStyle({ fillOpacity: 0.8 });
            });
        }
    }

    function downloadPetaTematik() {
        captureMap('Peta_Wilayah_HPIK_');
    }

    function downloadPeta() {
        captureMap('Peta_Titik_HPIK_');
    }

    function captureMap(filenamePrefix) {
        const mapContainer = document.getElementById('map-container-capture');
        const oldCenter = map.getCenter();
        const oldZoom = map.getZoom();
        
        const overlay = document.createElement('div');
        overlay.style = "position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.85);z-index:10000;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#206bc4;border-radius:8px;";
        overlay.innerHTML = '<div class="text-center"><div class="spinner-border mb-2 text-primary"></div><br>Sedang Menyiapkan Gambar Peta...</div>';
        mapContainer.appendChild(overlay);

        map.setView([-2.0, 116.0], 5, { animate: false });

        setTimeout(() => {
            mapContainer.classList.add('capturing');

            html2canvas(mapContainer, {
                useCORS: true,
                scale: 2,
                logging: false,
                backgroundColor: '#ffffff',
                allowTaint: true,
                ignoreElements: (el) => {
                    return el === overlay;
                }
            }).then(canvas => {
                let link = document.createElement('a');
                link.download = filenamePrefix + new Date().toISOString().slice(0,10) + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                mapContainer.classList.remove('capturing');
                if(overlay.parentNode) mapContainer.removeChild(overlay);
                map.setView(oldCenter, oldZoom, { animate: false });
            }).catch(err => {
                console.error("Capture Error:", err);
                if(overlay.parentNode) mapContainer.removeChild(overlay);
                alert("Gagal mengunduh peta. Silakan gunakan fitur 'Print' browser sebagai alternatif.");
            });
        }, 1500);
    }

    function toggleFullScreen() {
        const mapElem = document.getElementById('map-container-capture');
        if (!document.fullscreenElement) {
            mapElem.requestFullscreen().catch(err => {
                alert(`Error: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    document.addEventListener('fullscreenchange', () => {
        setTimeout(() => {
            map.invalidateSize();
        }, 300);
    });
</script>
@endsection
