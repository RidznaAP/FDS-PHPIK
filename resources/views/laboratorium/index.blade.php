@extends('layouts.app')

@section('title', 'Laboratorium')
@section('page_title', 'Modul Laboratorium')
@section('page_subtitle', 'Daftar sampel dan status pengujian laboratorium')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Lokasi Sampling</th>
                    <th>Komoditas</th>
                    <th>Jumlah Sampel</th>
                    <th>Tanggal Input</th>
                    <th>Status Lab</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td class="text-muted">{{ $key + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->lokasi_pengambilan_sampel }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                    </td>
                    <td>{{ $item->perencanaan->jenis_mp ?? '-' }}</td>
                    <td>{{ $item->jumlah_sampel }} ekor</td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
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
</div>
@endsection
