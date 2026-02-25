@extends('layouts.app')

@section('title', 'Laporan & Ekspor')
@section('page_title', 'Laporan & Ekspor Data')
@section('page_subtitle', 'Unduh Excel, cetak PDF, atau lihat peta GIS')

@section('content')

<div class="row row-cards mb-4">
    {{-- #14: Filter Wilayah + Tahun untuk laporan PDF --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title"><i class="ti ti-filter me-2"></i>Filter Laporan Ringkasan (PDF)</h3></div>
            <div class="card-body">
                <form action="{{ route('laporan.pdf') }}" method="GET" target="_blank" class="row g-2 align-items-end">
                    <div class="col-8">
                        <label class="form-label">Wilayah</label>
                        <select name="wilayah" class="form-select">
                            <option value="">Semua Wilayah</option>
                            @foreach($bkhitList as $w)
                                <option value="{{ $w }}">{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-danger w-100"><i class="ti ti-printer me-1"></i>Ringkasan PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Laporan Formulir HPIK (Sesuai Gambar) --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title"><i class="ti ti-file-text me-2"></i>Cetak Formulir Resmi HPIK</h3></div>
            <div class="card-body">
                <form action="{{ route('laporan.formulir') }}" method="GET" target="_blank" class="row g-2 align-items-end">
                    <div class="col-8">
                        <label class="form-label">Nama UPT</label>
                        <select name="wilayah" class="form-select" required>
                            <option value="">— Pilih UPT —</option>
                            @foreach($bkhitList as $w)
                                <option value="{{ $w }}">{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-download me-1"></i>Cetak Formulir (18 Kolom)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Ekspor Excel --}}
<div class="row row-cards">
    <div class="col-md-4">
        <div class="card card-link card-link-pop h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-blue text-white avatar"><i class="ti ti-clipboard-list"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Data Perencanaan</div>
                        <div class="text-muted small">Rekap seluruh perencanaan HPIK + status validasi</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('laporan.export.perencanaan') }}" class="btn btn-primary w-100">
                        <i class="ti ti-download me-1"></i>Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-link card-link-pop h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar"><i class="ti ti-flask"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Data Pelaksanaan & Lab</div>
                        <div class="text-muted small">GPS, sampel, dan hasil uji laboratorium</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('laporan.export.pelaksanaan') }}" class="btn btn-success w-100">
                        <i class="ti ti-download me-1"></i>Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-link card-link-pop h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-teal text-white avatar"><i class="ti ti-map"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Peta Sebaran GIS</div>
                        <div class="text-muted small">Visualisasi interaktif semua titik pemantauan</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('peta.index') }}" class="btn btn-teal w-100 text-white">
                        <i class="ti ti-map me-1"></i>Buka Peta GIS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
