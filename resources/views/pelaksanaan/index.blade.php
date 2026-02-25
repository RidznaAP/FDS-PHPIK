@extends('layouts.app')

@section('title', 'Pelaksanaan')
@section('page_title', 'Modul Pelaksanaan')
@section('page_subtitle', 'Data realisasi lapangan pemantauan HPIK')

@section('content')

{{-- Search & Filter Bar --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('pelaksanaan.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari lokasi, jenis ikan, provinsi..." value="{{ request('search') }}">
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
                    <select name="lab" class="form-select">
                        <option value="">Semua Status Lab</option>
                        <option value="done" {{ request('lab') == 'done' ? 'selected' : '' }}>✅ Sudah Diuji</option>
                        <option value="pending" {{ request('lab') == 'pending' ? 'selected' : '' }}>⏳ Belum Diuji</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>Filter</button>
                </div>
                @if(request('search') || request('lab') || request('tahun'))
                <div class="col-md-2">
                    <a href="{{ route('pelaksanaan.index') }}" class="btn btn-outline-secondary w-100"><i class="ti ti-x me-1"></i>Reset</a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title"><i class="ti ti-map-pin me-2"></i>Data Pelaksanaan Lapangan</h3>
        <span class="badge bg-blue-lt ms-2">{{ $pelaksanaans->count() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Wilayah / Komoditas</th>
                    <th>Jenis Ikan</th>
                    <th>Lokasi & Tanggal</th>
                    <th>Sampel</th>
                    <th>Koordinat GPS</th>
                    <th>Status Lab</th>
                    <th>Tgl Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td class="text-muted">{{ $key + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->perencanaan->jenis_mp ?? '-' }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                    </td>
                    <td>
                        @if($item->jenis_ikan)
                            <div class="fw-semibold">{{ $item->jenis_ikan }}</div>
                            @if($item->nama_latin)<div class="text-muted small fst-italic">{{ $item->nama_latin }}</div>@endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $item->lokasi_pengambilan_sampel }}</div>
                        @if($item->tanggal_pemantauan)
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->jumlah_sampel }} ekor</div>
                        <div class="text-muted small">{{ $item->metode_pengambilan_sampel }}</div>
                        @if($item->jumlah_kematian > 0)
                            <span class="badge bg-danger-lt text-danger">Mati: {{ $item->jumlah_kematian }}</span>
                        @endif
                    </td>
                    <td>
                        @if($item->latitude && $item->longitude)
                            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-decoration-none">
                                <span class="badge bg-azure-lt">
                                    <i class="ti ti-map-pin me-1"></i>{{ number_format($item->latitude,4) }}, {{ number_format($item->longitude,4) }}
                                </span>
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        @if($item->laboratorium)
                            <span class="badge bg-success-lt text-success">
                                <span class="badge-dot bg-success"></span>
                                {{ $item->laboratorium->hasil_uji }}
                            </span>
                        @else
                            <span class="badge bg-warning-lt text-warning">
                                <span class="badge-dot bg-warning"></span>
                                Belum Diuji
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="ti ti-info-circle"></i>
                            </a>
                            @if($item->laboratorium)
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Hasil Lab">
                                    <i class="ti ti-eye me-1"></i>Lihat Lab
                                </a>
                            @else
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-flask me-1"></i>Input Lab
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="ti ti-map-pin" style="font-size:2.5rem;opacity:.2;"></i>
                        <div class="mt-2 fw-semibold">Belum ada data pelaksanaan</div>
                        <div class="text-muted small">@if(request('search') || request('lab'))Tidak ada hasil sesuai filter.@else Belum ada input data lapangan.@endif</div>
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