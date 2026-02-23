@extends('layouts.app')

@section('title', 'Perencanaan')
@section('page_title', 'Modul Perencanaan')
@section('page_subtitle', 'Daftar rencana pemantauan HPIK')

@section('page_actions')
    @if(Auth::user()->isBkhit())
        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary d-none d-sm-inline-flex">
            <i class="ti ti-plus me-1"></i> Perencanaan Baru
        </a>
    @endif
@endsection

@section('content')

{{-- Search & Filter Bar --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('perencanaan.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari provinsi, kota, jenis MP, HPIK..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    {{-- #8 Filter Tahun --}}
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Menunggu Validasi</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>Filter</button>
                </div>
                @if(request('search') || request('status') || request('tahun'))
                <div class="col-md-2">
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-secondary w-100"><i class="ti ti-x me-1"></i>Reset</a>
                </div>
                @endif
            </div>
        </form>

    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title"><i class="ti ti-clipboard-list me-2"></i>Daftar Perencanaan</h3>
        <span class="badge bg-blue-lt ms-2">{{ $perencanaans->count() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-mobile-md card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Wilayah</th>
                    <th>Jenis MP / HPIK</th>
                    <th>Target Uji</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perencanaans as $key => $p)
                <tr>
                    <td class="text-muted">{{ $key + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $p->kab_kota }}</div>
                        <div class="text-muted small">{{ $p->provinsi }}</div>
                    </td>
                    <td>
                        <div>{{ $p->jenis_mp }}</div>
                        <div class="text-muted small">{{ $p->jenis_hpik }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $p->target_uji }}</div>
                        <div class="text-muted small">TW: {{ $p->tw1 }}/{{ $p->tw2 }}/{{ $p->tw3 }}/{{ $p->tw4 }}</div>
                    </td>
                    <td>
                        @php
                            $statusMap = [
                                'draft'    => ['label'=>'Draft',              'class'=>'bg-secondary-lt text-secondary', 'dot'=>'bg-secondary'],
                                'waiting'  => ['label'=>'Menunggu Validasi',  'class'=>'bg-warning-lt text-warning',    'dot'=>'bg-warning'],
                                'approved' => ['label'=>'Disetujui',          'class'=>'bg-success-lt text-success',    'dot'=>'bg-success'],
                            ];
                            $s = $statusMap[$p->status] ?? $statusMap['draft'];
                        @endphp
                        <span class="badge {{ $s['class'] }}">
                            <span class="badge-dot {{ $s['dot'] }}"></span>
                            {{ $s['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            {{-- BKHIT: Draft actions (Edit · Hapus · Ajukan) --}}
                            @if(Auth::user()->isBkhit())
                                @if($p->status === 'draft' && $p->user_id === Auth::id())
                                    {{-- Edit --}}
                                    <a href="{{ route('perencanaan.edit', $p->id) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    {{-- Hapus --}}
                                    <form action="{{ route('perencanaan.destroy', $p->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="return confirm('Yakin hapus perencanaan ini?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    {{-- Ajukan --}}
                                    <form action="{{ route('perencanaan.submit', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning"
                                            onclick="return confirm('Ajukan perencanaan ini untuk validasi?')">
                                            <i class="ti ti-send me-1"></i>Ajukan
                                        </button>
                                    </form>
                                @elseif($p->status === 'approved')
                                    <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-plus me-1"></i>Input Lapangan
                                    </a>
                                @endif
                            @endif

                            {{-- BBKHIT/Pusat: Setujui / Evaluasi --}}
                            @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                                @if($p->status === 'waiting')
                                    <form action="{{ route('perencanaan.approve', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success"
                                            onclick="return confirm('Setujui perencanaan ini?')">
                                            <i class="ti ti-check me-1"></i>Setujui
                                        </button>
                                    </form>
                                @elseif($p->status === 'approved' && !$p->evaluasi)
                                    <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-orange">
                                        <i class="ti ti-chart-bar me-1"></i>Evaluasi
                                    </a>
                                @elseif($p->evaluasi)
                                    <span class="badge bg-green-lt">✅ Selesai Evaluasi</span>
                                @endif
                            @endif
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="ti ti-clipboard-list" style="font-size:2.5rem; opacity:.2;"></i>
                        <div class="mt-2 fw-semibold">Belum ada data perencanaan</div>
                        <div class="text-muted small mb-3">@if(request('search') || request('status'))Tidak ada hasil yang cocok dengan filter.@else Belum ada perencanaan yang dibuat.@endif</div>
                        @if(Auth::user()->isBkhit())
                            <a href="{{ route('perencanaan.create') }}" class="btn btn-primary btn-sm"><i class="ti ti-plus me-1"></i>Buat Perencanaan</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($perencanaans->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Menampilkan <span class="fw-semibold">{{ $perencanaans->firstItem() }}–{{ $perencanaans->lastItem() }}</span>
            dari <span class="fw-semibold">{{ $perencanaans->total() }}</span> data
        </p>
        <ul class="pagination m-0 ms-auto">
            {{-- Prev --}}
            <li class="page-item {{ $perencanaans->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $perencanaans->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18"/></svg>
                    Sebelumnya
                </a>
            </li>
            {{-- Page numbers --}}
            @foreach($perencanaans->getUrlRange(max(1,$perencanaans->currentPage()-2), min($perencanaans->lastPage(),$perencanaans->currentPage()+2)) as $page => $url)
            <li class="page-item {{ $page === $perencanaans->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
            @endforeach
            {{-- Next --}}
            <li class="page-item {{ !$perencanaans->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $perencanaans->nextPageUrl() }}">
                    Berikutnya
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18"/></svg>
                </a>
            </li>
        </ul>
    </div>
    @endif
</div>
@endsection
