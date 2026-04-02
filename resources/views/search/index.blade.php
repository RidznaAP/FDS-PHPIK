@extends('layouts.app')

@section('title', 'Pencarian Global')
@section('page_title', 'Hasil Pencarian')
@section('page_subtitle', 'Mencari "'. $q .'" di semua modul')

@section('content')
<div class="container-xl px-0 animate-fade-in">
    
    <div class="row g-4">
        {{-- Section: Perencanaan --}}
        <div class="col-12">
            <div class="card card-premium shadow-sm">
                <div class="card-header bg-transparent border-bottom px-4 pt-4 pb-3">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest d-flex align-items-center">
                        <i class="ti ti-map-2 me-2" style="font-size: 1.25rem;"></i> Perencanaan ({{ count($perencanaans) }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if(count($perencanaans) > 0)
                        <div class="list-group list-group-flush border-0">
                            @foreach($perencanaans as $p)
                                <a href="{{ route('perencanaan.show', $p->id) }}" class="list-group-item list-group-item-action py-3 px-4 border-bottom border-light">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">{{ $p->jenis_mp }} <span class="text-muted fw-normal mx-2">•</span> {{ $p->kab_kota }}, {{ $p->provinsi }}</h4>
                                            <p class="mb-0 text-muted small">Target: {{ $p->target_uji }} | HPIK: {{ $p->jenis_hpik }}</p>
                                        </div>
                                        <span class="badge {{ $p->status === 'approved' ? 'bg-success-lt text-success' : ($p->status === 'draft' ? 'bg-secondary-lt text-secondary' : 'bg-warning-lt text-warning') }}">{{ $p->status }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">Tidak ditemukan data Perencanaan yang cocok.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section: Pelaksanaan --}}
        <div class="col-12">
            <div class="card card-premium shadow-sm">
                <div class="card-header bg-transparent border-bottom px-4 pt-4 pb-3">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest d-flex align-items-center">
                        <i class="ti ti-map-pin me-2" style="font-size: 1.25rem;"></i> Pelaksanaan ({{ count($pelaksanaans) }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if(count($pelaksanaans) > 0)
                        <div class="list-group list-group-flush border-0">
                            @foreach($pelaksanaans as $p)
                                <a href="{{ route('pelaksanaan.show', $p->id) }}" class="list-group-item list-group-item-action py-3 px-4 border-bottom border-light">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">{{ $p->lokasi_pengambilan_sampel }}</h4>
                                            <p class="mb-0 text-muted small">Komoditas: {{ $p->jenis_ikan }} | Sampel: {{ $p->jumlah_sampel }}</p>
                                        </div>
                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($p->tanggal_pemantauan)->format('d M Y') }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">Tidak ditemukan data Pelaksanaan lapangan yang cocok.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section: Laboratorium --}}
        <div class="col-12">
            <div class="card card-premium shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom px-4 pt-4 pb-3">
                    <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest d-flex align-items-center">
                        <i class="ti ti-flask me-2" style="font-size: 1.25rem;"></i> Uji Laboratorium ({{ count($laboratoriums) }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if(count($laboratoriums) > 0)
                        <div class="list-group list-group-flush border-0">
                            @foreach($laboratoriums as $lab)
                                <a href="{{ route('laboratorium.show', $lab->id) }}" class="list-group-item list-group-item-action py-3 px-4 border-bottom border-light">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">Uji: {{ $lab->metode_uji ?? '-' }}</h4>
                                            <p class="mb-0 text-muted small">Lokasi Sampel: {{ $lab->pelaksanaan->lokasi_pengambilan_sampel ?? '-' }} | Hasil: {{ $lab->hasil_uji }}</p>
                                        </div>
                                        <span class="badge {{ $lab->hasil_uji === 'NIHIL' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger fw-bold' }}">{{ $lab->hasil_uji }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">Tidak ditemukan hasil uji Laboratorium yang cocok.</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
