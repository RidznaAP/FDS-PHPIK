@extends('layouts.app')

@section('title', 'Laporan & Ekspor')
@section('page_title', 'Laporan & Ekspor Data')
@section('page_subtitle', 'Unduh data dalam format Excel atau lihat peta GIS')

@section('content')
<div class="row row-cards">
    <div class="col-md-4">
        <div class="card card-link card-link-pop">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-blue text-white avatar">
                            <i class="ti ti-clipboard-list"></i>
                        </span>
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
        <div class="card card-link card-link-pop">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar">
                            <i class="ti ti-flask"></i>
                        </span>
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
        <div class="card card-link card-link-pop">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-teal text-white avatar">
                            <i class="ti ti-map"></i>
                        </span>
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
