@extends('layouts.app')

@section('title', 'Edit Perencanaan')
@section('page_title', 'Edit Perencanaan')
@section('page_subtitle', 'Ubah data rencana pemantauan HPIK (hanya Draft)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('perencanaan.update', $perencanaan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Lokasi & Komoditas</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                                value="{{ old('provinsi', $perencanaan->provinsi) }}" required
                                @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit()) readonly @endif>
                            @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Kabupaten / Kota</label>
                            <input type="text" name="kab_kota" class="form-control @error('kab_kota') is-invalid @enderror"
                                value="{{ old('kab_kota', $perencanaan->kab_kota) }}" required>
                            @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis MP (Media Pembawa)</label>
                            <input type="text" name="jenis_mp" class="form-control"
                                value="{{ old('jenis_mp', $perencanaan->jenis_mp) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis HPIK (Target)</label>
                            <input type="text" name="jenis_hpik" class="form-control"
                                value="{{ old('jenis_hpik', $perencanaan->jenis_hpik) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Kemampuan Uji UPT</label>
                            <input type="text" name="kemampuan_uji_upt" class="form-control"
                                value="{{ old('kemampuan_uji_upt', $perencanaan->kemampuan_uji_upt) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Metode Pengujian</label>
                            <input type="text" name="metode_pengujian" class="form-control"
                                value="{{ old('metode_pengujian', $perencanaan->metode_pengujian) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Lab Uji</label>
                            <input type="text" name="lab_uji" class="form-control"
                                value="{{ old('lab_uji', $perencanaan->lab_uji) }}" required>
                        </div>
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-md-6">
                            <label class="form-label">Rencana Lokasi Pengambilan Sampel</label>
                            <input type="text" name="rencana_lokasi" class="form-control" 
                                value="{{ old('rencana_lokasi', $perencanaan->rencana_lokasi) }}" placeholder="Contoh: Tambak Udang Kec. Bireun">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rencana Jumlah Sampel</label>
                            <input type="number" name="rencana_jumlah_sampel" class="form-control" 
                                value="{{ old('rencana_jumlah_sampel', $perencanaan->rencana_jumlah_sampel) }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rencana Metode Sampling</label>
                            <input type="text" name="rencana_metode_sampling" class="form-control" 
                                value="{{ old('rencana_metode_sampling', $perencanaan->rencana_metode_sampling) }}" placeholder="Contoh: Acak / Selektif">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Target Per Kuartal (TW)</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 1</label>
                            <input type="number" name="tw1" class="form-control text-center"
                                value="{{ old('tw1', $perencanaan->tw1) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 2</label>
                            <input type="number" name="tw2" class="form-control text-center"
                                value="{{ old('tw2', $perencanaan->tw2) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 3</label>
                            <input type="number" name="tw3" class="form-control text-center"
                                value="{{ old('tw3', $perencanaan->tw3) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 4</label>
                            <input type="number" name="tw4" class="form-control text-center"
                                value="{{ old('tw4', $perencanaan->tw4) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Total Target Uji (Tahun)</label>
                            <input type="number" name="target_uji" class="form-control"
                                value="{{ old('target_uji', $perencanaan->target_uji) }}" required min="1">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-link">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
