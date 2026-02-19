@extends('layouts.app')

@section('title', 'Pelaksanaan')
@section('page_title', 'Modul Pelaksanaan')
@section('page_subtitle', 'Data realisasi lapangan pemantauan HPIK')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Wilayah / Komoditas</th>
                    <th>Lokasi Sampling</th>
                    <th>Sampel</th>
                    <th>Koordinat GPS</th>
                    <th>Status Lab</th>
                    <th>Tanggal</th>
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
                    <td>{{ $item->lokasi_pengambilan_sampel }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->jumlah_sampel }}</div>
                        <div class="text-muted small">{{ $item->metode_pengambilan_sampel }}</div>
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
                            <span class="badge bg-success-lt text-success">✅ {{ $item->laboratorium->hasil_uji }}</span>
                        @else
                            <span class="badge bg-warning-lt text-warning">⏳ Belum</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="ti ti-map-pin" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">Belum ada data pelaksanaan lapangan.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection