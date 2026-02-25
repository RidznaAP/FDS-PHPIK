@extends('layouts.app')

@section('title', 'Evaluasi')
@section('page_title', 'Modul Evaluasi')
@section('page_subtitle', 'Penetapan status akhir hasil pemantauan HPIK')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Data Evaluasi HPIK</h3>
        <span class="badge bg-blue-lt ms-2">{{ $perencanaans->total() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Wilayah / Komoditas</th>
                    <th>Lab Selesai</th>
                    <th>Hasil Evaluasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perencanaans as $key => $p)
                <tr>
                    <td class="text-muted">{{ $key + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $p->kab_kota }}, {{ $p->provinsi }}</div>
                        <div class="text-muted small">{{ $p->jenis_mp }} — {{ $p->jenis_hpik }}</div>
                    </td>
                    <td>
                        @php
                            $selesai = $p->pelaksanaans->filter(fn($pl) => $pl->laboratorium !== null)->count();
                            $total = $p->pelaksanaans->count();
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-fill">
                                <div class="progress progress-sm">
                                    <div class="progress-bar {{ $selesai == $total && $total > 0 ? 'bg-success' : 'bg-yellow' }}"
                                         style="width: {{ $total > 0 ? ($selesai/$total*100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted small">{{ $selesai }}/{{ $total }}</div>
                        </div>
                    </td>
                    <td>
                        @if($p->evaluasi)
                            @php $w = $p->evaluasi->warna; @endphp
                            <span class="badge bg-{{ $w }}-lt text-{{ $w }}">
                                {{ $p->evaluasi->kesimpulan }}
                            </span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">Belum Dievaluasi</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            @if($p->evaluasi)
                                <a href="{{ route('evaluasi.show', $p->evaluasi->id) }}" class="btn btn-sm btn-outline-info" title="Detail Evaluasi">
                                    <i class="ti ti-chart-bar me-1"></i>Detail
                                </a>
                            @else
                                <a href="{{ route('perencanaan.show', $p->id) }}" class="btn btn-sm btn-outline-secondary" title="Detail Perencanaan">
                                    <i class="ti ti-eye me-1"></i>Rinci
                                </a>
                            @endif

                            @if(!$p->evaluasi && $p->status === 'approved' && (Auth::user()->isBbkhit() || Auth::user()->isPusat()))
                                <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-warning">
                                    <i class="ti ti-plus me-1"></i>Evaluasi
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="ti ti-chart-bar" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">Belum ada data untuk dievaluasi.</div>
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
            <li class="page-item {{ $perencanaans->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $perencanaans->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18"/></svg>
                    Sebelumnya
                </a>
            </li>
            @foreach($perencanaans->getUrlRange(max(1,$perencanaans->currentPage()-2), min($perencanaans->lastPage(),$perencanaans->currentPage()+2)) as $page => $url)
            <li class="page-item {{ $page === $perencanaans->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
            @endforeach
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
