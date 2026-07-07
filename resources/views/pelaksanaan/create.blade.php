@extends('layouts.app')

@section('title', 'Input Pelaksanaan')
@section('page_title', 'Input Pelaksanaan Lapangan')
@section('page_subtitle', $rencana->jenis_mp . ' — ' . $rencana->kab_kota . ', ' . $rencana->provinsi)

@section('page_actions')
<a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i>Kembali
</a>
@endsection

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-12">

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
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Media Pembawa</div>
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
                {{-- Bagian 1: Lokasi, Waktu & GPS --}}
                <div class="col-12">
                    <div class="card card-premium mb-0 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-map-2 me-2 text-primary"></i> PENGAMBILAN & KOORDINAT
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-sm btn-success btn-pill shadow-sm" onclick="getLocation()">
                                    <i class="ti ti-current-location me-1"></i>Ambil GPS Otomatis
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
                                            class="form-control rounded-3 border-light-dark shadow-sm @error('lokasi_pengambilan_sampel') is-invalid @enderror"
                                            value="{{ old('lokasi_pengambilan_sampel') }}"
                                            placeholder="Prov/Kab/Kec/Desa/Kontak Pemilik..." required>
                                    </div>
                                    <div class="form-hint mt-2">Contoh: Kec. Alue Naga, Kab. Banda Aceh, Prov. Aceh</div>
                                    @error('lokasi_pengambilan_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Tanggal Pemantauan</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                                        <input type="date" name="tanggal_pemantauan"
                                            class="form-control rounded-3 border-light-dark shadow-sm @error('tanggal_pemantauan') is-invalid @enderror"
                                            value="{{ old('tanggal_pemantauan', date('Y-m-d')) }}" required>
                                    </div>
                                    @error('tanggal_pemantauan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Metode Sampling</label>
                                    <select name="metode_pengambilan_sampel" class="form-select rounded-3 border-light-dark shadow-sm" required>
                                        <option value="Acak" {{ old('metode_pengambilan_sampel') === 'Acak' ? 'selected' : '' }}>Acak (Random)</option>
                                        <option value="Selektif" {{ old('metode_pengambilan_sampel') === 'Selektif' ? 'selected' : '' }}>Selektif</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">LATITUDE</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="latitude" id="lat" class="form-control rounded-3 border-light-dark shadow-sm font-monospace"
                                            value="{{ old('latitude') }}" placeholder="-6.123456">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">LONGITUDE</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="longitude" id="lng" class="form-control rounded-3 border-light-dark shadow-sm font-monospace"
                                            value="{{ old('longitude') }}" placeholder="106.845678">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Data Petugas & Jumlah Sampel --}}
                <div class="col-12">
                    <div class="card card-premium mb-0 border-0 shadow-sm bg-white">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-users me-2 text-indigo"></i> INFORMASI PENGAMBIL SAMPEL & JUMLAH
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <h3 class="card-title fw-bold text-azure mb-3">
                                        <i class="ti ti-list-numbers me-1"></i> JUMLAH SAMPEL
                                    </h3>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-lg">
                                                <input type="number" name="jumlah_sampel"
                                                    class="form-control fw-bold border-light-dark bg-white shadow-sm rounded-start rounded-3 pt-3 pb-3" style="font-size: 1.5rem;"
                                                    value="{{ old('jumlah_sampel', $rencana->rencana_jumlah_sampel ?? 1) }}" required min="1">
                                                <span class="input-group-text border-light-dark bg-azure-lt text-azure shadow-sm rounded-end rounded-3 px-4 fw-bold">SAMPEL</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="jenis_ikan" value="{{ $rencana->jenis_mp }}">
                                </div>

                                <div class="col-md-12 border-top pt-4">
                                    <h3 class="card-title fw-bold text-indigo mb-3">
                                        <i class="ti ti-users me-2"></i> NAMA PENGAMBIL SAMPEL LAPANGAN
                                    </h3>
                                    <div id="petugas-list" class="mb-3">
                                        @php $oldPetugas = old('pengambil_sampel', ['']); @endphp
                                        @foreach($oldPetugas as $idx => $val)
                                        <div class="petugas-row animate-fade-in mb-2">
                                            <div class="input-group input-group-flat shadow-sm border rounded-3 overflow-hidden">
                                                <span class="input-group-text bg-light border-0 text-muted small pe-1">{{ $idx + 1 }}.</span>
                                                <input type="text" name="pengambil_sampel[]"
                                                    class="form-control border-0 ps-1"
                                                    placeholder="Nama Lengkap Petugas"
                                                    value="{{ $val }}">
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
                                        <i class="ti ti-plus me-1"></i> Tambah Petugas Lagi
                                    </button>
                                </div>
                            </div>
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
