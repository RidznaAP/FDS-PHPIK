@extends('layouts.app')

@section('title', 'Peta Pemantauan')
@section('page_title', 'Peta Sebaran Pemantauan HPIK')
@section('page_subtitle', $markers->count() . ' titik lokasi terpetakan')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 520px; width: 100%; border-radius: 0 0 6px 6px; }
    @media print {
        body * { visibility: hidden; }
        .card, #map, #map * { visibility: visible; }
        #map { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; z-index: 9999; }
        .card-header, .row-cards, .navbar, .header { display: none !important; }
        .card { border: none !important; }
    }
</style>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row row-deck row-cards mb-3">
    <div class="col-6 col-md-3">
        <div class="card" style="border-top: 3px solid #28a745;">
            <div class="card-body text-center py-3">
                <div class="h2 mb-0 text-success">{{ $stats['hijau'] }}</div>
                <div class="text-muted small">🟢 Bebas HPIK</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border-top: 3px solid #ffc107;">
            <div class="card-body text-center py-3">
                <div class="h2 mb-0 text-warning">{{ $stats['kuning'] }}</div>
                <div class="text-muted small">🟡 Waspada</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border-top: 3px solid #dc3545;">
            <div class="card-body text-center py-3">
                <div class="h2 mb-0 text-danger">{{ $stats['merah'] }}</div>
                <div class="text-muted small">🔴 Positif HPIK</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border-top: 3px solid #6c757d;">
            <div class="card-body text-center py-3">
                <div class="h2 mb-0 text-secondary">{{ $stats['abu'] }}</div>
                <div class="text-muted small">⚪ Belum Dievaluasi</div>
            </div>
        </div>
    </div>
</div>

{{-- Peta --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h3 class="card-title mb-0">Peta Interaktif</h3>
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-lg-flex align-items-center gap-3 small text-muted me-3">
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#28a745;"></span> Bebas HPIK</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ffc107;"></span> Waspada</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#dc3545;"></span> Positif HPIK</span>
            </div>
            <button onclick="downloadPeta()" class="btn btn-outline-primary btn-sm" id="btn-download">
                <i class="ti ti-download me-1"></i>Download Peta (PNG)
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="map"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // === TILE LAYERS (bisa diganti-ganti) ===
    const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 20, crossOrigin: true
    });
    const darkMatter = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd', maxZoom: 20, crossOrigin: true
    });
    const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri — Source: Esri, Maxar, GeoEye, Earthstar Geographics',
        maxZoom: 19, crossOrigin: true
    });

    const map = L.map('map', { zoomControl: true }).setView([-2.5, 118.0], 5);
    voyager.addTo(map); // default: Voyager (bersih, modern)

    // Layer control
    L.control.layers({
        '🗺️ Voyager (Modern)': voyager,
        '🌙 Dark Matter (Gelap)': darkMatter,
        '🛰️ Satellite (Esri)': satellite
    }, {}, { position: 'topright', collapsed: false }).addTo(map);

    // Data markers
    const markers = @json($markers);
    const colorMap = { 'hijau':'#22c55e', 'kuning':'#eab308', 'merah':'#ef4444', 'abu-abu':'#94a3b8' };
    const shadowColor = { 'hijau':'rgba(34,197,94,0.4)', 'kuning':'rgba(234,179,8,0.4)', 'merah':'rgba(239,68,68,0.4)', 'abu-abu':'rgba(148,163,184,0.3)' };

    markers.forEach(function(item) {
        const color = colorMap[item.warna] || '#94a3b8';
        const shadow = shadowColor[item.warna] || 'rgba(148,163,184,0.3)';

        // Pakai circleMarker dengan efek glow
        const circle = L.circleMarker([item.lat, item.lng], {
            radius: 10,
            fillColor: color,
            color: 'white',
            weight: 2.5,
            opacity: 1,
            fillOpacity: 0.95
        }).addTo(map);

        // Outer glow ring
        L.circleMarker([item.lat, item.lng], {
            radius: 17,
            fillColor: color,
            color: color,
            weight: 1,
            opacity: 0.25,
            fillOpacity: 0.2
        }).addTo(map);

        circle.bindPopup(`
            <div style="min-width:210px;font-family:'Inter',sans-serif;">
                <div style="background:${color};color:white;padding:8px 12px;margin:-1px -1px 0 -1px;border-radius:6px 6px 0 0;font-weight:600;display:flex;justify-content:between;align-items:center;">
                    <span>${item.kesimpulan}</span>
                </div>
                <div style="padding:10px 12px;font-size:13px;line-height:1.7;">
                    <div class="mb-1"><b>📍 Lokasi:</b> ${item.lokasi}</div>
                    <div class="mb-1"><b>🏙️ Wilayah:</b> ${item.kab_kota}, ${item.provinsi}</div>
                    <div class="mb-1"><b>🐟 Komoditas:</b> ${item.jenis_mp}</div>
                    <div class="mb-1"><b>🦠 Target:</b> ${item.jenis_hpik}</div>
                    <div class="mb-2"><b>🔬 Hasil Lab:</b> <span class="badge ${item.hasil_lab === 'Positif' ? 'bg-danger-lt' : 'bg-success-lt'}">${item.hasil_lab}</span></div>
                    
                    <a href="/pelaksanaan/${item.id}/detail" class="btn btn-primary btn-sm w-100 mt-2" style="font-size:11px;">
                        <i class="ti ti-eye me-1"></i>Lihat Detail Data
                    </a>
                </div>
            </div>
        `, { maxWidth: 280 });

        circle.bindTooltip(
            `<b>${item.lokasi}</b><br><small style="color:${color};">${item.kesimpulan}</small>`,
            { direction: 'top', offset: [0, -8] }
        );
    });

    // Memastikan peta di menu fitur ini juga default-nya 1 Indonesia penuh.
    // Menghapus fitBounds agar tidak nge-zoom ke 1 titik saja.
    // if (markers.length > 0) {
    //     const group = L.featureGroup(markers.map(m => L.circleMarker([m.lat, m.lng])));
    //     map.fitBounds(group.getBounds().pad(0.25));
    // }

    // Fungsi untuk Download Peta menjadi Gambar PNG murni
    function downloadPeta() {
        const btn = document.getElementById('btn-download');
        btn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Memproses...';
        btn.disabled = true;

        html2canvas(document.querySelector("#map"), {
            useCORS: true,
            allowTaint: false,
            scale: 2 // Resolusi HD
        }).then(canvas => {
            let link = document.createElement('a');
            link.download = 'Peta_Sebaran_HPIK_' + new Date().toISOString().slice(0,10) + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.innerHTML = '<i class="ti ti-download me-1"></i>Download Peta (PNG)';
            btn.disabled = false;
        }).catch(err => {
            alert('Gagal mendownload peta: ' + err);
            btn.innerHTML = '<i class="ti ti-download me-1"></i>Download Peta (PNG)';
            btn.disabled = false;
        });
    }
</script>
@endsection
