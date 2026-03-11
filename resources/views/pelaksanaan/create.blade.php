@extends('layouts.app')

@section('title', 'Input Pelaksanaan')
@section('page_title', 'Input Pelaksanaan Lapangan')
@section('page_subtitle', $rencana->jenis_mp . ' — ' . $rencana->kab_kota . ', ' . $rencana->provinsi)

@section('content')
<div class="row justify-content-center animate-fade-in">
    <div class="col-lg-10">
        <form action="{{ route('pelaksanaan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="perencanaan_id" value="{{ $rencana->id }}">

            {{-- Referensi Rencana (Info Board) --}}
            <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center bg-blue-lt p-3">
                        <div class="bg-blue text-white p-2 rounded-3 me-3">
                            <i class="ti ti-clipboard-list fs-2"></i>
                        </div>
                        <div>
                            <div class="text-blue small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Referensi Rencana Pemantauan</div>
                            <div class="fw-bold">ID: #PLN-{{ str_pad($rencana->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                    <div class="row g-0 border-top bg-white">
                        <div class="col-sm-3 border-end p-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Wilayah</div>
                            <div class="fw-bold fs-4">{{ $rencana->kab_kota }}, {{ $rencana->provinsi }}</div>
                        </div>
                        <div class="col-sm-3 border-end p-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Target HPIK</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(explode(',', $rencana->jenis_hpik) as $hpik)
                                    <span class="badge bg-primary-lt text-primary px-2 py-0 fs-5">{{ trim($hpik) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-sm-3 border-end p-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Komoditas</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(explode(',', $rencana->jenis_mp) as $mp)
                                    <span class="badge bg-blue-lt text-blue px-2 py-0 fs-5">{{ trim($mp) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-sm-3 p-3 bg-light">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Target Sampling</div>
                            <div class="fw-bold fs-3 text-azure">{{ $rencana->target_uji }} <small class="fw-normal">Pelaksanaan</small></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Kiri: Data Lokasi & Waktu --}}
                <div class="col-md-7">
                    <div class="card card-premium h-100 mb-0 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-primary">
                                <i class="ti ti-map-2 me-2"></i> LOKASI & WAKTU
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label required fw-bold mb-2">Lokasi Pengambilan Sampel</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                    <input type="text" name="lokasi_pengambilan_sampel"
                                        class="form-control @error('lokasi_pengambilan_sampel') is-invalid @enderror"
                                        value="{{ old('lokasi_pengambilan_sampel') }}"
                                        placeholder="Prov/Kab/Kec/Desa/Kontak Pemilik..." required>
                                </div>
                                <div class="form-hint mt-2">Contoh: Kec. Alue Naga, Kab. Banda Aceh, Prov. Aceh</div>
                                @error('lokasi_pengambilan_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Tanggal Pemantauan</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                                        <input type="date" name="tanggal_pemantauan"
                                            class="form-control @error('tanggal_pemantauan') is-invalid @enderror"
                                            value="{{ old('tanggal_pemantauan', date('Y-m-d')) }}" required>
                                    </div>
                                    @error('tanggal_pemantauan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Metode Sampling</label>
                                    <select name="metode_pengambilan_sampel" class="form-select" required>
                                        <option value="Acak" {{ old('metode_pengambilan_sampel') === 'Acak' ? 'selected' : '' }}>Acak (Random)</option>
                                        <option value="Selektif" {{ old('metode_pengambilan_sampel') === 'Selektif' ? 'selected' : '' }}>Selektif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: GPS & Navigasi --}}
                <div class="col-md-5">
                    <div class="card card-premium h-100 mb-0 border-0 shadow-sm bg-light">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-success">
                                <i class="ti ti-current-location me-2"></i> KOORDINAT GPS
                            </h3>
                            <div class="card-options">
                                <button type="button" class="btn btn-sm btn-success btn-pill shadow-sm" onclick="getLocation()">
                                    <i class="ti ti-current-location me-1"></i>Ambil GPS
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="geo-status" class="mb-3" style="display:none;"></div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">LATITUDE</label>
                                    <input type="text" name="latitude" id="lat" class="form-control bg-white font-monospace"
                                        value="{{ old('latitude') }}" placeholder="-6.123456">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">LONGITUDE</label>
                                    <input type="text" name="longitude" id="lng" class="form-control bg-white font-monospace"
                                        value="{{ old('longitude') }}" placeholder="106.845678">
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-white-50 rounded-3 border-dashed border-2">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="ti ti-info-circle me-2 fs-3"></i>
                                    GPS digunakan untuk pemetaan sebaran penyakit secara otomatis di dashboard.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Baris 2 Kiri: Data Ikan --}}
                <div class="col-md-8">
                    <div class="card card-premium mb-0 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-azure">
                                <i class="ti ti-fish me-2"></i> DATA IKAN (CONTOH UJI)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <label class="form-label required fw-bold mb-2">Jenis Ikan (Nama Lokal)</label>
                                    <input type="text" name="jenis_ikan"
                                        class="form-control @error('jenis_ikan') is-invalid @enderror"
                                        value="{{ old('jenis_ikan', $rencana->jenis_mp) }}"
                                        placeholder="Contoh: Udang Vanname" required>
                                    @error('jenis_ikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold mb-2">Nama Latin (Ilmiah)</label>
                                    <input type="text" name="nama_latin"
                                        class="form-control italic"
                                        value="{{ old('nama_latin') }}"
                                        placeholder="Contoh: Litopenaeus vannamei">
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Rata-rata Panjang</label>
                                    <div class="input-group input-group-flat">
                                        <input type="number" name="panjang_cm" step="0.01" min="0"
                                            class="form-control px-2" value="{{ old('panjang_cm') }}" placeholder="0.0">
                                        <span class="input-group-text px-2 bg-light">cm</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Rata-rata Berat</label>
                                    <div class="input-group input-group-flat">
                                        <input type="number" name="berat_gram" step="0.01" min="0"
                                            class="form-control px-2" value="{{ old('berat_gram') }}" placeholder="0.0">
                                        <span class="input-group-text px-2 bg-light">gr</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Padat Tebar</label>
                                    <div class="input-group input-group-flat">
                                        <input type="number" name="padat_tebar" min="0"
                                            class="form-control px-2" value="{{ old('padat_tebar') }}" placeholder="0">
                                        <span class="input-group-text px-2 bg-light">m²</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-2">Asal Benih</label>
                                    <input type="text" name="asal_benih_induk"
                                        class="form-control" value="{{ old('asal_benih_induk') }}"
                                        placeholder="Hatchery...">
                                </div>

                                <div class="col-md-4">
                                    <div class="p-4 bg-azure-lt rounded-4 border-start border-azure border-4 h-100">
                                        <label class="form-label required fw-bold mb-2 text-azure">JUMLAH SAMPEL</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" name="jumlah_sampel"
                                                class="form-control fw-bold border-0 bg-transparent"
                                                value="{{ old('jumlah_sampel', $rencana->rencana_jumlah_sampel ?? 1) }}" required min="1">
                                            <span class="input-group-text border-0 bg-transparent text-azure small">PELAKSANAAN</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold mb-2">Gejala Klinis / Kondisi Lapangan</label>
                                    <textarea name="gejala_klinis" class="form-control" rows="3"
                                        placeholder="Deskripsikan gejala penyakit atau kondisi lingkungan yang mencolok...">{{ old('gejala_klinis') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Baris 2 Kanan: Pengambil Sampel --}}
                <div class="col-md-4">
                    <div class="card card-premium h-100 mb-0 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-indigo">
                                <i class="ti ti-users me-2"></i> PENGAMBIL SAMPEL
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="petugas-list" class="mb-3">
                                @php $oldPetugas = old('pengambil_sampel', ['']); @endphp
                                @foreach($oldPetugas as $idx => $val)
                                <div class="petugas-row animate-fade-in mb-2">
                                    <div class="input-group input-group-flat shadow-none border-bottom">
                                        <span class="input-group-text bg-transparent border-0 text-muted small pe-1">{{ $idx + 1 }}.</span>
                                        <input type="text" name="pengambil_sampel[]"
                                            class="form-control border-0 bg-transparent ps-1"
                                            placeholder="Nama Lengkap Petugas"
                                            value="{{ $val }}">
                                        @if($idx > 0)
                                        <button type="button" class="btn btn-link link-danger p-0 px-2 border-0 bg-transparent" onclick="this.closest('.petugas-row').remove(); renumberPetugas();">
                                            <i class="ti ti-x"></i>
                                        </button>
                                        @else
                                        <span class="px-3"></span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-indigo btn-sm w-100 border-dashed" onclick="addPetugas()">
                                <i class="ti ti-plus me-1"></i>Tambah Petugas
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
                <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-danger shadow-sm btn-pill px-5 py-3 fs-3 fw-bold hover-scale transition-all">
                    <i class="ti ti-arrow-left me-2"></i> Batal & Kembali
                </a>
                <button type="submit" class="btn btn-primary btn-pill px-5 py-3 fs-3 shadow-lg fw-extrabold hover-scale transition-all">
                    <i class="ti ti-device-floppy me-2"></i> SIMPAN PELAKSANAAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── GPS ───────────────────────────────────────────────────────────────────
function getLocation() {
    var statusEl = document.getElementById('geo-status');
    statusEl.style.display = 'block';
    if (!navigator.geolocation) {
        statusEl.innerHTML = '<div class="alert alert-danger px-3 py-2 small">Browser tidak mendukung GPS.</div>';
        return;
    }
    statusEl.innerHTML = '<div class="alert alert-info px-3 py-2 small"><div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2"></div>Menarik koordinat satelit...</div></div>';
    navigator.geolocation.getCurrentPosition(
        function(p) {
            document.getElementById("lat").value = p.coords.latitude.toFixed(6);
            document.getElementById("lng").value = p.coords.longitude.toFixed(6);
            statusEl.innerHTML = '<div class="alert alert-success px-3 py-2 small">✅ GPS Berhasil Ditarik!</div>';
            setTimeout(() => statusEl.style.display = 'none', 3000);
        },
        function(e) {
            var msg = 'Gagal mengambil GPS: ';
            if (e.code===1) msg += 'Izin ditolak.'; else if (e.code===2) msg += 'Tidak tersedia.'; else msg += 'Waktu habis.';
            statusEl.innerHTML = '<div class="alert alert-warning px-3 py-2 small">⚠️ '+msg+'</div>';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

// ── Petugas Management ────────────────────────────────────────────────────
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
