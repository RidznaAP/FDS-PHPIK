@extends('layouts.guest')

@section('title', 'Login Masuk')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">
        <h2 class="auth-title">Masuk ke Akun Anda</h2>
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf

            <div class="form-group">
                <label class="form-lbl">Alamat Email</label>
                <input type="email" name="email" class="form-input @error('email') error @enderror" placeholder="nama@instansi.go.id" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-lbl" style="margin-bottom: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: #2563eb; text-decoration: none; font-weight: 600;">Lupa password?</a>
                </div>
                <div class="input-group-pw">
                    <input type="password" name="password" id="password" class="form-input @error('password') error @enderror" placeholder="••••••••" required>
                    <button type="button" class="pw-toggle" onclick="togglePassword()" title="Lihat password">
                        <i class="ti ti-eye" id="eye-icon" style="font-size: 1.2rem;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="check-row" onclick="document.getElementById('remember').click()">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Ingat Saya</label>
            </div>

            <button type="submit" class="btn-submit">Masuk Aplikasi</button>
        </form>

        <div class="divider">atau</div>
    </div>
    <div class="auth-card-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </div>
</div>
@endsection
