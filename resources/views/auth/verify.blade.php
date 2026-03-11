@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">
        
        <div style="text-align:center; margin-bottom:1.75rem;">
            <div style="
                display:inline-flex; align-items:center; justify-content:center;
                width:64px; height:64px; border-radius:18px;
                background:linear-gradient(135deg,#059669,#10b981);
                box-shadow:0 8px 20px rgba(16,185,129,0.3);
                margin-bottom:1rem;
            ">
                <i class="ti ti-mail-check" style="font-size:1.8rem;color:#fff;"></i>
            </div>
            <h2 class="auth-title" style="margin-bottom:0.25rem;">Verifikasi Email Anda</h2>
            <p style="color:#64748b;font-size:0.875rem;text-align:center;margin-top:0.25rem;">
                Kami telah mengirimkan link verifikasi ke alamat email Anda.
            </p>
        </div>

        @if (session('resent'))
            <div style="background:#dcfce7;border:1px solid #16a34a;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
                <div class="d-flex align-items-center gap-2" style="color:#15803d;font-size:0.9rem;font-weight:600;">
                    <i class="ti ti-circle-check fs-4"></i> Link verifikasi baru berhasil dikirim!
                </div>
            </div>
        @endif

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:1.5rem;">
            <p style="color:#475569;font-size:0.9rem;line-height:1.5;margin-bottom:10px;">
                Sebelum melanjutkan, silakan periksa kotak masuk email Anda (termasuk folder spam/junk) untuk mengakses link verifikasi.
            </p>
            <p style="color:#475569;font-size:0.9rem;line-height:1.5;margin:0;">
                Jika Anda tidak menerima email tersebut, Anda dapat meminta ulang.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.resend') }}" class="d-grid">
            @csrf
            <button type="submit" class="btn-submit" style="background:#0f172a;box-shadow:0 4px 6px -1px rgba(15,23,42,0.2);">
                <i class="ti ti-send me-2"></i>Kirim Ulang Email Verifikasi
            </button>
        </form>

    </div>
    <div class="auth-card-footer">
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#dc2626;font-weight:600;font-size:0.9rem;cursor:pointer;padding:0;">
                <i class="ti ti-logout me-1"></i>Logout Akun
            </button>
        </form>
    </div>
</div>
@endsection
