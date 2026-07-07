@extends('layouts.app')

@php $editing = isset($item); @endphp
@section('title', $editing ? 'Edit Media Pembawa' : 'Tambah Media Pembawa')
@section('page_title', $editing ? 'Edit Media Pembawa' : 'Tambah Media Pembawa')
@section('page_subtitle', 'Master Data Media Pembawa / Jenis Ikan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-fish me-2"></i>
                    {{ $editing ? 'Edit: ' . $item->nama : 'Tambah Media Pembawa Baru' }}
                </h3>
            </div>
            <form action="{{ $editing ? route('master.media-pembawa.update', $item) : route('master.media-pembawa.store') }}"
                  method="POST">
                @csrf
                @if($editing) @method('PUT') @endif
                <div class="card-body">
                    {{-- Nama Umum --}}
                    <div class="mb-3">
                        <label class="form-label required">Nama Umum</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $item->nama ?? '') }}"
                            required autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Nama Inggris --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Inggris</label>
                        <input type="text" name="nama_inggris"
                            class="form-control @error('nama_inggris') is-invalid @enderror"
                            value="{{ old('nama_inggris', $item->nama_inggris ?? '') }}">
                        @error('nama_inggris')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Nama Ilmiah --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Ilmiah</label>
                        <input type="text" name="keterangan" 
                            class="form-control @error('keterangan') is-invalid @enderror"
                            value="{{ old('keterangan', $item->keterangan ?? '') }}">
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Aktif --}}
                    <div class="mb-0">
                        <label class="form-check form-switch">
                            <input type="hidden" name="aktif" value="0">
                            <input class="form-check-input" type="checkbox" name="aktif" value="1"
                                {{ old('aktif', $item->aktif ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Aktif (tampil di dropdown Perencanaan)</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ $editing ? 'Simpan Perubahan' : 'Tambahkan' }}
                    </button>
                    <a href="{{ route('master.media-pembawa.index') }}" class="btn btn-link">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
