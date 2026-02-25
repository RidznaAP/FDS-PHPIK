@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">
        <h2 class="auth-title">Lupa Password?</h2>

        @if(session('status'))
            <div style="background:#dcfce7;border:1px solid #16a34a;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:0.9rem;">
                ✅ {{ session('status') }}
            </div>
        @endif

        <p style="color:#64748b;font-size:0.88rem;text-align:center;margin-bottom:1.2rem;">
            Masukkan email Anda. Jika akun ada, kami akan mengirimkan link reset password.
        </p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-lbl">Alamat Email</label>
                <input type="email" name="email"
                    class="form-input @error('email') error @enderror"
                    placeholder="nama@instansi.go.id"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Kirim Link Reset Password</button>
        </form>

        <div style="margin-top:1.2rem;padding:12px;background:#fef9c3;border-radius:8px;border-left:3px solid #ca8a04;">
            <p style="font-size:0.82rem;color:#854d0e;margin:0;">
                <strong>⚠️ Tidak menerima email?</strong><br>
                Hubungi <strong>Admin Pusat</strong> untuk reset password manual.
                Admin dapat mereset password Anda melalui halaman Manajemen Pengguna.
            </p>
        </div>
    </div>
    <div class="auth-card-footer">
        <a href="{{ route('login') }}">← Kembali ke Login</a>
    </div>
</div>
@endsection
