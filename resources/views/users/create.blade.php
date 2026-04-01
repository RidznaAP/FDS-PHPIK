@extends('layouts.app')

@section('title', 'Buat Akun Baru')
@section('page_title', 'Buat Akun Pengguna Baru')
@section('page_subtitle', 'Tambahkan akun BKHIT atau BBKHIT')

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-12">
        {{-- High-End Page Header --}}
        <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-primary border-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-4">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                        <i class="ti ti-user-plus fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-lt text-primary px-3 fs-6 rounded-pill">SISTEM ADMIN</span>
                        </div>
                        <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">Buat Akun Baru</h1>
                        <div class="text-muted fs-3">Tambahkan akun akses BKHIT atau BBKHIT</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('users.index') }}" class="btn btn-white btn-pill px-4 border shadow-sm">
                    <i class="ti ti-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="card card-premium border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent pt-4 px-4 pb-0 border-0">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-user-plus me-2 text-primary"></i> Data Pengguna Baru
                            </h3>
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
                        <div class="col-md-6">
                            <label class="form-label">BKHIT / Instansi Asal</label>
                            <input type="text" name="upt_asal"
                                class="form-control"
                                value="{{ old('upt_asal') }}"
                                placeholder="Contoh: Balai KHIT Aceh">
                            <div class="text-muted small mt-1">Opsional — nama unit kerja</div>
                        </div>

                        {{-- Koordinator Selector (Hanya untuk BKHIT) --}}
                        <div class="col-md-6" id="coordinator_container" style="{{ old('role') === 'bkhit' ? '' : 'display:none;' }}">
                            <label class="form-label">Koordinator Wilayah (BBKHIT)</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">— Pilih Koordinator (Optional) —</option>
                                @foreach($coordinators as $c)
                                    <option value="{{ $c->id }}" {{ old('parent_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted small mt-1">Pilih BBKHIT yang membawahi unit ini</div>
                            @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top mt-2 d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-pill px-5 shadow-sm fw-bold">
                                <i class="ti ti-user-check me-2"></i>Buat Akun
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-link link-secondary px-4">Batal</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Info panel --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 bg-blue-lt">
                    <div class="card-body p-4">
                        <h4 class="text-blue fw-bold mb-4 fs-3"><i class="ti ti-info-circle me-2"></i>Panduan Hak Akses</h4>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex p-3 bg-white rounded-3 shadow-sm border border-light">
                                <div class="bg-blue-lt text-blue p-2 rounded-3 me-3 align-self-start"><i class="ti ti-edit"></i></div>
                                <div><b class="d-block mb-1">BKHIT</b>Petugas lapangan yang membuat Perencanaan dan input data.</div>
                            </div>
                            <div class="d-flex p-3 bg-white rounded-3 shadow-sm border border-light">
                                <div class="bg-purple-lt text-purple p-2 rounded-3 me-3 align-self-start"><i class="ti ti-checkup-list"></i></div>
                                <div><b class="d-block mb-1">BBKHIT</b>Pejabat yang menyetujui Perencanaan dan membuat Evaluasi.</div>
                            </div>
                        </div>
                        <ul class="text-muted small ps-3 mt-4">
                            <li class="mb-2">Email digunakan sebagai username saat login, harus valid.</li>
                            <li>Password default bisa diset, dan dapat di-reset kapan saja dari Daftar Pengguna.</li>
                        </ul>
                    </div>
                </div>
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

// Logic to show/hide coordinator field
document.querySelectorAll('input[name="role"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var container = document.getElementById('coordinator_container');
        if (this.value === 'bkhit') {
            container.style.display = '';
        } else {
            container.style.display = 'none';
            // Optional: reset value if not BKHIT
            container.querySelector('select').value = '';
        }
    });
});
</script>
@endsection
