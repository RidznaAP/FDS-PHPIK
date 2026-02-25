@extends('layouts.app')

@section('title', '403 — Akses Ditolak')

@section('content')
<div class="container-tight py-6">
    <div class="text-center">
        <div class="mb-4" style="font-size:5rem;line-height:1;">🔒</div>
        <div class="text-muted mb-2" style="font-size:1rem;font-weight:600;letter-spacing:2px;text-transform:uppercase;">Error 403</div>
        <h1 class="mb-2" style="font-size:2rem;font-weight:700;">Akses Ditolak</h1>
        <p class="text-muted mb-4">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Hubungi Admin Pusat jika Anda merasa ini adalah kesalahan.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="ti ti-dashboard me-1"></i>Dashboard
            </a>
        </div>
        <div class="mt-4 text-muted small">
            Login sebagai: <strong>{{ Auth::check() ? Auth::user()->name . ' (' . strtoupper(Auth::user()->role) . ')' : 'Tamu' }}</strong>
        </div>
    </div>
</div>
@endsection
