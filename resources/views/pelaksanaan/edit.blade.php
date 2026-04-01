@extends('layouts.app')

@section('title', 'Edit Pelaksanaan')

@section('breadcrumb')
<ol class="breadcrumb" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Perencanaan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('perencanaan.show', $item->perencanaan_id) }}">Detail Perencanaan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pelaksanaan.show', $item->id) }}">Pelaksanaan #{{ $item->id }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-12">

        {{-- Header --}}
        <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-warning border-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-4">
                    <div class="bg-warning text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                        <i class="ti ti-edit fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning-lt text-warning px-3 fs-6 rounded-pill">EDIT PELAKSANAAN</span>
                        </div>
                        <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">Edit Data Pelaksanaan</h1>
                        <div class="text-muted fs-3">
                            <i class="ti ti-map-pin me-1"></i>{{ $item->lokasi_pengambilan_sampel }}
                            &nbsp;·&nbsp;
                            <i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-white btn-pill px-4 border shadow-sm">
                    <i class="ti ti-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('pelaksanaan.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Referensi Rencana --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center bg-yellow-lt p-3">
                        <div class="bg-warning text-white p-2 rounded-3 me-3">
                            <i class="ti ti-clipboard-list fs-2"></i>
                        </div>
                        <div>
                            <div class="text-warning small fw-bold text-uppercase">Referensi Rencana Pemantauan</div>
                            <div class="fw-bold">ID: #PLN-{{ str_pad($item->perencanaan_id, 5, '0', STR_PAD_LEFT) }}
                                — {{ $item->perencanaan?->kab_kota }}, {{ $item->perencanaan?->provinsi }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Lokasi & Tanggal --}}
                <div class="col-12">
                    <div class="card card-premium border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-map-2 me-2 text-primary"></i> PENGAMBILAN & KOORDINAT
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-sm btn-success btn-pill shadow-sm" onclick="getLocation()">
                                    <i class="ti ti-current-location me-1"></i>Perbarui GPS
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div id="geo-status" class="mb-3" style="display:none;"></div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label required fw-bold mb-2">Lokasi Pengambilan Sampel</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="lokasi_pengambilan_sampel"
                                            class="form-control @error('lokasi_pengambilan_sampel') is-invalid @enderror"
                                            value="{{ old('lokasi_pengambilan_sampel', $item->lokasi_pengambilan_sampel) }}"
                                            placeholder="Lokasi pengambilan sampel..." required>
                                    </div>
                                    @error('lokasi_pengambilan_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Tanggal Pemantauan</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                                        <input type="date" name="tanggal_pemantauan"
                                            class="form-control @error('tanggal_pemantauan') is-invalid @enderror"
                                            value="{{ old('tanggal_pemantauan', \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('Y-m-d')) }}" required>
                                    </div>
                                    @error('tanggal_pemantauan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Metode Sampling</label>
                                    <select name="metode_pengambilan_sampel" class="form-select" required>
                                        <option value="Acak" {{ old('metode_pengambilan_sampel', $item->metode_pengambilan_sampel) === 'Acak' ? 'selected' : '' }}>Acak (Random)</option>
                                        <option value="Selektif" {{ old('metode_pengambilan_sampel', $item->metode_pengambilan_sampel) === 'Selektif' ? 'selected' : '' }}>Selektif</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">LATITUDE</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="latitude" id="lat" class="form-control font-monospace"
                                            value="{{ old('latitude', $item->latitude) }}" placeholder="-6.123456">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">LONGITUDE</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="longitude" id="lng" class="form-control font-monospace"
                                            value="{{ old('longitude', $item->longitude) }}" placeholder="106.845678">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Sampel --}}
                <div class="col-12">
                    <div class="card card-premium border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-fish me-2 text-azure"></i> DATA IKAN & SAMPEL
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Jenis Ikan / Komoditas</label>
                                    <input type="text" name="jenis_ikan"
                                        class="form-control @error('jenis_ikan') is-invalid @enderror"
                                        value="{{ old('jenis_ikan', $item->jenis_ikan) }}" required>
                                    @error('jenis_ikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Nama Latin</label>
                                    <input type="text" name="nama_latin" class="form-control fst-italic"
                                        value="{{ old('nama_latin', $item->nama_latin) }}" placeholder="Nama ilmiah...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required fw-bold mb-2">Jumlah Sampel</label>
                                    <div class="input-group">
                                        <input type="number" name="jumlah_sampel" class="form-control"
                                            value="{{ old('jumlah_sampel', $item->jumlah_sampel) }}" min="1" required>
                                        <span class="input-group-text">Ekor</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Jumlah Kematian</label>
                                    <div class="input-group">
                                        <input type="number" name="jumlah_kematian" class="form-control"
                                            value="{{ old('jumlah_kematian', $item->jumlah_kematian) }}" min="0">
                                        <span class="input-group-text">Ekor</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Panjang (cm)</label>
                                    <input type="number" step="0.1" name="panjang_cm" class="form-control"
                                        value="{{ old('panjang_cm', $item->panjang_cm) }}" placeholder="0.0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Berat (gram)</label>
                                    <input type="number" step="0.1" name="berat_gram" class="form-control"
                                        value="{{ old('berat_gram', $item->berat_gram) }}" placeholder="0.0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Asal Benih/Induk</label>
                                    <input type="text" name="asal_benih_induk" class="form-control"
                                        value="{{ old('asal_benih_induk', $item->asal_benih_induk) }}" placeholder="Dari mana benih/induk berasal...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Padat Tebar (ekor/m²)</label>
                                    <input type="number" name="padat_tebar" class="form-control"
                                        value="{{ old('padat_tebar', $item->padat_tebar) }}" placeholder="0">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold mb-2">Gejala Klinis</label>
                                    <textarea name="gejala_klinis" class="form-control" rows="3"
                                        placeholder="Deskripsikan gejala klinis yang ditemukan di lapangan...">{{ old('gejala_klinis', $item->gejala_klinis) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Petugas --}}
                <div class="col-12">
                    <div class="card card-premium border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-users me-2 text-indigo"></i> PENGAMBIL SAMPEL
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div id="petugas-list" class="mb-3">
                                @php $oldPetugas = old('pengambil_sampel', $item->pengambil_sampel ?? ['']); @endphp
                                @foreach((array)$oldPetugas as $idx => $val)
                                <div class="petugas-row animate-fade-in mb-2">
                                    <div class="input-group input-group-flat shadow-sm border rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-0 text-muted small pe-1 row-num">{{ $idx + 1 }}.</span>
                                        <input type="text" name="pengambil_sampel[]" class="form-control border-0 ps-1"
                                            placeholder="Nama Lengkap Petugas" value="{{ $val }}">
                                        @if($idx > 0)
                                        <button type="button" class="btn btn-link link-danger p-0 px-3 border-0 bg-transparent" onclick="this.closest('.petugas-row').remove(); renumberPetugas();">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                        @else
                                        <span class="px-4"></span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-indigo btn-sm border-dashed" onclick="addPetugas()">
                                <i class="ti ti-plus me-1"></i> Tambah Petugas
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
                <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-outline-secondary btn-pill px-5">
                    <i class="ti ti-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-warning btn-pill px-5 shadow-lg fw-bold">
                    <i class="ti ti-device-floppy me-2"></i>SIMPAN PERUBAHAN
                </button>
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
        statusEl.innerHTML = '<div class="alert alert-danger px-3 py-2 small">Browser tidak mendukung GPS.</div>';
        return;
    }
    statusEl.innerHTML = '<div class="alert alert-info px-3 py-2 small"><div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2"></div>Menarik koordinat GPS...</div></div>';
    navigator.geolocation.getCurrentPosition(
        function(p) {
            document.getElementById("lat").value = p.coords.latitude.toFixed(6);
            document.getElementById("lng").value = p.coords.longitude.toFixed(6);
            statusEl.innerHTML = '<div class="alert alert-success px-3 py-2 small">✅ GPS Berhasil Ditarik!</div>';
            setTimeout(() => statusEl.style.display = 'none', 3000);
        },
        function(e) {
            statusEl.innerHTML = '<div class="alert alert-warning px-3 py-2 small">⚠️ Gagal mengambil GPS.</div>';
        }, { enableHighAccuracy: true, timeout: 10000 }
    );
}

function addPetugas() {
    var list = document.getElementById('petugas-list');
    var rows = list.querySelectorAll('.petugas-row');
    var num  = rows.length + 1;
    var div  = document.createElement('div');
    div.className = 'petugas-row animate-fade-in mb-2';
    div.innerHTML = `
        <div class="input-group input-group-flat shadow-none border-bottom">
            <span class="input-group-text bg-transparent border-0 text-muted small pe-1 row-num">${num}.</span>
            <input type="text" name="pengambil_sampel[]" class="form-control border-0 bg-transparent ps-1" placeholder="Nama Lengkap Petugas">
            <button type="button" class="btn btn-link link-danger p-0 px-2 border-0 bg-transparent" onclick="this.closest('.petugas-row').remove(); renumberPetugas();">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `;
    list.appendChild(div);
}

function renumberPetugas() {
    document.querySelectorAll('#petugas-list .petugas-row').forEach((row, idx) => {
        row.querySelector('.row-num').innerText = (idx + 1) + '.';
    });
}
</script>
@endsection
