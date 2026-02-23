@extends('layouts.app')

@section('title', 'Laboratorium')
@section('page_title', 'Modul Laboratorium')
@section('page_subtitle', 'Daftar sampel dan status pengujian laboratorium')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title"><i class="ti ti-flask me-2"></i>Data Sampel Laboratorium</h3>
        <span class="badge bg-blue-lt ms-2">{{ $pelaksanaans->total() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Lokasi Sampling</th>
                    <th>Komoditas</th>
                    <th>Jml Sampel</th>
                    <th>Tanggal Uji</th>
                    <th>Parasit</th>
                    <th>Bakteri</th>
                    <th>Virus</th>
                    <th>Jamur</th>
                    <th>Prev%</th>
                    <th>Status Lab</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td class="text-muted">{{ $pelaksanaans->firstItem() + $key }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->lokasi_pengambilan_sampel }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                    </td>
                    <td>{{ $item->perencanaan->jenis_mp ?? '-' }}</td>
                    <td>{{ $item->jumlah_sampel }} ekor</td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
                    @php
                        $patogenBadge = [
                            '+' => 'bg-danger-lt text-danger',
                            '-' => 'bg-success-lt text-success',
                            'NT' => 'bg-secondary-lt text-secondary',
                        ];
                    @endphp
                    @foreach(['hasil_parasit','hasil_bakteri','hasil_virus','hasil_jamur'] as $f)
                    <td>
                        @if($item->laboratorium)
                            @php $val = $item->laboratorium->$f ?? 'NT'; @endphp
                            <span class="badge {{ $patogenBadge[$val] ?? 'bg-secondary-lt' }}">{{ $val }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-muted small">
                        @if($item->laboratorium && $item->laboratorium->prevalensi !== null)
                            {{ $item->laboratorium->prevalensi }}%
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($item->laboratorium)
                            <span class="badge bg-success-lt text-success">
                                <i class="ti ti-check me-1"></i>{{ $item->laboratorium->hasil_uji }}
                            </span>
                        @else
                            <span class="badge bg-warning-lt text-warning">
                                <i class="ti ti-clock me-1"></i>Menunggu
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(!$item->laboratorium)
                            <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-flask me-1"></i>Input Hasil
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="ti ti-flask" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">Belum ada data sampel masuk.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pelaksanaans->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Menampilkan <span class="fw-semibold">{{ $pelaksanaans->firstItem() }}–{{ $pelaksanaans->lastItem() }}</span>
            dari <span class="fw-semibold">{{ $pelaksanaans->total() }}</span> data
        </p>
        <ul class="pagination m-0 ms-auto">
            <li class="page-item {{ $pelaksanaans->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $pelaksanaans->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18"/></svg>
                    Sebelumnya
                </a>
            </li>
            @foreach($pelaksanaans->getUrlRange(max(1,$pelaksanaans->currentPage()-2), min($pelaksanaans->lastPage(),$pelaksanaans->currentPage()+2)) as $page => $url)
            <li class="page-item {{ $page === $pelaksanaans->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
            @endforeach
            <li class="page-item {{ !$pelaksanaans->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $pelaksanaans->nextPageUrl() }}">
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
