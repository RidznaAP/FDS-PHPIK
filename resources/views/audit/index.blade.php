@extends('layouts.app')

@section('title', 'Riwayat Aktivitas Sistem')

@section('styles')
<style>
/* ── Timeline Styles ──────────────────────────────── */
.timeline-modern {
    position: relative;
    padding: 20px 0;
}
.timeline-modern::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 40px;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    margin-bottom: 40px;
    padding-left: 80px;
}
.timeline-icon {
    position: absolute;
    left: 20px;
    top: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    z-index: 2;
    box-shadow: 0 0 0 4px #fff;
}
.timeline-content {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
}
.timeline-time {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 8px;
}
.timeline-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.timeline-user {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.diff-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    font-size: 0.85rem;
    margin-top: 12px;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
}
.diff-label {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}
@media (max-width: 768px) {
    .timeline-modern::before { left: 30px; }
    .timeline-item { padding-left: 60px; }
    .timeline-icon { left: 10px; width: 38px; height: 38px; }
}
</style>
@endsection

@section('content')
<div class="animate-fade-in px-2">
    {{-- Header --}}
    <div class="row align-items-center mb-4 g-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                    <i class="ti ti-history fs-1"></i>
                </div>
                <div>
                    <h1 class="h2 fw-bolder text-dark mb-0">Audit Log Visual Timeline</h1>
                    <p class="text-muted mb-0">Menampilkan jejak digital aktivitas sistem secara kronologis.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('audit.index') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="user_id" class="form-select border-0 bg-light">
                            <option value="">-- Semua Pengguna --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="action" class="form-select border-0 bg-light">
                            <option value="">-- Semua Aksi --</option>
                            <option value="Submit" {{ request('action') == 'Submit' ? 'selected' : '' }}>Submit</option>
                            <option value="Approve" {{ request('action') == 'Approve' ? 'selected' : '' }}>Approve</option>
                            <option value="Reject" {{ request('action') == 'Reject' ? 'selected' : '' }}>Reject</option>
                            <option value="Hapus" {{ request('action') == 'Hapus' ? 'selected' : '' }}>Hapus</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Filter</button>
                    </div>
                    @if(request()->filled('action') || request()->filled('user_id'))
                    <div class="col-md-1">
                        <a href="{{ route('audit.index') }}" class="btn btn-ghost-secondary w-100 rounded-pill">Reset</a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Timeline Container --}}
    <div class="timeline-modern">
        @forelse($logs as $log)
            @php
                $icon = 'ti ti-dots';
                $color = '#6366f1'; // Indigo
                
                if (stripos($log->action, 'Approve') !== false || stripos($log->action, 'Setujui') !== false) {
                    $icon = 'ti ti-check'; $color = '#22c55e';
                } elseif (stripos($log->action, 'Reject') !== false || stripos($log->action, 'Tolak') !== false) {
                    $icon = 'ti ti-x'; $color = '#ef4444';
                } elseif (stripos($log->action, 'Hapus') !== false) {
                    $icon = 'ti ti-trash'; $color = '#64748b';
                } elseif (stripos($log->action, 'Submit') !== false) {
                    $icon = 'ti ti-send'; $color = '#f59e0b';
                }
            @endphp
            <div class="timeline-item">
                <div class="timeline-icon" style="background: {{ $color }};">
                    <i class="{{ $icon }} fs-3"></i>
                </div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="timeline-time">
                                <i class="ti ti-calendar me-1"></i> {{ $log->created_at->translatedFormat('d F Y') }} 
                                <i class="ti ti-clock ms-2 me-1"></i> {{ $log->created_at->format('H:i') }} WIB
                            </div>
                            <div class="timeline-title">{{ $log->action }} pada Modul <span class="text-primary">{{ $log->model }}</span></div>
                        </div>
                        <div class="badge bg-light text-muted fw-mono small border">{{ $log->ip }}</div>
                    </div>

                    @if($log->old_value || $log->new_value)
                        <div class="row g-2">
                            @if($log->old_value)
                            <div class="col-md-6">
                                <div class="diff-box border-start border-danger border-4">
                                    <div class="diff-label text-danger">Data Sebelumnya</div>
                                    <div class="text-wrap" style="word-break: break-all;">{{ $log->old_value }}</div>
                                </div>
                            </div>
                            @endif
                            @if($log->new_value)
                            <div class="col-md-6">
                                <div class="diff-box border-start border-success border-4">
                                    <div class="diff-label text-success">Data Baru / Perubahan</div>
                                    <div class="text-wrap" style="word-break: break-all;">{{ $log->new_value }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif

                    <div class="timeline-user">
                        <span class="avatar avatar-sm bg-primary-lt text-primary rounded-circle">
                            {{ substr(optional($log->user)->name ?? 'S', 0, 1) }}
                        </span>
                        <div>
                            <div class="fw-bold text-dark small">{{ optional($log->user)->name ?? 'Sistem Otomatis' }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ strtoupper(optional($log->user)->role ?? 'System') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                    <i class="ti ti-ghost fs-1 text-muted"></i>
                </div>
                <h3 class="text-dark">Tidak ada rekaman aktivitas</h3>
                <p class="text-muted">Gunakan filter lain atau kembali lagi nanti.</p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
    <div class="mt-4 mb-5 d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
