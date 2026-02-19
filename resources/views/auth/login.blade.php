@extends('layouts.guest')

@section('title', 'Login Masuk')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Masuk ke Akun Anda</h2>
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@instansi.go.id" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-2">
                <label class="form-label">
                    Password
                    <span class="form-label-description">
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    </span>
                </label>
                <div class="input-group input-group-flat">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password Anda" required>
                    <span class="input-group-text">
                        <a href="#" class="link-secondary" onclick="togglePassword(); return false;" title="Lihat password">
                            <i class="ti ti-eye" id="eye-icon"></i>
                        </a>
                    </span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-2">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                    <span class="form-check-label">Ingat Saya</span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Masuk Aplikasi</button>
            </div>
        </form>
    </div>
    <div class="hr-text">atau</div>
    <div class="card-body">
        <div class="text-center text-muted">
            Belum punya akun? <a href="{{ route('register') }}" tabindex="-1">Daftar di sini</a>
        </div>
    </div>
</div>
