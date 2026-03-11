@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">

        {{-- Icon + Title --}}
        <div style="text-align:center; margin-bottom:1.75rem;">
            <div style="
                display:inline-flex; align-items:center; justify-content:center;
                width:64px; height:64px; border-radius:18px;
                background:linear-gradient(135deg,#1d4ed8,#6366f1);
                box-shadow:0 8px 20px rgba(37,99,235,0.3);
                margin-bottom:1rem;
            ">
                <i class="ti ti-lock-open" style="font-size:1.8rem;color:#fff;"></i>
            </div>
            <h2 class="auth-title" style="margin-bottom:0.25rem;">Buat Password Baru</h2>
            <p style="color:#64748b;font-size:0.875rem;text-align:center;margin-top:0.25rem;">
                Masukkan password baru yang kuat untuk akun Anda
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" id="reset-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="form-group">
                <label class="form-lbl">Alamat Email</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:0.9rem;top:50%;transform:translateY(-50%);color:#94a3b8;">
                        <i class="ti ti-mail" style="font-size:1.1rem;"></i>
                    </span>
                    <input type="email" name="email"
                        class="form-input @error('email') error @enderror"
                        style="padding-left:2.8rem;"
                        value="{{ $email ?? old('email') }}"
                        required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <div class="error-msg" style="color:#dc2626;font-size:0.8rem;margin-top:0.4rem;">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div class="form-group">
                <label class="form-lbl">Password Baru</label>
                <div class="input-group-pw">
                    <span style="position:absolute;left:0.9rem;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:1;">
                        <i class="ti ti-lock" style="font-size:1.1rem;"></i>
                    </span>
                    <input type="password" name="password" id="new-password"
                        class="form-input @error('password') error @enderror"
                        style="padding-left:2.8rem;"
                        placeholder="Minimal 8 karakter"
                        required autocomplete="new-password"
                        oninput="checkStrength(this.value)">
                    <button type="button" class="pw-toggle"
                        onclick="togglePassword('new-password','eye-new')" title="Lihat password">
                        <i class="ti ti-eye" id="eye-new" style="font-size:1.2rem;"></i>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div style="margin-top:6px;">
                    <div style="height:4px;border-radius:4px;background:#e5e7eb;overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0%;border-radius:4px;transition:all 0.35s;"></div>
                    </div>
                    <div id="strength-label" style="font-size:0.72rem;margin-top:3px;color:#94a3b8;"></div>
                </div>
                @error('password')
                    <div class="error-msg" style="color:#dc2626;font-size:0.8rem;margin-top:0.4rem;">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label class="form-lbl">Konfirmasi Password Baru</label>
                <div class="input-group-pw">
                    <span style="position:absolute;left:0.9rem;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:1;">
                        <i class="ti ti-shield-check" style="font-size:1.1rem;"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="confirm-password"
                        class="form-input"
                        style="padding-left:2.8rem;"
                        placeholder="Ulangi password baru"
                        required autocomplete="new-password"
                        oninput="checkMatch()">
                    <button type="button" class="pw-toggle"
                        onclick="togglePassword('confirm-password','eye-confirm')" title="Lihat password">
                        <i class="ti ti-eye" id="eye-confirm" style="font-size:1.2rem;"></i>
                    </button>
                </div>
                <div id="match-msg" style="font-size:0.72rem;margin-top:4px;"></div>
            </div>

            {{-- Requirements hint --}}
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:10px 14px;margin-bottom:1.25rem;">
                <p style="font-size:0.78rem;color:#0369a1;margin:0;font-weight:600;">Syarat password:</p>
                <ul style="margin:6px 0 0 0;padding-left:1rem;font-size:0.78rem;color:#0369a1;">
                    <li id="req-len"   style="opacity:0.5;">Minimal 8 karakter</li>
                    <li id="req-upper" style="opacity:0.5;">Mengandung huruf kapital (A–Z)</li>
                    <li id="req-num"   style="opacity:0.5;">Mengandung angka (0–9)</li>
                </ul>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <i class="ti ti-lock-check" style="margin-right:0.5rem;"></i>
                Simpan Password Baru
            </button>
        </form>
    </div>

    <div class="auth-card-footer">
        <a href="{{ route('login') }}">← Kembali ke Login</a>
    </div>
</div>

<style>
.error-msg { color:#dc2626; font-size:0.8rem; margin-top:0.4rem; }
.form-input.error { border-color:#dc2626; background:#fff5f5; }
</style>

<script>
function checkStrength(val) {
    var bar   = document.getElementById('strength-bar');
    var label = document.getElementById('strength-label');
    var score = 0;

    var reqLen   = document.getElementById('req-len');
    var reqUpper = document.getElementById('req-upper');
    var reqNum   = document.getElementById('req-num');

    // Check requirements
    var hasLen   = val.length >= 8;
    var hasUpper = /[A-Z]/.test(val);
    var hasNum   = /[0-9]/.test(val);
    var hasSym   = /[^A-Za-z0-9]/.test(val);

    reqLen.style.opacity   = hasLen   ? '1' : '0.4';
    reqLen.style.color     = hasLen   ? '#15803d' : '#0369a1';
    reqUpper.style.opacity = hasUpper ? '1' : '0.4';
    reqUpper.style.color   = hasUpper ? '#15803d' : '#0369a1';
    reqNum.style.opacity   = hasNum   ? '1' : '0.4';
    reqNum.style.color     = hasNum   ? '#15803d' : '#0369a1';

    if (hasLen)   score++;
    if (hasUpper) score++;
    if (hasNum)   score++;
    if (hasSym)   score++;
    if (val.length >= 12) score++;

    var colors = ['#ef4444','#f97316','#f59e0b','#22c55e','#16a34a'];
    var labels = ['Sangat Lemah','Lemah','Sedang','Kuat','Sangat Kuat'];
    var widths = ['20%','40%','60%','80%','100%'];

    var idx = Math.min(score - 1, 4);
    if (val.length === 0) {
        bar.style.width = '0%';
        label.textContent = '';
    } else {
        bar.style.background = colors[Math.max(idx, 0)];
        bar.style.width      = widths[Math.max(idx, 0)];
        label.textContent    = labels[Math.max(idx, 0)];
        label.style.color    = colors[Math.max(idx, 0)];
    }
}

function checkMatch() {
    var pw   = document.getElementById('new-password').value;
    var conf = document.getElementById('confirm-password').value;
    var msg  = document.getElementById('match-msg');
    if (conf.length === 0) { msg.textContent = ''; return; }
    if (pw === conf) {
        msg.textContent = '✅ Password cocok';
        msg.style.color = '#16a34a';
    } else {
        msg.textContent = '❌ Password tidak cocok';
        msg.style.color = '#dc2626';
    }
}
</script>
@endsection
