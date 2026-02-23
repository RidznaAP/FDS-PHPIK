@extends('layouts.app')

@section('title', 'Buat Akun Baru')
@section('page_title', 'Buat Akun Pengguna Baru')
@section('page_subtitle', 'Tambahkan akun BKHIT atau BBKHIT')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-user-plus me-2"></i>Data Pengguna Baru</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Role Selector (pilih dulu supaya konteks jelas) --}}
                        <div class="col-12">
                            <label class="form-label required">Role Pengguna</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="role" value="bkhit"
                                            class="form-selectgroup-input"
                                            {{ old('role') === 'bkhit' ? 'checked' : '' }} required>
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div>
                                                <span class="form-selectgroup-title fw-bold d-block">BKHIT</span>
                                                <span class="text-muted small">Membuat perencanaan & input lapangan</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="role" value="bbkhit"
                                            class="form-selectgroup-input"
                                            {{ old('role') === 'bbkhit' ? 'checked' : '' }}>
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div>
                                                <span class="form-selectgroup-title fw-bold d-block">BBKHIT</span>
                                                <span class="text-muted small">Menyetujui perencanaan & evaluasi</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nama --}}
                        <div class="col-md-6">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Budi Santoso"
                                required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label required">Email (untuk Login)</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="Contoh: budi@fds.go.id"
                                required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Instansi --}}
                        <div class="col-12">
                            <label class="form-label">BKHIT / Instansi Asal</label>
                            <input type="text" name="upt_asal"
                                class="form-control"
                                value="{{ old('upt_asal') }}"
                                placeholder="Contoh: Balai KHIT Aceh">
                            <div class="text-muted small mt-1">Opsional — nama unit kerja atau instansi</div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="form-label required">Password</label>
                            <div class="input-group">
                                <input type="password" id="pw1" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Min. 8 karakter"
                                    required minlength="8">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw1','eye1')" tabindex="-1">
                                    <i class="ti ti-eye" id="eye1"></i>
                                </button>
                            </div>
                            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" id="pw2" name="password_confirmation"
                                    class="form-control"
                                    placeholder="Ulangi password"
                                    required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw2','eye2')" tabindex="-1">
                                    <i class="ti ti-eye" id="eye2"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-user-check me-1"></i>Buat Akun
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-link">Batal</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Info panel --}}
    <div class="col-lg-4">
        <div class="card bg-blue-lt">
            <div class="card-body">
                <h4 class="text-blue"><i class="ti ti-info-circle me-2"></i>Panduan</h4>
                <ul class="text-muted small ps-3">
                    <li class="mb-2">Pilih role <b>BKHIT</b> untuk petugas lapangan yang membuat Perencanaan dan input data lapangan.</li>
                    <li class="mb-2">Pilih role <b>BBKHIT</b> untuk pejabat yang menyetujui Perencanaan dan membuat Evaluasi akhir.</li>
                    <li class="mb-2">Email digunakan sebagai username saat login — pastikan unik.</li>
                    <li>Password bisa direset kapan saja melalui menu Daftar Pengguna.</li>
                </ul>
            </div>
        </div>
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
