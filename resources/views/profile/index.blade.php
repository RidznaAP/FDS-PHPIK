@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun dan ubah password')

@section('content')
<div class="row row-cards">

    {{-- Kartu Info Profil --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px !important;">
            <div class="card-header border-0 pb-0 pt-4 text-center d-block">
                <div class="position-relative d-inline-block">
                    <span class="avatar avatar-xl shadow-lg" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); font-size:2.2rem; width: 100px; height: 100px; border: 4px solid #fff;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="badge bg-green position-absolute bottom-0 end-0 border-white border-2" style="width:18px;height:18px;padding:0;border-radius:50%;" title="Online"></span>
                </div>
                <h3 class="mt-3 mb-0 fw-bold fs-3">{{ Auth::user()->name }}</h3>
                <p class="text-muted small">{{ Auth::user()->email }}</p>
                
                <div class="mt-2">
                    @if(Auth::user()->isUpt())
                        <span class="badge bg-green-lt px-3 py-2 btn-pill fw-bold" style="font-size:0.65rem;">BKHIT</span>
                    @elseif(Auth::user()->isBbkhit())
                        <span class="badge bg-warning-lt px-3 py-2 btn-pill fw-bold" style="font-size:0.65rem;">BBKHIT</span>
                    @else
                        <span class="badge bg-purple-lt px-3 py-2 btn-pill fw-bold" style="font-size:0.65rem;">ADMIN PUSAT</span>
                    @endif
                </div>
            </div>

            <div class="card-body pt-3">
                @if(Auth::user()->upt_asal)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light-lt mb-3">
                        <div class="bg-white p-2 rounded-2 shadow-sm">
                            <i class="ti ti-building text-primary fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase" style="font-size:0.6rem; letter-spacing:0.05em;">Unit Kerja / Instansi</div>
                            <div class="fw-bold text-dark">{{ Auth::user()->upt_asal }}</div>
                        </div>
                    </div>
                @endif

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 border border-light rounded-3 text-center">
                            <div class="text-muted small mb-1">Terdaftar Sejak</div>
                            <div class="fw-bold">{{ Auth::user()->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border border-light rounded-3 text-center">
                            <div class="text-muted small mb-1">Status Akun</div>
                            <div class="fw-bold text-success"><i class="ti ti-circle-check me-1"></i>Aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Koordinasi --}}
            @if(Auth::user()->isBkhit() && Auth::user()->coordinator)
                <div class="card-footer bg-blue-lt border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-blue text-white p-2 rounded-2">
                            <i class="ti ti-link fs-3"></i>
                        </div>
                        <div>
                            <div class="text-blue small fw-bold text-uppercase" style="font-size:0.6rem;">Koordinator Wilayah</div>
                            <div class="fw-bold">{{ Auth::user()->coordinator->name }}</div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->isBbkhit())
                <div class="card-footer bg-light border-0 p-4">
                    <div class="text-muted small mb-3 fw-bold text-uppercase" style="font-size:0.6rem; letter-spacing:0.05em;">
                        <i class="ti ti-users me-1"></i> Unit di Bawah Koordinasi
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse(Auth::user()->units as $u)
                            <span class="badge bg-white border border-light text-dark px-2 py-1 shadow-xs fw-normal">
                                <i class="ti ti-building-hospital me-1 text-success"></i>{{ $u->name }}
                            </span>
                        @empty
                            <div class="text-muted italic small">Belum ada unit terhubung.</div>
                        @endforelse
                    </div>
                </div>
            @endif
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
