@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page_title', 'Manajemen Akun Pengguna')
@section('page_subtitle', 'Kelola akun BKHIT dan BBKHIT')

@section('page_actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus me-1"></i>Buat Akun Baru
    </a>
@endsection

@section('content')

{{-- Ringkasan --}}
@php
    $totalUpt    = $users->where('role','bkhit')->count();
    $totalBbkhit = $users->where('role','bbkhit')->count();
    $totalPusat  = $users->where('role','pusat')->count();
@endphp
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar" style="background:#dcfce7;border-radius:10px;">
                    <i class="ti ti-building-community" style="color:#16a34a;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Admin BKHIT</div>
                    <div class="h3 mb-0">{{ $totalUpt }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar" style="background:#fef9c3;border-radius:10px;">
                    <i class="ti ti-building" style="color:#ca8a04;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Admin BBKHIT</div>
                    <div class="h3 mb-0">{{ $totalBbkhit }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar" style="background:#ede9fe;border-radius:10px;">
                    <i class="ti ti-shield-check" style="color:#7c3aed;font-size:1.4rem;"></i>
                </div>
                <div>
                    <div class="text-muted small">Admin Pusat</div>
                    <div class="h3 mb-0">{{ $totalPusat }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pengguna --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-users me-2"></i>Daftar Semua Pengguna</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama / Instansi</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm"
                                style="background: linear-gradient(135deg,
                                    {{ $u->role === 'bkhit' ? '#16a34a,#22c55e' : ($u->role === 'bbkhit' ? '#ca8a04,#eab308' : '#7c3aed,#a78bfa') }});">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $u->name }}</div>
                                @if($u->upt_asal)
                                    <div class="text-muted small">{{ $u->upt_asal }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $u->email }}</td>
                    <td>
                        @if($u->role === 'bkhit')
                            <span class="badge bg-success-lt text-success">BKHIT</span>
                        @elseif($u->role === 'bbkhit')
                            <span class="badge bg-warning-lt text-warning">BBKHIT</span>
                        @else
                            <span class="badge bg-purple-lt text-purple">PUSAT</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            {{-- Edit Pengguna (#5) --}}
                            @if($u->role !== 'pusat')
                            <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Data">
                                <i class="ti ti-pencil me-1"></i>Edit
                            </a>
                            @endif

                            {{-- Reset Password --}}
                            <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#resetModal{{ $u->id }}">
                                <i class="ti ti-key me-1"></i>Reset PW
                            </button>

                            {{-- Hapus (kecuali pusat) --}}
                            @if($u->role !== 'pusat')
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus akun {{ $u->name }}? Tindakan ini tidak bisa dibatalkan.')">
                                    <i class="ti ti-trash me-1"></i>Hapus
                                </button>
                            </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Modal Reset Password --}}
                <div class="modal modal-blur fade" id="resetModal{{ $u->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="ti ti-key me-2 text-primary"></i>Reset Password
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('users.reset-password', $u->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body">
                                    <div class="text-muted small mb-3">Atur password baru untuk <b>{{ $u->name }}</b></div>
                                    <div class="mb-2">
                                        <label class="form-label required">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" id="rp_pw_{{ $u->id }}" name="password"
                                                class="form-control" required minlength="8">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="togglePw('rp_pw_{{ $u->id }}','rp_eye_{{ $u->id }}')" tabindex="-1">
                                                <i class="ti ti-eye" id="rp_eye_{{ $u->id }}"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label required">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <input type="password" id="rp_conf_{{ $u->id }}" name="password_confirmation"
                                                class="form-control" required>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="togglePw('rp_conf_{{ $u->id }}','rp_ceye_{{ $u->id }}')" tabindex="-1">
                                                <i class="ti ti-eye" id="rp_ceye_{{ $u->id }}"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>Reset Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="ti ti-users" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">Belum ada pengguna terdaftar.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePw(inputId, iconId) {
    var el = document.getElementById(inputId);
    var ic = document.getElementById(iconId);
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
@endsection
