@extends('layouts.app')

@section('title', 'Input Pelaksanaan')
@section('page_title', 'Input Pelaksanaan Lapangan')
@section('page_subtitle', $rencana->jenis_mp . ' — ' . $rencana->kab_kota . ', ' . $rencana->provinsi)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form action="{{ route('pelaksanaan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="perencanaan_id" value="{{ $rencana->id }}">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Sampel</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required">Lokasi Pengambilan Sampel</label>
                            <input type="text" name="lokasi_pengambilan_sampel" class="form-control" placeholder="Contoh: Tambak Rakyat Desa Sukamaju" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jumlah Sampel</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_sampel" class="form-control" required min="1">
                                <span class="input-group-text">ekor/unit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Metode Pengambilan</label>
                            <select name="metode_pengambilan_sampel" class="form-select">
                                <option value="Acak">Acak (Random)</option>
                                <option value="Selektif">Selektif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin me-2 text-success"></i>Koordinat GPS
                    </h3>
                    <div class="card-options">
                        <button type="button" class="btn btn-sm btn-success" onclick="getLocation()">
                            <i class="ti ti-current-location me-1"></i>Ambil GPS Otomatis
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="geo-status" class="mb-3" style="display:none;"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" id="lat" class="form-control" placeholder="-6.1234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="lng" class="form-control" placeholder="106.8456789">
                        </div>
                    </div>
                    <div class="text-muted small mt-2">
                        <i class="ti ti-info-circle me-1"></i>Koordinat GPS bersifat opsional namun sangat dianjurkan untuk pemetaan.
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Data Lapangan
                    </button>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-link">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function getLocation() {
    var statusEl = document.getElementById('geo-status');
    statusEl.style.display = 'block';
    if (!navigator.geolocation) {
        statusEl.innerHTML = '<div class="alert alert-danger">Browser tidak mendukung GPS. Isi manual.</div>';
        return;
    }
    statusEl.innerHTML = '<div class="alert alert-info"><div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2"></div>Mengambil lokasi GPS...</div></div>';
    navigator.geolocation.getCurrentPosition(
        function(p) {
            document.getElementById("lat").value = p.coords.latitude;
            document.getElementById("lng").value = p.coords.longitude;
            statusEl.innerHTML = '<div class="alert alert-success">✅ Lokasi berhasil diambil!</div>';
        },
        function(e) {
            var msg = 'Gagal: ';
            if (e.code===1) msg += 'Izin ditolak.'; else if (e.code===2) msg += 'Tidak tersedia.'; else msg += 'Waktu habis.';
            statusEl.innerHTML = '<div class="alert alert-warning">⚠️ '+msg+' Isi koordinat manual.</div>';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>
@endsection
