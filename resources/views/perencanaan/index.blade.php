@extends('layouts.app')

@section('title', 'Perencanaan')
@section('page_title', 'Modul Perencanaan')
@section('page_subtitle', 'Daftar rencana pemantauan HPIK')

@section('page_actions')
    @if(Auth::user()->isUpt())
        <a href="{{ route('perencanaan.create') }}" class="btn btn-primary d-none d-sm-inline-flex">
            <i class="ti ti-plus me-1"></i> Perencanaan Baru
        </a>
    @endif
@endsection

@section('content')
<div class="card">
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
                        @if($p->status === 'draft')
                            <span class="badge bg-secondary-lt text-secondary">Draft</span>
                        @elseif($p->status === 'waiting')
                            <span class="badge bg-warning-lt text-warning">Menunggu Validasi</span>
                        @else
                            <span class="badge bg-success-lt text-success">Disetujui</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            {{-- UPT: Ajukan / Input Lapangan --}}
                            @if(Auth::user()->isUpt())
                                @if($p->status === 'draft')
                                    <form action="{{ route('perencanaan.submit', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning" onclick="return confirm('Ajukan perencanaan ini untuk validasi?')">
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
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Setujui perencanaan ini?')">
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
                        <i class="ti ti-clipboard-list" style="font-size:2rem; opacity:.3;"></i>
                        <div class="mt-2">Belum ada data perencanaan.</div>
                        @if(Auth::user()->isUpt())
                            <a href="{{ route('perencanaan.create') }}" class="btn btn-primary btn-sm mt-2">Buat Perencanaan</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
