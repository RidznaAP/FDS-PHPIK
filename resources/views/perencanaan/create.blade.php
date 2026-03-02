@extends('layouts.app')

@section('title', 'Form Perencanaan')
@section('page_title', 'Perencanaan Baru')
@section('page_subtitle', 'Isi formulir rencana pemantauan HPIK')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('perencanaan.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Lokasi & Komoditas</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                                value="{{ old('provinsi', (Auth::user()->isBkhit() || Auth::user()->isBbkhit()) ? Auth::user()->upt_asal : '') }}" 
                                placeholder="Contoh: Jawa Barat" required
                                @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit()) readonly @endif>
                            @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Kabupaten / Kota</label>
                            <input type="text" name="kab_kota" class="form-control @error('kab_kota') is-invalid @enderror" value="{{ old('kab_kota') }}" placeholder="Contoh: Kota Bogor" required>
                            @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis MP (Media Pembawa)</label>
                            <input type="text" name="jenis_mp" list="mp-list"
                                class="form-control" value="{{ old('jenis_mp') }}"
                                placeholder="Pilih atau ketik nama ikan/udang..." required autocomplete="off">
                            <datalist id="mp-list">
                                @foreach($mediaPembawas ?? [] as $mp)
                                    <option value="{{ $mp->nama }}"
                                        @if($mp->nama_latin) label="{{ $mp->nama_latin }}" @endif>
                                @endforeach
                            </datalist>
                            @if(($mediaPembawas ?? collect())->isEmpty())
                                <div class="form-hint text-warning">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Belum ada master data. <a href="{{ route('master.media-pembawa.create') }}">Tambahkan</a> terlebih dahulu atau ketik manual.
                                </div>
                            @else
                                <div class="form-hint">Pilih dari daftar atau ketik nama baru.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jenis HPIK (Target)</label>
                            <input type="text" name="jenis_hpik" list="hpik-list"
                                class="form-control" value="{{ old('jenis_hpik') }}"
                                placeholder="Pilih atau ketik jenis penyakit..." required autocomplete="off">
                            <datalist id="hpik-list">
                                @foreach($jenisPenyakits ?? [] as $jp)
                                    <option value="{{ $jp->nama }}{{ $jp->singkatan ? ' (' . $jp->singkatan . ')' : '' }}">
                                @endforeach
                            </datalist>
                            @if(($jenisPenyakits ?? collect())->isEmpty())
                                <div class="form-hint text-warning">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Belum ada master data. <a href="{{ route('master.jenis-penyakit.create') }}">Tambahkan</a> atau ketik manual.
                                </div>
                            @else
                                <div class="form-hint">Pilih dari daftar atau ketik nama baru.</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Kemampuan Uji UPT</label>
                            <input type="text" name="kemampuan_uji_upt" class="form-control" value="{{ old('kemampuan_uji_upt') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Metode Pengujian</label>
                            <input type="text" name="metode_pengujian" class="form-control" value="{{ old('metode_pengujian') }}" placeholder="Contoh: PCR, ELISA" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Lab Uji</label>
                            <input type="text" name="lab_uji" class="form-control" value="{{ old('lab_uji') }}" required>
                        </div>
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-md-6">
                            <label class="form-label">Rencana Lokasi Pengambilan Sampel</label>
                            <input type="text" name="rencana_lokasi" class="form-control" value="{{ old('rencana_lokasi') }}" placeholder="Contoh: Tambak Udang Kec. Bireun">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rencana Jumlah Sampel</label>
                            <input type="number" name="rencana_jumlah_sampel" class="form-control" value="{{ old('rencana_jumlah_sampel', 0) }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rencana Metode Sampling</label>
                            <input type="text" name="rencana_metode_sampling" class="form-control" value="{{ old('rencana_metode_sampling') }}" placeholder="Contoh: Acak / Selektif">
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
                            <input type="number" name="tw1" class="form-control text-center" value="{{ old('tw1', 0) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 2</label>
                            <input type="number" name="tw2" class="form-control text-center" value="{{ old('tw2', 0) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 3</label>
                            <input type="number" name="tw3" class="form-control text-center" value="{{ old('tw3', 0) }}" min="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">TW 4</label>
                            <input type="number" name="tw4" class="form-control text-center" value="{{ old('tw4', 0) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Total Target Uji (Tahun)</label>
                            <input type="number" name="target_uji" class="form-control" value="{{ old('target_uji') }}" required min="1">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Simpan Perencanaan
                    </button>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-link">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
