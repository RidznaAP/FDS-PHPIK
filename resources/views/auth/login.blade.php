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
                    <label class="form-lbl" style="margin-bottom: 0;">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: #2563eb; text-decoration: none; font-weight: 600;">Lupa Kata Sandi?</a>
                </div>
                <div class="input-group-pw">
                    <input type="password" name="password" id="password" class="form-input @error('password') error @enderror" placeholder="••••••••" required>
                    <button type="button" class="pw-toggle" onclick="togglePassword()" title="Tampilkan kata sandi">
                        <i class="ti ti-eye" id="eye-icon" style="font-size: 1.2rem;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-lbl" style="font-size: 1.25rem; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 0.75rem; color: #000; text-transform: uppercase;">HITUNG {{ $num1 ?? 5 }} + {{ $num2 ?? 4 }}</label>
                <div style="display: flex; align-items: stretch;">
                    <input type="number" name="captcha" class="form-input @error('captcha') error @enderror" placeholder="Hasil Penjumlahan" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; font-size: 1rem; flex: 1;">
                    <div style="display: flex; align-items: center; justify-content: center; background: #f1f5f9; border: 1px solid {{ $errors->has('captcha') ? '#ef4444' : '#e2e8f0' }}; border-left: none; border-radius: 0 8px 8px 0; padding: 0 1rem; color: #0f172a; font-weight: bold; font-size: 1.25rem;">
                        ?
                    </div>
                </div>
                @error('captcha')
                    <div class="error-msg" style="margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <label class="check-row" style="margin-top: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="cursor: pointer;">
                <span style="user-select: none; flex: 1; font-weight: 500; color: #475569; font-size: 0.9rem;">Ingat Saya</span>
            </label>

            <button type="submit" class="btn-submit">Masuk Aplikasi</button>
        </form>

        <div class="divider"></div>
    </div>
    <div class="auth-card-footer" style="font-size:0.82rem; color:#94a3b8; text-align:center;">
        <i class="ti ti-shield-lock" style="margin-right:4px;"></i>
        Akses khusus pengguna terdaftar. Hubungi <strong>Admin Pusat</strong> untuk pembuatan akun baru.
    </div>
</div>
@endsection
