@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')
@section('page_subtitle', 'Pemberitahuan aktivitas terbaru')

@section('page_actions')
    @if($notifikasis->total() > 0)
    <form action="{{ route('notifikasi.baca-semua') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-checks me-1"></i>Tandai Semua Dibaca
        </button>
    </form>
    @endif
@endsection

@section('content')

{{-- Filter Bar — pola sama dengan Laboratorium/Perencanaan --}}
<form method="GET" action="{{ route('notifikasi.index') }}">
    <div class="row g-2 mb-3">
        <div class="col">
            <div class="input-icon" style="max-width:400px;">
                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control"
                       placeholder="Cari judul atau isi notifikasi…"
                       value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-auto d-flex gap-2">
            <select name="dibaca" class="form-select" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="0" {{ request('dibaca') === '0' ? 'selected' : '' }}>Belum Dibaca</option>
                <option value="1" {{ request('dibaca') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-filter me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'dibaca']))
                <a href="{{ route('notifikasi.index') }}" class="btn btn-ghost-secondary" title="Reset filter">
                    <i class="ti ti-x"></i>
                </a>
            @endif
        </div>
    </div>
</form>

{{-- Card Notifikasi --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-orange text-white rounded-2 d-flex align-items-center justify-content-center"
                 style="width:32px;height:32px;">
                <i class="ti ti-bell" style="font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Semua Notifikasi</div>
                <div class="text-muted small">{{ $notifikasis->total() }} total pemberitahuan</div>
            </div>
        </div>
    </div>

    @if($notifikasis->isEmpty())
    <div class="card-body p-0">
        <div class="empty-state">
            <div class="empty-state-icon">🔔</div>
            <h4>Belum Ada Notifikasi</h4>
            <p>Notifikasi akan muncul ketika ada aktivitas terkait peran Anda di aplikasi.</p>
        </div>
    </div>
    @else
    <div class="list-group list-group-flush">
        @foreach($notifikasis as $notif)
        @php
            $iconMap = [
                'upload_pelaporan' => ['ti-file-upload',    'text-blue',   'bg-blue-lt'],
                'upload_evaluasi'  => ['ti-file-analytics', 'text-purple', 'bg-purple-lt'],
                'default'          => ['ti-bell',           'text-orange', 'bg-orange-lt'],
            ];
            [$icon, $textColor, $bgColor] = $iconMap[$notif->tipe] ?? $iconMap['default'];
        @endphp
        <div class="list-group-item px-4 py-3 {{ $notif->dibaca ? '' : 'bg-blue-lt' }}"
             style="{{ $notif->dibaca ? '' : 'border-left: 3px solid #3b82f6;' }}">
            <div class="d-flex align-items-start gap-3">
                {{-- Ikon tipe --}}
                <div class="{{ $bgColor }} rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;min-width:40px;">
                    <i class="ti {{ $icon }} {{ $textColor }}" style="font-size:1.2rem;"></i>
                </div>

                {{-- Konten --}}
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="fw-semibold {{ $notif->dibaca ? 'text-muted' : 'text-dark' }}">
                            {{ $notif->judul }}
                        </div>
                        @if(!$notif->dibaca)
                            <span class="badge bg-blue"
                                  style="width:8px;height:8px;padding:0;border-radius:50%;flex-shrink:0;"></span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1">{{ $notif->pesan }}</div>
                    <div class="d-flex align-items-center gap-3 mt-2">
                        <span class="text-muted" style="font-size:.75rem;">
                            <i class="ti ti-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                        </span>
                        @if($notif->dariUser)
                        <span class="text-muted" style="font-size:.75rem;">
                            <i class="ti ti-user me-1"></i>{{ $notif->dariUser->name }}
                        </span>
                        @endif
                        @if($notif->url)
                        <a href="{{ route('notifikasi.baca', $notif->id) }}"
                           class="text-primary small fw-semibold text-decoration-none">
                            Lihat Detail →
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Hapus --}}
                <form action="{{ route('notifikasi.hapus', $notif->id) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-ghost-secondary btn-icon"
                            title="Hapus notifikasi ini">
                        <i class="ti ti-x"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @if($notifikasis->hasPages())
    <div class="card-footer border-0 bg-transparent py-3">
        {{ $notifikasis->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
