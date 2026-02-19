@extends('layouts.app')

@section('title', 'Evaluasi')
@section('page_title', 'Modul Evaluasi')
@section('page_subtitle', 'Penetapan status akhir hasil pemantauan HPIK')

@section('content')
<div class="card">
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
                        @if(!$p->evaluasi && $p->status === 'approved')
                            <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-warning">
                                <i class="ti ti-chart-bar me-1"></i>Evaluasi
                            </a>
                        @elseif($p->evaluasi)
                            <span class="text-muted small">—</span>
                        @else
                            <span class="badge bg-secondary-lt text-muted small">Belum Disetujui</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="ti ti-chart-bar" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">Belum ada data untuk dievaluasi.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
