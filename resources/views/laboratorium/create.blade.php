@extends('layouts.app')

@section('title', 'Input Hasil Laboratorium')
@section('page_title', 'Hasil Pemeriksaan Laboratorium')
@section('page_subtitle', 'Input data pengujian untuk sampel #' . str_pad($pelaksanaan->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-12">
        {{-- High-End Page Header --}}
        <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-purple border-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-4">
                    <div class="bg-purple text-white p-3 rounded-3 shadow-sm">
                        <i class="ti ti-flask-filled fs-1"></i>
                    </div>
                    <div>
                        <div class="text-purple small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Input Hasil Laboratorium</div>
                        <h1 class="mb-0 fw-bold text-dark">Pemeriksaan Sampel #{{ str_pad($pelaksanaan->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('pelaksanaan.show', $pelaksanaan->id) }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2"></i> Kembali ke Detail Pelaksanaan
                </a>
            </div>
        </div>

        {{-- Info Sampel Lapangan --}}
        <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden bg-light">
            <div class="card-body p-0">
                <div class="d-flex align-items-center bg-indigo-lt p-3">
                    <div class="bg-indigo text-white p-2 rounded-3 me-3 shadow-sm">
                        <i class="ti ti-microscope fs-2"></i>
                    </div>
                    <div>
                        <div class="text-indigo small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Metadata Sampel Lapangan</div>
                        <div class="fw-bold fs-3">{{ $pelaksanaan->lokasi_pengambilan_sampel }}</div>
                    </div>
                </div>
                <div class="row g-0 border-top bg-white">
                    <div class="col-6 col-md-3 border-end p-3">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Wilayah</div>
                        <div class="fw-bold">{{ $pelaksanaan->perencanaan->kab_kota ?? '-' }}, {{ $pelaksanaan->perencanaan->provinsi ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3 border-end p-3">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Komoditas</div>
                        <div class="fw-bold">{{ $pelaksanaan->jenis_ikan ?? $pelaksanaan->perencanaan->jenis_mp ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3 border-end p-3">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Target HPIK</div>
                        <div class="fw-bold text-primary">{{ $pelaksanaan->perencanaan->jenis_hpik ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3 p-3 bg-light">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Jumlah Sampel</div>
                        <div class="fw-bold fs-4">{{ $pelaksanaan->jumlah_sampel }} <small class="fw-normal">Pelaksanaan</small></div>
                    </div>
                </div>
                @if($pelaksanaan->gejala_klinis)
                <div class="p-3 bg-white border-top small">
                    <span class="text-muted fw-bold me-2">CATATAN LAPANGAN:</span>
                    <span class="fst-italic">"{{ $pelaksanaan->gejala_klinis }}"</span>
                </div>
                @endif
            </div>
        </div>

        <form action="{{ route('laboratorium.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pelaksanaan_id" value="{{ $pelaksanaan->id }}">

            <div class="row g-4">
                {{-- Identitas Pengujian --}}
                <div class="col-12">
                    <div class="card card-premium mb-0 border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-primary">
                                <i class="ti ti-flask me-2"></i> IDENTITAS PENGUJIAN
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Kode Sampel Lab</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-barcode"></i></span>
                                        <input type="text" name="kode_sampel"
                                            class="form-control @error('kode_sampel') is-invalid @enderror fw-bold"
                                            value="{{ old('kode_sampel', 'LAB-' . date('Y') . '-') }}" required>
                                    </div>
                                    @error('kode_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Laboratorium Penguji</label>
                                    <input type="text" name="lab_penguji"
                                        class="form-control"
                                        value="{{ old('lab_penguji', $pelaksanaan->perencanaan->lab_uji ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Nama Petugas Penguji</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-user-check"></i></span>
                                        <input type="text" name="nama_petugas_uji"
                                            class="form-control @error('nama_petugas_uji') is-invalid @enderror"
                                            placeholder="Nama lengkap analis/petugas uji"
                                            value="{{ old('nama_petugas_uji') }}" required>
                                    </div>
                                    @error('nama_petugas_uji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Tanggal Pengujian</label>
                                    <input type="date" name="tanggal_uji"
                                        class="form-control"
                                        value="{{ old('tanggal_uji', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label required fw-bold mb-2">Jenis Target di Uji</label>
                                    <textarea name="jenis_hpik_diuji"
                                        class="form-control" rows="2"
                                        required>{{ old('jenis_hpik_diuji', $pelaksanaan->perencanaan->jenis_hpik ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required fw-bold mb-2">Metode Uji Utama</label>
                                    <select name="metode_uji" class="form-select select-pill" required>
                                        <option value="">— Pilih Metode Utama —</option>
                                        <option value="PCR" {{ old('metode_uji') === 'PCR' ? 'selected' : '' }}>PCR</option>
                                        <option value="RT-PCR" {{ old('metode_uji') === 'RT-PCR' ? 'selected' : '' }}>RT-PCR</option>
                                        <option value="Real-Time PCR (qPCR)" {{ old('metode_uji') === 'Real-Time PCR (qPCR)' ? 'selected' : '' }}>Real-Time PCR (qPCR)</option>
                                        <option value="Sekuensing DNA" {{ old('metode_uji') === 'Sekuensing DNA' ? 'selected' : '' }}>Sekuensing DNA</option>
                                        <option value="Isolasi Bakteri" {{ old('metode_uji') === 'Isolasi Bakteri' ? 'selected' : '' }}>Isolasi Bakteri</option>
                                        <option value="Uji Biokimia" {{ old('metode_uji') === 'Uji Biokimia' ? 'selected' : '' }}>Uji Biokimia</option>
                                        <option value="Uji Sensitivitas/Antibiogram" {{ old('metode_uji') === 'Uji Sensitivitas/Antibiogram' ? 'selected' : '' }}>Uji Sensitivitas/Antibiogram</option>
                                        <option value="Natif/Scrapping" {{ old('metode_uji') === 'Natif/Scrapping' ? 'selected' : '' }}>Natif/Scrapping</option>
                                        <option value="Sediaan Ulas (Smear)" {{ old('metode_uji') === 'Sediaan Ulas (Smear)' ? 'selected' : '' }}>Sediaan Ulas (Smear)</option>
                                        <option value="Kultur Jamur" {{ old('metode_uji') === 'Kultur Jamur' ? 'selected' : '' }}>Kultur Jamur</option>
                                        <option value="Pemeriksaan Mikroskopis Struktur Jamur" {{ old('metode_uji') === 'Pemeriksaan Mikroskopis Struktur Jamur' ? 'selected' : '' }}>Pemeriksaan Mikroskopis Struktur Jamur</option>
                                        <option value="Pemeriksaan Jaringan (Slide)" {{ old('metode_uji') === 'Pemeriksaan Jaringan (Slide)' ? 'selected' : '' }}>Pemeriksaan Jaringan (Slide)</option>
                                        <option value="Isolasi Virus" {{ old('metode_uji') === 'Isolasi Virus' ? 'selected' : '' }}>Isolasi Virus</option>
                                        <option value="ELISA" {{ old('metode_uji') === 'ELISA' ? 'selected' : '' }}>ELISA</option>
                                        <option value="IFAT" {{ old('metode_uji') === 'IFAT' ? 'selected' : '' }}>IFAT</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contoh Uji --}}
                <div class="col-12 mt-4">
                    <div class="card card-premium mb-0 border-0 shadow-sm border-top border-purple border-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-purple">
                                <i class="ti ti-ruler-measure me-2"></i> DATA CONTOH UJI
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Panjang (cm)</label>
                                    <input type="text" name="panjang" class="form-control bg-light" placeholder="e.g., 5" value="{{ old('panjang') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Berat (gram)</label>
                                    <input type="text" name="berat" class="form-control bg-light" placeholder="e.g., 6" value="{{ old('berat') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Jumlah Kematian</label>
                                    <input type="text" name="jumlah_kematian" class="form-control bg-light" placeholder="e.g., 10" value="{{ old('jumlah_kematian') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Padat Tebar</label>
                                    <input type="text" name="padat_tebar" class="form-control bg-light" placeholder="e.g., 500" value="{{ old('padat_tebar') }}">
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Asal Benih / Induk</label>
                                    <input type="text" name="asal_benih_induk" class="form-control bg-light" placeholder="..." value="{{ old('asal_benih_induk') }}">
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Gejala Klinis</label>
                                    <textarea name="gejala_klinis" class="form-control bg-light" rows="2" placeholder="Deskripsikan gejala klinis jika ada...">{{ old('gejala_klinis') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hasil & Kelompok Patogen --}}
                <div class="col-12">
                    <div class="card card-premium mb-0 border-0 shadow-sm h-100 border-top border-azure border-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h3 class="card-title fw-bold text-azure">
                                <i class="ti ti-microscope me-2"></i> HASIL PEMERIKSAAN KELOMPOK PATOGEN
                            </h3>
                        </div>
                        <div class="card-body">

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold required">Kelompok Patogen Ditemukan</label>
                                    <select name="kelompok_patogen" class="form-select @error('kelompok_patogen') is-invalid @enderror" required>
                                        <option value="">— Pilih Kelompok Patogen —</option>
                                        <option value="Parasit" {{ old('kelompok_patogen') == 'Parasit' ? 'selected' : '' }}>Parasit</option>
                                        <option value="Bakteri" {{ old('kelompok_patogen') == 'Bakteri' ? 'selected' : '' }}>Bakteri</option>
                                        <option value="Virus" {{ old('kelompok_patogen') == 'Virus' ? 'selected' : '' }}>Virus</option>
                                        <option value="Jamur" {{ old('kelompok_patogen') == 'Jamur' ? 'selected' : '' }}>Jamur</option>
                                        <option value="Nihil" {{ old('kelompok_patogen') == 'Nihil' ? 'selected' : '' }}>Nihil / Tidak Ada Patogen</option>
                                    </select>
                                    @error('kelompok_patogen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <label class="form-label required fw-bold mb-2 text-dark">NAMA PENYAKIT / HASIL AKHIR</label>
                                <select name="hasil_uji" class="form-select form-select-lg fw-bold shadow-sm" style="border-radius: 0.5rem;" required>
                                    <option value="">— Pilih Hasil Akhir —</option>
                                    <option value="NIHIL" {{ old('hasil_uji') === 'NIHIL' ? 'selected' : '' }} class="text-success fw-bold">✅ NIHIL</option>
                                    <optgroup label="Daftar Penyakit (HPIK)">
                                        @foreach($jenis_penyakits ?? [] as $penyakit)
                                            <option value="{{ collect(explode(' - ', $penyakit->nama))->first() }}" {{ old('hasil_uji') === collect(explode(' - ', $penyakit->nama))->first() ? 'selected' : '' }}>
                                                {{ $penyakit->nama }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Baris Bawah: Kalkulator --}}
                <div class="col-12">
                    <div class="card card-premium mb-0 border-0 shadow-sm border-top border-warning border-4">
                        <div class="card-header bg-warning-lt py-3">
                            <h3 class="card-title fw-bold text-orange mb-0">
                                <i class="ti ti-calculator me-2"></i> KALKULATOR PREVALENSI & INSIDENSI
                                <span class="badge bg-white text-orange ms-2 px-2 border border-warning">PEDOMAN HPIK</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-7">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded-4 border shadow-sm">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Jumlah Ikan INFEKSI</label>
                                                <div class="input-group">
                                                    <input type="number" id="jml_terinfeksi" name="jumlah_ikan_terinfeksi"
                                                        class="form-control fw-bold border-light bg-light text-primary py-2" style="font-size: 1.5rem;" min="0"
                                                        value="{{ old('jumlah_ikan_terinfeksi') }}"
                                                        placeholder="0" oninput="hitungOtomatis()">
                                                    <span class="input-group-text bg-primary-lt border-light text-primary fw-bold text-uppercase px-3" style="letter-spacing: 0.05em; font-size: 0.8rem;">Sampel</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded-4 border shadow-sm">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Sampel DIPERIKSA</label>
                                                <div class="input-group">
                                                    <input type="number" id="jml_diperiksa" name="jumlah_sampel_diperiksa"
                                                        class="form-control fw-bold border-light bg-light text-primary py-2" style="font-size: 1.5rem;" min="1"
                                                        value="{{ old('jumlah_sampel_diperiksa', $pelaksanaan->jumlah_sampel) }}"
                                                        oninput="hitungOtomatis()">
                                                    <span class="input-group-text bg-primary-lt border-light text-primary fw-bold text-uppercase px-3" style="letter-spacing: 0.05em; font-size: 0.8rem;">Sampel</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">JUMLAH KOLAM</label>
                                            <input type="number" id="jml_kolam" name="jumlah_kolam_uji" class="form-control form-control-sm" oninput="hitungOtomatis()">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">PERIODE PENGAMATAN</label>
                                            <input type="number" id="periode" name="periode_pengamatan" class="form-control form-control-sm" oninput="hitungOtomatis()">
                                        </div>
                                        <div class="col-md-6">
                                            <div id="kalkulasi-status" class="h-100 d-flex align-items-end">
                                                <div class="badge bg-blue-lt text-blue p-2 w-100 text-start">
                                                   <i class="ti ti-info-circle me-1"></i> Masukkan data untuk hitung otomatis
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="p-4 bg-azure text-white rounded-4 shadow-sm position-relative overflow-hidden">
                                                <div class="small fw-bold text-uppercase opacity-75 mb-1">PREVALENSI (%)</div>
                                                <div class="h1 mb-0 fw-bold" id="display_prevalensi">0.00%</div>
                                                <input type="hidden" name="prevalensi" id="hasil_prevalensi" value="{{ old('prevalensi') }}">
                                                <i class="ti ti-percentage position-absolute bottom-0 end-0 opacity-10" style="font-size: 5rem; margin-right: -1rem; margin-bottom: -1rem;"></i>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 bg-white border border-orange border-2 border-dashed rounded-4">
                                                <div class="small fw-bold text-orange text-uppercase mb-1">INSIDENSI</div>
                                                <div class="h2 mb-0 fw-bold text-orange" id="display_insidensi">0.000000</div>
                                                <input type="hidden" name="insidensi" id="hasil_insidensi" value="{{ old('insidensi') }}">
                                                <div class="small text-muted" style="font-size: 0.65rem;">IKAN / KOLAM / HARI</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Diagnosis --}}
                <div class="col-12">
                    <div class="card card-premium mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold mb-2">DIAGNOSIS AKHIR / KETERANGAN</label>
                                    <textarea name="diagnosis_akhir" class="form-control" rows="3"
                                        placeholder="Catatan tambahan hasil pengujian atau deviasi prosedur jika ada...">{{ old('diagnosis_akhir') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold mb-2">TANGGAL HASIL KELUAR</label>
                                    <input type="date" name="tanggal_hasil" class="form-control" value="{{ old('tanggal_hasil', date('Y-m-d')) }}">
                                    
                                    <div class="mt-4 pt-4 border-top d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-pill px-4 shadow-sm">
                                            <i class="ti ti-device-floppy me-2"></i>Simpan Hasil Lab
                                        </button>
                                        <a href="{{ route('laboratorium.index') }}" class="btn btn-link link-secondary">Batal</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function hitungOtomatis() {
    var terinfeksi = parseFloat(document.getElementById('jml_terinfeksi').value);
    var diperiksa  = parseFloat(document.getElementById('jml_diperiksa').value);
    var kolam      = parseFloat(document.getElementById('jml_kolam').value);
    var periode    = parseFloat(document.getElementById('periode').value);

    var statusEl = document.getElementById('kalkulasi-status');
    var displayPrev = document.getElementById('display_prevalensi');
    var inputPrev   = document.getElementById('hasil_prevalensi');
    var displayIns  = document.getElementById('display_insidensi');
    var inputIns    = document.getElementById('hasil_insidensi');

    var msgs = [];

    // ── PREVALENSI ──
    if (!isNaN(terinfeksi) && !isNaN(diperiksa) && diperiksa > 0) {
        var prev = (terinfeksi / diperiksa) * 100;
        displayPrev.textContent = prev.toFixed(2) + '%';
        inputPrev.value = prev.toFixed(2);
        msgs.push('✅ Prevalensi terhitung');
    } else {
        displayPrev.textContent = '0.00%';
        inputPrev.value = '';
    }

    // ── INSIDENSI ──
    if (!isNaN(terinfeksi) && !isNaN(kolam) && !isNaN(periode) && kolam > 0 && periode > 0) {
        var ins = terinfeksi / (kolam * periode);
        displayIns.textContent = ins.toFixed(6);
        inputIns.value = ins.toFixed(6);
        msgs.push('✅ Insidensi terhitung');
    } else {
        displayIns.textContent = '0.000000';
        inputIns.value = '';
    }

    // Update status
    if (msgs.length > 0) {
        statusEl.innerHTML = '<div class="badge bg-green-lt text-success p-2 w-100 text-start animate-fade-in">' + msgs.join(' | ') + '</div>';
    } else {
         statusEl.innerHTML = '<div class="badge bg-blue-lt text-blue p-2 w-100 text-start small"><i class="ti ti-info-circle me-1"></i> Masukkan data untuk hitung otomatis</div>';
    }
}

document.addEventListener('DOMContentLoaded', hitungOtomatis);
</script>
@endsection
