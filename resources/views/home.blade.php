@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header Selamat Datang --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white shadow">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                    <p class="mb-0 opacity-75">
                        Role:
                        @if(Auth::user()->isPusat())
                            <span class="badge bg-danger">Admin Pusat</span>
                        @elseif(Auth::user()->isBbkhit())
                            <span class="badge bg-warning text-dark">Admin BBKHIT</span>
                        @else
                            <span class="badge bg-success">Admin UPT</span>
                        @endif
                        &mdash; {{ Auth::user()->upt_asal ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Perencanaan</h6>
                    <h2 class="fw-bold text-primary">{{ $totalPerencanaan }}</h2>
                    <a href="{{ route('perencanaan.index') }}" class="text-decoration-none small">Lihat Detail →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Pelaksanaan</h6>
                    <h2 class="fw-bold text-success">{{ $totalPelaksanaan }}</h2>
                    <a href="{{ route('pelaksanaan.index') }}" class="text-decoration-none small">Lihat Detail →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Titik GIS Tercatat</h6>
                    <h2 class="fw-bold text-info">{{ $totalGIS }}</h2>
                    <span class="text-muted small">Lokasi dengan koordinat</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions berdasarkan Role --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">Menu Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Semua role bisa lihat data --}}
                        <div class="col-md-3">
                            <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-primary w-100 py-3">
                                📋 Daftar Perencanaan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-success w-100 py-3">
                                📊 Daftar Pelaksanaan
                            </a>
                        </div>

                        {{-- Hanya UPT yang bisa input --}}
                        @if(Auth::user()->isUpt())
                        <div class="col-md-3">
                            <a href="{{ route('perencanaan.create') }}" class="btn btn-primary w-100 py-3">
                                ➕ Tambah Perencanaan
                            </a>
                        </div>
                        @endif

                        {{-- Info role --}}
                        @if(Auth::user()->isPusat())
                        <div class="col-md-3">
                            <button class="btn btn-outline-danger w-100 py-3" disabled title="Segera hadir">
                                👥 Manajemen User (segera)
                            </button>
                        </div>
                        @endif

                        @if(Auth::user()->isBbkhit())
                        <div class="col-md-3">
                            <button class="btn btn-outline-warning w-100 py-3" disabled title="Segera hadir">
                                ✅ Validasi Data (segera)
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
