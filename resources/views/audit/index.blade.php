@extends('layouts.app')

@section('title', 'Riwayat Aktivitas Sistem')

@section('content')
<div class="animate-fade-in px-2">
    {{-- Header --}}
    <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-indigo border-5">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-4">
                <div class="bg-indigo text-white p-4 rounded-4 shadow-sm">
                    <i class="ti ti-activity fs-1"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-indigo-lt text-indigo px-3 fs-6 rounded-pill">MODUL PENGATURAN</span>
                    </div>
                    <h1 class="display-5 fw-bolder text-dark mb-1 tracking-tight">Riwayat Aktivitas (Audit Log)</h1>
                    <div class="text-muted d-flex align-items-center">
                        <p class="mb-0">Melacak perubahan status, aksi penghapusan, dan persetujuan oleh seluruh pengguna.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Data --}}
    <div class="card card-premium shadow-sm border-0 bg-white">
        <div class="card-header bg-transparent border-bottom px-4 pt-4 pb-3 mb-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest"><i class="ti ti-history me-2"></i> Log Aktivitas Terakhir</h3>
        </div>
        <div class="card-body px-4 pb-4 pt-0">
            <form action="{{ route('audit.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="user_id" class="form-select">
                            <option value="">-- Semua Pengguna --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ strtoupper($user->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="action" class="form-select">
                            <option value="">-- Semua Aksi --</option>
                            <option value="Submit" {{ request('action') == 'Submit' ? 'selected' : '' }}>Submit Validasi</option>
                            <option value="Approve" {{ request('action') == 'Approve' ? 'selected' : '' }}>Approve / Setujui</option>
                            <option value="Reject" {{ request('action') == 'Reject' ? 'selected' : '' }}>Reject / Tolak</option>
                            <option value="Hapus" {{ request('action') == 'Hapus' ? 'selected' : '' }}>Hapus Data</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-2"></i>Filter</button>
                    </div>
                    @if(request()->filled('action') || request()->filled('user_id'))
                        <div class="col-md-2">
                            <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Aktor / Pengguna</th>
                            <th>Tipe Aksi</th>
                            <th>Modul Data</th>
                            <th>Keterangan Tambahan</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-muted small">
                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="fw-bold">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-xs bg-primary text-white rounded-circle shadow-sm">{{ substr(optional($log->user)->name ?? 'A', 0, 1) }}</span>
                                    <div>
                                        <div class="fw-bold text-dark">{{ optional($log->user)->name ?? 'Sistem' }}</div>
                                        <div class="text-muted small" style="font-size:0.65rem;">{{ strtoupper(optional($log->user)->role ?? 'System') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $aColor = match($log->action) {
                                        'Approve' => 'success',
                                        'Reject' => 'danger',
                                        'Submit' => 'warning',
                                        'Hapus' => 'danger',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $aColor }}-lt text-{{ $aColor }} px-3 py-1 fw-bold">{{ $log->action }}</span>
                            </td>
                            <td class="fw-semibold">{{ $log->model ?? '-' }}</td>
                            <td>
                                <div class="text-muted small">
                                    @if($log->old_value)
                                        <div class="mb-1"><span class="badge bg-secondary">Lama:</span> <code class="bg-light px-1 border">{{ substr($log->old_value, 0, 50) }}...</code></div>
                                    @endif
                                    @if($log->new_value)
                                        <div><span class="badge bg-info">Update:</span> <code class="bg-light px-1 border">{{ substr($log->new_value, 0, 50) }}...</code></div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-muted fw-mono small">{{ $log->ip ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ti ti-folder-off fs-2 d-block mb-2"></i>
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
