@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<div class="container-tight py-6">
    <div class="text-center">
        <div class="mb-4" style="font-size:5rem;line-height:1;">🐠</div>
        <div class="text-muted mb-2" style="font-size:1rem;font-weight:600;letter-spacing:2px;text-transform:uppercase;">Error 404</div>
        <h1 class="mb-2" style="font-size:2rem;font-weight:700;">Halaman Tidak Ditemukan</h1>
        <p class="text-muted mb-4">
            Halaman yang Anda cari tidak ada atau sudah dipindahkan.<br>
            Silakan kembali ke dashboard.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="ti ti-dashboard me-1"></i>Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
