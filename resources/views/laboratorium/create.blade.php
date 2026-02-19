@extends('layouts.app')

@section('title', 'Input Hasil Laboratorium')
@section('page_title', 'Input Hasil Uji Laboratorium')
@section('page_subtitle', $pelaksanaan->lokasi_pengambilan_sampel)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        {{-- Info Sampel --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Informasi Sampel</h3></div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="text-muted small">Lokasi Sampling</div>
                        <div class="fw-semibold">{{ $pelaksanaan->lokasi_pengambilan_sampel }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Wilayah</div>
                        <div class="fw-semibold">{{ $pelaksanaan->perencanaan->kab_kota ?? '-' }}, {{ $pelaksanaan->perencanaan->provinsi ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Komoditas (Jenis MP)</div>
                        <div class="fw-semibold">{{ $pelaksanaan->perencanaan->jenis_mp ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Target HPIK</div>
                        <div class="fw-semibold">{{ $pelaksanaan->perencanaan->jenis_hpik ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Hasil Lab --}}
        <form action="{{ route('laboratorium.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pelaksanaan_id" value="{{ $pelaksanaan->id }}">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Hasil Pengujian</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Kode Sampel</label>
                            <input type="text" name="kode_sampel" class="form-control @error('kode_sampel') is-invalid @enderror" placeholder="Contoh: LAB-2026-001" required>
                            @error('kode_sampel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Metode Uji</label>
                            <select name="metode_uji" class="form-select" required>
                                <option value="">— Pilih Metode —</option>
                                <option value="PCR">PCR (Polymerase Chain Reaction)</option>
                                <option value="ELISA">ELISA</option>
                                <option value="Kultur Bakteri">Kultur Bakteri</option>
                                <option value="Histopatologi">Histopatologi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis HPIK yang Diuji</label>
                            <input type="text" name="jenis_hpik_diuji" class="form-control" value="{{ $pelaksanaan->perencanaan->jenis_hpik ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Hasil Uji</label>
                            <select name="hasil_uji" class="form-select" required>
                                <option value="">— Pilih Hasil —</option>
                                <option value="Negatif">✅ Negatif</option>
                                <option value="Positif">🔴 Positif</option>
                                <option value="Inkonklusif">⚠️ Inkonklusif</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Diagnosis / Keterangan</label>
                            <textarea name="diagnosis" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Laboratorium Penguji</label>
                            <input type="text" name="lab_penguji" class="form-control" value="{{ $pelaksanaan->perencanaan->lab_uji ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Pengujian</label>
                            <input type="date" name="tanggal_uji" class="form-control" value="{{ date('Y-m-d') }}" required>
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
