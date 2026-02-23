@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun dan ubah password')

@section('content')
<div class="row row-cards">

    {{-- Kartu Info Profil --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <span class="avatar avatar-xl mb-3" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); font-size:2rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <h3 class="mb-1">{{ Auth::user()->name }}</h3>
                <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                @if(Auth::user()->isUpt())
                    <span class="badge bg-success-lt fs-6">BKHIT</span>
                @elseif(Auth::user()->isBbkhit())
                    <span class="badge bg-warning-lt fs-6">BBKHIT</span>
                @else
                    <span class="badge bg-purple-lt fs-6">PUSAT</span>
                @endif
                @if(Auth::user()->upt_asal)
                    <div class="text-muted small mt-2"><i class="ti ti-building me-1"></i>{{ Auth::user()->upt_asal }}</div>
                @endif
            </div>
            <div class="card-body border-top">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="text-muted small">Bergabung</div>
                        <div class="fw-semibold">{{ Auth::user()->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Role</div>
                        <div class="fw-semibold text-capitalize">{{ Auth::user()->role }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Edit Profil --}}
    <div class="col-lg-8">
        {{-- Update Profil --}}
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-user me-2"></i>Edit Profil</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">BKHIT / Instansi Asal</label>
                            <input type="text" name="upt_asal" class="form-control"
                                   value="{{ old('upt_asal', Auth::user()->upt_asal) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ strtoupper(Auth::user()->role) }}" disabled>
                            <div class="text-muted small mt-1">Role hanya bisa diubah oleh Admin Pusat</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        {{-- Ubah Password --}}
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-lock me-2"></i>Ubah Password</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required">Password Lama</label>
                            <div class="input-group">
                                <input type="password" id="pw_old" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw_old','eye_old')" tabindex="-1">
                                    <i class="ti ti-eye" id="eye_old"></i>
                                </button>
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Password Baru</label>
                            <div class="input-group">
                                <input type="password" id="pw_new" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw_new','eye_new')" tabindex="-1">
                                    <i class="ti ti-eye" id="eye_new"></i>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" id="pw_conf" name="password_confirmation"
                                    class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw_conf','eye_conf')" tabindex="-1">
                                    <i class="ti ti-eye" id="eye_conf"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-key me-1"></i>Ubah Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePw(inputId, iconId) {
    var el = document.getElementById(inputId);
    var ic = document.getElementById(iconId);
    if (el.type === 'password') {
        el.type = 'text';
        ic.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        el.type = 'password';
        ic.classList.replace('ti-eye-off', 'ti-eye');
    }
}
</script>
@endsection
