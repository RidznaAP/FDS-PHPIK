@extends('layouts.app')

@php $editing = isset($item); @endphp
@section('title', $editing ? 'Edit Jenis Penyakit' : 'Tambah Jenis Penyakit')
@section('page_title', $editing ? 'Edit Jenis Penyakit' : 'Tambah Jenis Penyakit')
@section('page_subtitle', 'Master Data Jenis Penyakit / HPIK')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-virus me-2"></i>
                    {{ $editing ? 'Edit: ' . $item->nama : 'Tambah Jenis Penyakit Baru' }}
                </h3>
            </div>
            <form action="{{ $editing ? route('master.jenis-penyakit.update', $item) : route('master.jenis-penyakit.store') }}"
                  method="POST">
                @csrf
                @if($editing) @method('PUT') @endif
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label required">Nama Penyakit / HPIK</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $item->nama ?? '') }}"
                            placeholder="Contoh: Infection with ictalurid herpesvirus-1" required autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Organisme Penyebab --}}
                    <div class="mb-3">
                        <label class="form-label">Organisme Penyebab</label>
                        <input type="text" name="organisme_penyebab"
                            class="form-control @error('organisme_penyebab') is-invalid @enderror"
                            value="{{ old('organisme_penyebab', $item->organisme_penyebab ?? '') }}"
                            placeholder="Contoh: Ictalurid herpesvirus-1">
                        @error('organisme_penyebab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">Nama patogen atau penyebab spesifik.</div>
                    </div>

                    {{-- Golongan --}}
                    <div class="mb-3">
                        <label class="form-label required">Golongan Patogen</label>
                        <select name="golongan" class="form-select @error('golongan') is-invalid @enderror" required>
                            <option value="">— Pilih Golongan —</option>
                            @foreach(['Virus','Bakteri','Parasit','Jamur'] as $g)
                                <option value="{{ $g }}"
                                    {{ old('golongan', $item->golongan ?? '') === $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                        @error('golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Deskripsi singkat, inang utama, atau catatan penting (opsional)">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
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
                    <a href="{{ route('master.jenis-penyakit.index') }}" class="btn btn-link">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
