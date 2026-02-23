@extends('layouts.app')

@section('title', 'Input Hasil Laboratorium')
@section('page_title', 'Formulir Hasil Pemantauan HPIK')
@section('page_subtitle', 'Input hasil pemeriksaan laboratorium untuk sampel lapangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- ============================================================ --}}
        {{-- CARD 1: Info Sampel (readonly) --}}
        {{-- ============================================================ --}}
        <div class="card mb-3">
            <div class="card-header bg-blue-lt">
                <h3 class="card-title"><i class="ti ti-map-pin me-2"></i>Data Sampel Lapangan</h3>
            </div>
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-sm-3">
                        <div class="text-muted small">Lokasi Sampling</div>
                        <div class="fw-semibold">{{ $pelaksanaan->lokasi_pengambilan_sampel }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Wilayah</div>
                        <div class="fw-semibold">{{ $pelaksanaan->perencanaan->kab_kota ?? '-' }}, {{ $pelaksanaan->perencanaan->provinsi ?? '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Jenis Ikan</div>
                        <div class="fw-semibold">{{ $pelaksanaan->jenis_ikan ?? $pelaksanaan->perencanaan->jenis_mp ?? '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small">Target HPIK</div>
                        <div class="fw-semibold">{{ $pelaksanaan->perencanaan->jenis_hpik ?? '-' }}</div>
                    </div>
                    @if($pelaksanaan->gejala_klinis)
                    <div class="col-12">
                        <div class="text-muted small">Gejala Klinis</div>
                        <div class="small">{{ $pelaksanaan->gejala_klinis }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- FORM UTAMA --}}
        {{-- ============================================================ --}}
        <form action="{{ route('laboratorium.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pelaksanaan_id" value="{{ $pelaksanaan->id }}">

            {{-- ============================================================ --}}
            {{-- CARD 2: Identitas Pengujian --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-flask me-2"></i>Identitas Pengujian</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Kode Sampel</label>
                            <input type="text" name="kode_sampel"
                                class="form-control @error('kode_sampel') is-invalid @enderror"
                                value="{{ old('kode_sampel') }}"
                                placeholder="Contoh: LAB-2026-001" required>
                            @error('kode_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Laboratorium Penguji</label>
                            <input type="text" name="lab_penguji"
                                class="form-control @error('lab_penguji') is-invalid @enderror"
                                value="{{ old('lab_penguji', $pelaksanaan->perencanaan->lab_uji ?? '') }}" required>
                            @error('lab_penguji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Tanggal Pengujian</label>
                            <input type="date" name="tanggal_uji"
                                class="form-control @error('tanggal_uji') is-invalid @enderror"
                                value="{{ old('tanggal_uji', date('Y-m-d')) }}" required>
                            @error('tanggal_uji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis HPIK yang Diuji</label>
                            <input type="text" name="jenis_hpik_diuji"
                                class="form-control @error('jenis_hpik_diuji') is-invalid @enderror"
                                value="{{ old('jenis_hpik_diuji', $pelaksanaan->perencanaan->jenis_hpik ?? '') }}" required>
                            @error('jenis_hpik_diuji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Metode Uji</label>
                            <select name="metode_uji" class="form-select @error('metode_uji') is-invalid @enderror" required>
                                <option value="">— Pilih Metode —</option>
                                <option value="PCR Konvensional" {{ old('metode_uji') === 'PCR Konvensional' ? 'selected' : '' }}>PCR Konvensional</option>
                                <option value="Real Time PCR (qPCR)" {{ old('metode_uji') === 'Real Time PCR (qPCR)' ? 'selected' : '' }}>Real Time PCR (qPCR)</option>
                                <option value="ELISA" {{ old('metode_uji') === 'ELISA' ? 'selected' : '' }}>ELISA</option>
                                <option value="Kultur Bakteri" {{ old('metode_uji') === 'Kultur Bakteri' ? 'selected' : '' }}>Kultur Bakteri</option>
                                <option value="Histopatologi" {{ old('metode_uji') === 'Histopatologi' ? 'selected' : '' }}>Histopatologi</option>
                                <option value="Lainnya" {{ old('metode_uji') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('metode_uji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 3: Hasil Pemeriksaan Per Target Patogen (Kol 11-14) --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-virus me-2"></i>Hasil Pemeriksaan Per Target Patogen</h3>
                    <div class="card-options">
                        <span class="text-muted small">+ = Positif &nbsp;|&nbsp; - = Negatif &nbsp;|&nbsp; NT = Tidak Diuji</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach([
                            ['field' => 'hasil_parasit', 'label' => 'Parasit',  'icon' => 'ti-bug',      'col' => 11],
                            ['field' => 'hasil_bakteri', 'label' => 'Bakteri',  'icon' => 'ti-circle',   'col' => 12],
                            ['field' => 'hasil_virus',   'label' => 'Virus',    'icon' => 'ti-virus',    'col' => 13],
                            ['field' => 'hasil_jamur',   'label' => 'Jamur',    'icon' => 'ti-leaf',     'col' => 14],
                        ] as $target)
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="ti {{ $target['icon'] }} me-1"></i>
                                {{ $target['label'] }}
                                <span class="text-muted small">(Kol.{{ $target['col'] }})</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                @foreach(['+' => ['label'=>'Positif','color'=>'danger'], '-' => ['label'=>'Negatif','color'=>'success'], 'NT' => ['label'=>'NT','color'=>'secondary']] as $val => $opt)
                                <input type="radio" class="btn-check" name="{{ $target['field'] }}"
                                    id="{{ $target['field'] }}_{{ $val }}"
                                    value="{{ $val }}"
                                    {{ old($target['field'], 'NT') === $val ? 'checked' : '' }}>
                                <label class="btn btn-outline-{{ $opt['color'] }} btn-sm"
                                    for="{{ $target['field'] }}_{{ $val }}">{{ $val }}</label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Hasil Uji Keseluruhan --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">Hasil Uji Keseluruhan</label>
                            <select name="hasil_uji" class="form-select @error('hasil_uji') is-invalid @enderror" required>
                                <option value="">— Pilih Hasil Akhir —</option>
                                <option value="Negatif" {{ old('hasil_uji') === 'Negatif' ? 'selected' : '' }}>✅ Negatif (Bebas HPIK)</option>
                                <option value="Positif" {{ old('hasil_uji') === 'Positif' ? 'selected' : '' }}>🔴 Positif (Terdeteksi HPIK)</option>
                                <option value="Inkonklusif" {{ old('hasil_uji') === 'Inkonklusif' ? 'selected' : '' }}>⚠️ Inkonklusif</option>
                            </select>
                            @error('hasil_uji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD 3b: Kalkulator Prevalensi & Insidensi (Formula Pedoman) --}}
            {{-- ============================================================ --}}
            <div class="card mb-3 border-warning">
                <div class="card-header bg-warning-lt">
                    <h3 class="card-title">
                        <i class="ti ti-calculator me-2 text-warning"></i>
                        Kalkulator Prevalensi &amp; Insidensi
                        <span class="badge bg-warning-lt text-warning ms-2">Sesuai Pedoman HPIK</span>
                    </h3>
                </div>
                <div class="card-body">

                    {{-- Formula Box --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info p-2 mb-0">
                                <div class="text-center small fw-semibold mb-1">Formula Prevalensi (Pedoman)</div>
                                <div class="text-center">
                                    <span class="text-nowrap">
                                        Prevalensi =
                                        <span class="border-bottom border-dark px-2">Jumlah ikan terinfeksi</span>
                                        &divide;
                                        <span class="text-muted">Jumlah total ikan uji</span>
                                        &times; 100%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning p-2 mb-0">
                                <div class="text-center small fw-semibold mb-1">Formula Insidensi (Pedoman)</div>
                                <div class="text-center">
                                    <span class="text-nowrap">
                                        Insidensi =
                                        <span class="border-bottom border-dark px-2">Jumlah ikan terinfeksi</span>
                                        &divide;
                                        (<span class="text-muted">Kolam uji</span>
                                        &times;
                                        <span class="text-muted">Periode</span>)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Komponen --}}
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label required">
                                <i class="ti ti-fish me-1 text-danger"></i>Jumlah Ikan Terinfeksi
                            </label>
                            <div class="input-group">
                                <input type="number" id="jml_terinfeksi" name="jumlah_ikan_terinfeksi"
                                    class="form-control" min="0"
                                    value="{{ old('jumlah_ikan_terinfeksi') }}"
                                    placeholder="0" oninput="hitungOtomatis()">
                                <span class="input-group-text">ekor</span>
                            </div>
                            <div class="text-muted small mt-1">Pembilang kedua formula</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">
                                <i class="ti ti-clipboard me-1 text-blue"></i>Jumlah Sampel Diperiksa
                            </label>
                            <div class="input-group">
                                <input type="number" id="jml_diperiksa" name="jumlah_sampel_diperiksa"
                                    class="form-control" min="1"
                                    value="{{ old('jumlah_sampel_diperiksa', $pelaksanaan->jumlah_sampel ?? '') }}"
                                    placeholder="{{ $pelaksanaan->jumlah_sampel ?? '0' }}"
                                    oninput="hitungOtomatis()">
                                <span class="input-group-text">ekor</span>
                            </div>
                            <div class="text-muted small mt-1">Penyebut Prevalensi</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="ti ti-squares me-1 text-orange"></i>Jumlah Kolam Uji
                            </label>
                            <div class="input-group">
                                <input type="number" id="jml_kolam" name="jumlah_kolam_uji"
                                    class="form-control" min="1"
                                    value="{{ old('jumlah_kolam_uji') }}"
                                    placeholder="0" oninput="hitungOtomatis()">
                                <span class="input-group-text">kolam</span>
                            </div>
                            <div class="text-muted small mt-1">Untuk Insidensi</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="ti ti-calendar me-1 text-purple"></i>Periode Pengamatan
                            </label>
                            <div class="input-group">
                                <input type="number" id="periode" name="periode_pengamatan"
                                    class="form-control" min="1"
                                    value="{{ old('periode_pengamatan') }}"
                                    placeholder="0" oninput="hitungOtomatis()">
                                <span class="input-group-text">hari</span>
                            </div>
                            <div class="text-muted small mt-1">Untuk Insidensi</div>
                        </div>
                    </div>

                    {{-- Hasil Kalkulasi --}}
                    <div class="row g-3 mt-2">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="ti ti-percentage me-1"></i>Prevalensi (%)
                                <span class="badge bg-blue-lt text-blue ms-1">Kol. 15</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-blue-lt">
                                    <i class="ti ti-math-function text-blue"></i>
                                </span>
                                <input type="number" id="hasil_prevalensi" name="prevalensi"
                                    step="0.01" min="0" max="100"
                                    class="form-control fw-bold"
                                    value="{{ old('prevalensi') }}"
                                    placeholder="Otomatis terhitung">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-info-circle me-1"></i>
                                Dihitung otomatis dari input di atas, dapat diubah manual
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <div id="kalkulasi-status" class="text-center">
                                <div class="text-muted small">⟵ Input komponen</div>
                                <div class="text-muted small">untuk auto-calc ⟶</div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="ti ti-chart-line me-1"></i>Insidensi
                                <span class="badge bg-orange-lt text-orange ms-1">Kol. 16</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-orange-lt">
                                    <i class="ti ti-math-function text-orange"></i>
                                </span>
                                <input type="number" id="hasil_insidensi" name="insidensi"
                                    step="0.000001" min="0"
                                    class="form-control fw-bold"
                                    value="{{ old('insidensi') }}"
                                    placeholder="Otomatis terhitung">
                                <span class="input-group-text">ekor/kolam/hari</span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-info-circle me-1"></i>
                                Perlu data Kolam & Periode. Dapat diubah manual.
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            {{-- ============================================================ --}}
            {{-- CARD 4: Diagnosis & Keterangan --}}
            {{-- ============================================================ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-notes me-2"></i>Diagnosis & Keterangan</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Diagnosis Akhir / Keterangan</label>
                            <textarea name="diagnosis_akhir" class="form-control" rows="3"
                                placeholder="Catatan tambahan, kondisi khusus, atau keterangan lain (Kol. 18 Ket.)">{{ old('diagnosis_akhir') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Hasil Keluar</label>
                            <input type="date" name="tanggal_hasil"
                                class="form-control" value="{{ old('tanggal_hasil') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Hasil Lab
                    </button>
                    <a href="{{ route('laboratorium.index') }}" class="btn btn-link">Batal</a>
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
    var prevEl   = document.getElementById('hasil_prevalensi');
    var insEl    = document.getElementById('hasil_insidensi');

    var msgs = [];

    // ── PREVALENSI ────────────────────────────────────────────────────────
    if (!isNaN(terinfeksi) && !isNaN(diperiksa) && diperiksa > 0) {
        var prev = (terinfeksi / diperiksa) * 100;
        prevEl.value = prev.toFixed(2);
        prevEl.classList.add('border-primary');
        msgs.push('✅ Prevalensi: <strong>' + prev.toFixed(2) + '%</strong>');
    } else {
        prevEl.classList.remove('border-primary');
        if (!isNaN(terinfeksi) || !isNaN(diperiksa)) {
            msgs.push('⚠️ Isi Terinfeksi & Diperiksa untuk Prevalensi');
        }
    }

    // ── INSIDENSI ────────────────────────────────────────────────────────
    if (!isNaN(terinfeksi) && !isNaN(kolam) && !isNaN(periode) && kolam > 0 && periode > 0) {
        var ins = terinfeksi / (kolam * periode);
        insEl.value = ins.toFixed(6);
        insEl.classList.add('border-warning');
        msgs.push('✅ Insidensi: <strong>' + ins.toFixed(6) + '</strong> ekor/kolam/hari');
    } else {
        insEl.classList.remove('border-warning');
        if (!isNaN(terinfeksi) && (isNaN(kolam) || isNaN(periode))) {
            msgs.push('ℹ️ Isi Kolam & Periode untuk Insidensi');
        }
    }

    // ── Update status area ───────────────────────────────────────────────
    if (msgs.length > 0) {
        statusEl.innerHTML = '<div class="badge bg-green-lt text-success small p-2 text-wrap text-start">'
            + msgs.join('<br>') + '</div>';
    } else {
        statusEl.innerHTML = '<div class="text-muted small text-center">⟵ Input komponen<br>untuk auto-calc ⟶</div>';
    }
}

// Jalankan saat halaman load (bila ada old() input)
document.addEventListener('DOMContentLoaded', function () {
    hitungOtomatis();
});
</script>
@endsection
