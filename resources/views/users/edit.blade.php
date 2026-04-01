@extends('layouts.app')

@section('title', 'Edit Akun')
@section('page_title', 'Edit Akun Pengguna')
@section('page_subtitle', 'Ubah data nama, email, role, atau instansi')

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-12">
        {{-- High-End Page Header --}}
        <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-primary border-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-4">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                        <i class="ti ti-user-edit fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-lt text-primary px-3 fs-6 rounded-pill">SISTEM ADMIN</span>
                        </div>
                        <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">Edit Akun Pengguna</h1>
                        <div class="text-muted fs-3">Ubah detail data nama, email, role, atau instansi</div>
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
            <div class="col-12">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="card card-premium border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-user-edit me-2 text-primary"></i> Detail Akun Form
                            </h3>
                        </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar" style="background: linear-gradient(135deg,
                            {{ $user->role === 'bkhit' ? '#16a34a,#22c55e' : ($user->role === 'bbkhit' ? '#ca8a04,#eab308' : '#7c3aed,#a78bfa') }});">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div>
                            <h3 class="card-title mb-0">{{ $user->name }}</h3>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Alamat Email</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Role</label>
                        @if($user->role === 'pusat')
                            <input type="text" class="form-control" value="Admin Pusat" disabled>
                            <input type="hidden" name="role" value="pusat">
                            <div class="text-muted small mt-1">Role Pusat tidak dapat diubah.</div>
                        @else
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="bkhit" {{ old('role', $user->role) === 'bkhit' ? 'selected' : '' }}>
                                    BKHIT (UPT Pelaksana)
                                </option>
                                <option value="bbkhit" {{ old('role', $user->role) === 'bbkhit' ? 'selected' : '' }}>
                                    BBKHIT (Pengawas Regional)
                                </option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instansi / UPT Asal</label>
                        <input type="text" name="upt_asal"
                            class="form-control @error('upt_asal') is-invalid @enderror"
                            value="{{ old('upt_asal', $user->upt_asal) }}"
                            placeholder="Contoh: BKHIT Palembang">
                        @error('upt_asal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Koordinator Selector (Hanya untuk BKHIT) --}}
                    <div class="mb-3" id="coordinator_container" style="{{ (old('role', $user->role) === 'bkhit') ? '' : 'display:none;' }}">
                        <label class="form-label">Koordinator Wilayah (BBKHIT)</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Pilih Koordinator (Optional) —</option>
                            @foreach($coordinators ?? [] as $c)
                                <option value="{{ $c->id }}" {{ old('parent_id', $user->parent_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted small mt-1">Pilih BBKHIT yang membawahi unit ini</div>
                        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-info py-2">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Password</strong> tidak berubah saat edit data. Gunakan tombol <em>Reset PW</em> di halaman daftar pengguna untuk mengubah password.
                    </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top mt-2 d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-pill px-5 shadow-sm fw-bold">
                                <i class="ti ti-device-floppy me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-link link-secondary px-4">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            var container = document.getElementById('coordinator_container');
            if (this.value === 'bkhit') {
                container.style.display = '';
            } else {
                container.style.display = 'none';
                var select = container.querySelector('select');
                if (select) select.value = '';
            }
        });
    }
});
</script>
@endsection
