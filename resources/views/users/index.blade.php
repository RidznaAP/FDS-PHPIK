@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page_title', 'Manajemen Akun Pengguna')
@section('page_subtitle', 'Kelola akun BKHIT dan BBKHIT yang terdaftar')

@section('page_actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus me-1"></i>Tambah Akun Baru
    </a>
@endsection

@section('content')

@php
    $totalBkhit     = $users->where('role','bkhit')->count();
    $totalBbkhit    = $users->where('role','bbkhit')->count();
    $totalPusat     = $users->where('role','pusat')->count();
    $totalDeveloper = $users->where('role','developer')->count();
@endphp

{{-- ─── Stat Cards ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#dcfce7;">
                    <i class="ti ti-building-community" style="color:#16a34a;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total BKHIT</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalBkhit }}</div>
                </div>
                <span class="ms-auto badge bg-success-lt text-success px-3">UPT</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#fef9c3;">
                    <i class="ti ti-building" style="color:#ca8a04;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total BBKHIT</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalBbkhit }}</div>
                </div>
                <span class="ms-auto badge bg-warning-lt text-warning px-3">Regional</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#ede9fe;">
                    <i class="ti ti-shield-check" style="color:#7c3aed;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Admin Pusat</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalPusat }}</div>
                </div>
                <span class="ms-auto badge bg-purple-lt text-purple px-3">Pusat</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#fee2e2;">
                    <i class="ti ti-shield-star" style="color:#dc2626;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Developer</div>
                    <div class="h2 mb-0 fw-bold">{{ $totalDeveloper }}</div>
                </div>
                <span class="ms-auto badge px-3" style="background:#fee2e2;color:#dc2626;">Super Admin</span>
            </div>
        </div>
    </div>
</div>

{{-- ─── Filter & Search ─────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <form method="GET" action="{{ route('users.index') }}" class="input-icon flex-grow-1" style="max-width:360px;">
        <span class="input-icon-addon"><i class="ti ti-search text-muted"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari nama, email, UPT…"
               value="{{ request('search') }}">
        @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif
    </form>
    <form id="filter-user" method="GET" action="{{ route('users.index') }}" class="d-flex gap-2 align-items-center">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <select name="role" class="form-select form-select-sm" style="width:160px;" onchange="this.form.submit()">
            <option value="">👥 Semua Role</option>
            <option value="bkhit"     {{ request('role')=='bkhit'     ? 'selected':'' }}>🟢 BKHIT</option>
            <option value="bbkhit"    {{ request('role')=='bbkhit'    ? 'selected':'' }}>🟡 BBKHIT</option>
            <option value="pusat"     {{ request('role')=='pusat'     ? 'selected':'' }}>🟣 Pusat</option>
            @if(auth()->user()->isDeveloper())
            <option value="developer" {{ request('role')=='developer' ? 'selected':'' }}>🔴 Developer</option>
            @endif
        </select>
        @if(request('search') || request('role'))
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-x me-1"></i>Reset
            </a>
        @endif
    </form>
</div>

{{-- ─── Tabel ───────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-blue text-white rounded-2 d-flex align-items-center justify-content-center"
                 style="width:32px;height:32px;">
                <i class="ti ti-users" style="font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Daftar Pengguna Terdaftar</div>
                <div class="text-muted small">{{ $users->count() }} pengguna ditampilkan</div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Nama / Instansi</th>
                    <th>Email</th>
                    <th style="width:110px;">Role</th>
                    <th>Koordinator</th>
                    <th style="width:110px;">Bergabung</th>
                    <th style="width:160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Apply filter
                    $filtered = $users;
                    if (request('search')) {
                        $q = strtolower(request('search'));
                        $filtered = $filtered->filter(fn($u) =>
                            str_contains(strtolower($u->name), $q) ||
                            str_contains(strtolower($u->email), $q) ||
                            str_contains(strtolower($u->upt_asal ?? ''), $q)
                        );
                    }
                    if (request('role')) {
                        $filtered = $filtered->where('role', request('role'));
                    }
                @endphp
                @forelse($filtered->values() as $i => $u)
                <tr>
                    <td class="text-muted text-center">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm"
                                style="background: linear-gradient(135deg,
                                    {{ $u->role === 'bkhit' ? '#16a34a,#22c55e' : ($u->role === 'bbkhit' ? '#ca8a04,#eab308' : '#7c3aed,#a78bfa') }});">
                                {{ strtoupper(substr($u->upt_asal ?? $u->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $u->upt_asal ?? $u->name }}</div>
                                @if($u->name)
                                    <div class="text-muted small">{{ $u->name }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $u->email }}</td>
                    <td>
                        @if($u->role === 'developer')
                            <span class="badge px-2" style="background:#fee2e2;color:#dc2626;">🔴 Developer</span>
                        @elseif($u->role === 'bkhit')
                            <span class="badge bg-success-lt text-success px-2">🟢 BKHIT</span>
                        @elseif($u->role === 'bbkhit')
                            <span class="badge bg-warning-lt text-warning px-2">🟡 BBKHIT</span>
                        @else
                            <span class="badge bg-purple-lt text-purple px-2">🟣 Pusat</span>
                        @endif
                    </td>
                    <td>
                        @if($u->role === 'bkhit')
                            @if($u->coordinator)
                                <div class="small fw-semibold text-primary">
                                    <i class="ti ti-link me-1"></i>{{ $u->coordinator->upt_asal ?? $u->coordinator->name }}
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Tanpa koordinator</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            {{-- Developer tidak bisa diedit/dihapus dari UI --}}
                            @if($u->role !== 'developer')
                                @if($u->role !== 'pusat' || auth()->user()->isDeveloper())
                                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-outline-secondary btn-icon" title="Edit data">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                @endif
                            @endif
                            @if($u->role !== 'developer')
                            <button class="btn btn-sm btn-outline-primary btn-icon" title="Reset password"
                                data-bs-toggle="modal" data-bs-target="#resetModal{{ $u->id }}">
                                <i class="ti ti-key"></i>
                            </button>
                            @endif
                            @if($u->role !== 'pusat' && $u->role !== 'developer')
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus akun"
                                onclick="confirmAction(
                                    '{{ route('users.destroy', $u->id) }}',
                                    'Akun &quot;{{ $u->upt_asal ?? $u->name }}&quot; akan dihapus permanen.',
                                    'DELETE', 'btn-danger'
                                )">
                                <i class="ti ti-trash"></i>
                            </button>
                            @elseif($u->role === 'pusat' && auth()->user()->isDeveloper())
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus akun Pusat"
                                onclick="confirmAction(
                                    '{{ route('users.destroy', $u->id) }}',
                                    'Akun Admin Pusat &quot;{{ $u->name }}&quot; akan dihapus permanen.',
                                    'DELETE', 'btn-danger'
                                )">
                                <i class="ti ti-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>


                @empty
                <tr>
                    <td colspan="7" class="p-0">
                        <div class="empty-state">
                            <div class="empty-state-icon">👥</div>
                            <h4>Belum Ada Pengguna</h4>
                            <p>Tambahkan akun BKHIT atau BBKHIT menggunakan tombol di atas.</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="ti ti-user-plus me-1"></i>Tambah Akun Baru
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
{{-- Render semua Modal Reset Password di luar tabel agar struktur HTML valid dan javascript/form bekerja --}}
@foreach($filtered->values() as $u)
    <div class="modal modal-blur fade" id="resetModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary-lt border-0">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="ti ti-key me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.reset-password', $u->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body pt-3 pb-4">
                        <p class="text-muted small mb-4 text-center">
                            Atur ulang kata sandi untuk <b>{{ $u->upt_asal ?? $u->name }}</b>
                        </p>
                        <div class="mb-3">
                            <label class="form-label required fw-bold small">Ketik Password Baru</label>
                            <div class="input-group input-group-flat shadow-none border rounded-3 overflow-hidden">
                                <input type="password" id="rp_pw_{{ $u->id }}" name="password"
                                        class="form-control border-0 ps-3 py-2" required minlength="8" placeholder="Minimal 8 karakter">
                                <span class="input-group-text bg-white border-0">
                                    <button type="button" class="btn btn-link text-muted p-0 border-0 text-decoration-none shadow-none"
                                            onclick="togglePw('rp_pw_{{ $u->id }}','rp_eye_{{ $u->id }}')">
                                        <i class="ti ti-eye fs-3" id="rp_eye_{{ $u->id }}"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="form-label required fw-bold small">Ulangi Password Baru</label>
                            <div class="input-group input-group-flat shadow-none border rounded-3 overflow-hidden">
                                <input type="password" id="rp_conf_{{ $u->id }}" name="password_confirmation"
                                        class="form-control border-0 ps-3 py-2" required placeholder="Ulangi password di atas">
                                <span class="input-group-text bg-white border-0">
                                    <button type="button" class="btn btn-link text-muted p-0 border-0 text-decoration-none shadow-none"
                                            onclick="togglePw('rp_conf_{{ $u->id }}','rp_ceye_{{ $u->id }}')">
                                        <i class="ti ti-eye fs-3" id="rp_ceye_{{ $u->id }}"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 d-flex justify-content-between py-3">
                        <button type="button" class="btn btn-ghost-danger btn-sm px-3" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm hover-scale transition-all">
                            <i class="ti ti-device-floppy me-1"></i>Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endpush

@endsection

@push('scripts')
<script>
function togglePw(inputId, iconId) {
    const el = document.getElementById(inputId);
    const ic = document.getElementById(iconId);
    if (!el || !ic) return;
    if (el.type === 'password') {
        el.type = 'text';
        ic.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        el.type = 'password';
        ic.classList.replace('ti-eye-off', 'ti-eye');
    }
}
</script>
@endpush
