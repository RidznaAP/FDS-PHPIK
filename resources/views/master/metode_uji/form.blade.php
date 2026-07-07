@extends('layouts.app')

@section('title', isset($item) ? 'Edit Metode Uji' : 'Tambah Metode Uji')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-0 pt-4">
                <h3 class="card-title fw-bold text-primary">
                    <i class="ti ti-{{ isset($item) ? 'pencil' : 'plus' }} me-2"></i>
                    {{ isset($item) ? 'Edit' : 'Tambah' }} Metode Uji
                </h3>
            </div>
            <form action="{{ isset($item) ? route('master.metode-uji.update', $item) : route('master.metode-uji.store') }}" method="POST">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required fw-bold">Nama Metode Uji</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                               value="{{ old('nama', $item->nama ?? '') }}" required autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="3">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="hidden" name="aktif" value="0">
                            <input class="form-check-input" type="checkbox" name="aktif" value="1" 
                                   {{ old('aktif', $item->aktif ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label fw-bold">Status Aktif</span>
                        </label>
                        <div class="form-hint text-muted small">
                            Jika non-aktif, data ini tidak akan muncul di pilihan dropdown form laboratorium.
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end gap-2">
                    <a href="{{ route('master.metode-uji.index') }}" class="btn btn-link link-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
