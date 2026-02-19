@extends('layouts.guest')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Daftar Akun Baru</h2>
        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Pegawai / Admin" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@instansi.go.id" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group input-group-flat">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
            </div>

            {{-- Role Selection (Optional: default to UPT for now or keep hidden if logic handles it) --}}
            {{-- For public registration, usually we don't let them pick 'Pusat' freely, but for this demo I'll let it handle by default or hidden --}}
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Buat Akun Baru</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="text-center text-muted">
            Sudah punya akun? <a href="{{ route('login') }}" tabindex="-1">Masuk di sini</a>
        </div>
    </div>
</div>
