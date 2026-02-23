@extends('layouts.app')

@section('title', 'Input Pelaksanaan')
@section('page_title', 'Input Pelaksanaan Lapangan')
@section('page_subtitle', $rencana->jenis_mp . ' — ' . $rencana->kab_kota . ', ' . $rencana->provinsi)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('pelaksanaan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="perencanaan_id" value="{{ $rencana->id }}">

            {{-- ============================================================ --}}
            {{-- CARD 1: Informasi Rencana (readonly, info saja) --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title"><i class="ti ti-clipboard-list me-2"></i>Referensi Rencana Pemantauan</h3>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2 text-sm">
                        <div class="col-sm-3">
                            <div class="text-muted small">Wilayah</div>
                            <div class="fw-semibold">{{ $rencana->kab_kota }}, {{ $rencana->provinsi }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-muted small">Jenis MP</div>
                            <div class="fw-semibold">{{ $rencana->jenis_mp }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-muted small">Target HPIK</div>
                            <div class="fw-semibold">{{ $rencana->jenis_hpik }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-muted small">Target Uji</div>
                            <div class="fw-semibold">{{ $rencana->target_uji }} sampel</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 2: Lokasi & Waktu Pemantauan --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-calendar me-2"></i>Lokasi & Waktu Pemantauan</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label required">Lokasi Pengambilan Sampel (Prov/Kab/Kec/Desa)</label>
                            <input type="text" name="lokasi_pengambilan_sampel"
                                class="form-control @error('lokasi_pengambilan_sampel') is-invalid @enderror"
                                value="{{ old('lokasi_pengambilan_sampel') }}"
                                placeholder="Contoh: Kec. Alue Naga, Kab. Banda Aceh, Prov. Aceh" required>
                            @error('lokasi_pengambilan_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Tanggal Pemantauan</label>
                            <input type="date" name="tanggal_pemantauan"
                                class="form-control @error('tanggal_pemantauan') is-invalid @enderror"
                                value="{{ old('tanggal_pemantauan', date('Y-m-d')) }}" required>
                            @error('tanggal_pemantauan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 3: Data Ikan / Contoh Uji (Tabel 5, Kol 4-8) --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-fish me-2"></i>Data Ikan (Contoh Uji)</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Jenis Ikan (Nama Lokal)</label>
                            <input type="text" name="jenis_ikan"
                                class="form-control @error('jenis_ikan') is-invalid @enderror"
                                value="{{ old('jenis_ikan') }}"
                                placeholder="Contoh: Udang Vanname" required>
                            @error('jenis_ikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Latin (Ilmiah)</label>
                            <input type="text" name="nama_latin"
                                class="form-control"
                                value="{{ old('nama_latin') }}"
                                placeholder="Contoh: Litopenaeus vannamei">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Panjang Rata-rata (cm)</label>
                            <div class="input-group">
                                <input type="number" name="panjang_cm" step="0.01" min="0"
                                    class="form-control" value="{{ old('panjang_cm') }}" placeholder="0.00">
                                <span class="input-group-text">cm</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Berat Rata-rata (gram)</label>
                            <div class="input-group">
                                <input type="number" name="berat_gram" step="0.01" min="0"
                                    class="form-control" value="{{ old('berat_gram') }}" placeholder="0.00">
                                <span class="input-group-text">gram</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Asal Benih / Induk</label>
                            <input type="text" name="asal_benih_induk"
                                class="form-control" value="{{ old('asal_benih_induk') }}"
                                placeholder="Contoh: Hatchery Lokal">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Padat Tebar</label>
                            <div class="input-group">
                                <input type="number" name="padat_tebar" min="0"
                                    class="form-control" value="{{ old('padat_tebar') }}" placeholder="0">
                                <span class="input-group-text">ekor/m²</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 4: Jumlah Sampel & Metode Pengambilan --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-test-pipe me-2"></i>Sampel Pengujian</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Jumlah Sampel</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_sampel"
                                    class="form-control @error('jumlah_sampel') is-invalid @enderror"
                                    value="{{ old('jumlah_sampel') }}" required min="1">
                                <span class="input-group-text">ekor/unit</span>
                            </div>
                            @error('jumlah_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Metode Pengambilan Sampel</label>
                            <select name="metode_pengambilan_sampel" class="form-select" required>
                                <option value="Acak" {{ old('metode_pengambilan_sampel') === 'Acak' ? 'selected' : '' }}>Acak (Random)</option>
                                <option value="Selektif" {{ old('metode_pengambilan_sampel') === 'Selektif' ? 'selected' : '' }}>Selektif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jumlah Kematian (ekor)</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_kematian" min="0"
                                    class="form-control" value="{{ old('jumlah_kematian', 0) }}">
                                <span class="input-group-text">ekor</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gejala Klinis</label>
                            <textarea name="gejala_klinis" class="form-control" rows="3"
                                placeholder="Deskripsikan gejala yang terlihat pada ikan (warna, lesi, perilaku, dll.)">{{ old('gejala_klinis') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 5: Koordinat GPS --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
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
                            <input type="text" name="latitude" id="lat" class="form-control"
                                value="{{ old('latitude') }}" placeholder="-6.1234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="lng" class="form-control"
                                value="{{ old('longitude') }}" placeholder="106.8456789">
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
