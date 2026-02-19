@extends('layouts.app')

@section('title', 'Peta GIS')
@section('page_title', 'Peta Sebaran Pemantauan HPIK')
@section('page_subtitle', $markers->count() . ' titik lokasi terpetakan')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 520px; width: 100%; border-radius: 0 0 6px 6px; }
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
        <div class="d-flex align-items-center gap-3 small text-muted">
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#28a745;"></span> Bebas HPIK</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ffc107;"></span> Waspada</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#dc3545;"></span> Positif HPIK</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#6c757d;"></span> Belum Evaluasi</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="map"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-2.5, 118.0], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const markers = @json($markers);
    const colorMap = { 'hijau':'#28a745', 'kuning':'#ffc107', 'merah':'#dc3545', 'abu-abu':'#6c757d' };

    markers.forEach(function(item) {
        const color = colorMap[item.warna] || '#6c757d';
        const marker = L.circleMarker([item.lat, item.lng], {
            radius: 12, fillColor: color, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.9
        }).addTo(map);

        marker.bindPopup(`
            <div style="min-width:200px;font-family:Inter,sans-serif;">
                <h6 style="color:${color};margin-bottom:6px;">● ${item.kesimpulan}</h6>
                <hr style="margin:5px 0;">
                <b>Lokasi:</b> ${item.lokasi}<br>
                <b>Wilayah:</b> ${item.kab_kota}, ${item.provinsi}<br>
                <b>Komoditas:</b> ${item.jenis_mp}<br>
                <b>Target HPIK:</b> ${item.jenis_hpik}<br>
                <b>Hasil Lab:</b> ${item.hasil_lab}
            </div>
        `);
        marker.bindTooltip('<b>' + item.lokasi + '</b><br><small>' + item.kab_kota + '</small>', { direction: 'top' });
    });

    if (markers.length > 0) {
        const group = L.featureGroup(markers.map(m => L.circleMarker([m.lat, m.lng])));
        map.fitBounds(group.getBounds().pad(0.2));
    }
</script>
@endsection
