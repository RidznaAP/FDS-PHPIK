@extends('layouts.guest')

@section('title', 'Konfirmasi Password')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">
        
        <div style="text-align:center; margin-bottom:1.75rem;">
            <div style="
                display:inline-flex; align-items:center; justify-content:center;
                width:64px; height:64px; border-radius:18px;
                background:linear-gradient(135deg,#9333ea,#a855f7);
                box-shadow:0 8px 20px rgba(168,85,247,0.3);
                margin-bottom:1rem;
            ">
                <i class="ti ti-shield-lock" style="font-size:1.8rem;color:#fff;"></i>
            </div>
            <h2 class="auth-title" style="margin-bottom:0.25rem;">Konfirmasi Akses</h2>
            <p style="color:#64748b;font-size:0.875rem;text-align:center;margin-top:0.25rem;">
                Area ini membutuhkan keamanan ekstra. Silakan konfirmasi password Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
                <label class="form-lbl">Password Anda</label>
                <div class="input-group-pw">
                    <span style="position:absolute;left:0.9rem;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:1;">
                        <i class="ti ti-lock" style="font-size:1.1rem;"></i>
                    </span>
                    <input type="password" name="password" id="password"
                        class="form-input @error('password') error @enderror"
                        style="padding-left:2.8rem;"
                        placeholder="Masukkan password saat ini"
                        required autocomplete="current-password" autofocus>
                    <button type="button" class="pw-toggle" onclick="togglePassword('password', 'eye-icon')" title="Lihat password">
                        <i class="ti ti-eye" id="eye-icon" style="font-size:1.2rem;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg" style="color:#dc2626;font-size:0.8rem;margin-top:0.4rem;">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn-submit" style="background:#0f172a;box-shadow:0 4px 6px -1px rgba(15,23,42,0.2);">
                <i class="ti ti-check me-2"></i>Lanjutkan ke Area Aman
            </button>
        </form>

        @if (Route::has('password.request'))
            <div style="text-align:center;margin-top:1.5rem;">
                <a href="{{ route('password.request') }}" style="color:#2563eb;font-size:0.85rem;text-decoration:none;font-weight:600;">
                    <i class="ti ti-help-circle me-1"></i>Lupa Password Anda?
                </a>
            </div>
        @endif
        
    </div>
    
    <div class="auth-card-footer">
        <a href="{{ url()->previous() }}">← Batalkan dan Kembali</a>
    </div>
</div>

<style>
.error-msg { color:#dc2626; font-size:0.8rem; margin-top:0.4rem; }
.form-input.error { border-color:#dc2626; background:#fff5f5; }
</style>
@endsection
