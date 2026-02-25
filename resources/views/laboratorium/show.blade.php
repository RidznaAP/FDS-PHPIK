@extends('layouts.app')

@section('title', 'Detail Hasil Laboratorium')
@section('page_title', 'Detail Hasil Laboratorium')
@section('page_subtitle', 'Kode Sampel: ' . $lab->kode_sampel)

@section('page_actions')
<a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-1"></i>Kembali
</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        {{-- Card: Info Lab Utama --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title"><i class="ti ti-test-pipe me-2"></i>Data Pengujian Real-Time</h3>
                @php
                    $badgeRes = match($lab->hasil_uji) {
                        'Positif' => 'bg-danger text-white',
                        'Negatif' => 'bg-success text-white',
                        default   => 'bg-secondary text-white',
                    };
                @endphp
                <span class="badge {{ $badgeRes }} fs-5 px-3 py-1">{{ $lab->hasil_uji }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small font-weight-bold">Diagnosis Akhir</div>
                        <div class="h3 text-primary">{{ $lab->diagnosis_akhir ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Target HPIK Diuji</div>
                        <div class="h4">{{ $lab->jenis_hpik_diuji ?? '-' }}</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="text-muted small">Kode Sampel</div>
                        <div class="fw-semibold">{{ $lab->kode_sampel }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Metode Uji</div>
                        <div class="fw-semibold">{{ $lab->metode_uji }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Lab Penguji</div>
                        <div class="fw-semibold">{{ $lab->lab_penguji }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Tanggal Uji</div>
                        <div class="fw-semibold">{{ $lab->tanggal_uji->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Tanggal Hasil</div>
                        <div class="fw-semibold">{{ $lab->tanggal_hasil ? $lab->tanggal_hasil->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Breakdown Patogen --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title"><i class="ti ti-virus me-2"></i>Breakdown Hasil Patogen</h3></div>
            <div class="card-body p-0">
                <table class="table table-vcenter table-nowrap card-table">
                    <thead>
                        <tr><th>Patogen</th><th class="text-center">Hasil (+ / - / NT)</th></tr>
                    </thead>
                    <tbody>
                        @foreach(['Virus' => 'hasil_virus', 'Bakteri' => 'hasil_bakteri', 'Parasit' => 'hasil_parasit', 'Jamur' => 'hasil_jamur'] as $label => $field)
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="text-center">
                                @php $val = $lab->$field ?? 'NT'; @endphp
                                <span class="badge {{ $val == '+' ? 'bg-danger text-white' : ($val == '-' ? 'bg-success text-white' : 'bg-secondary-lt text-muted') }}" style="width:30px;">
                                    {{ $val }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card: Statistik Sampel Lab --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="ti ti-chart-line me-2"></i>Statistik Sampel</h3></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded d-flex justify-content-between">
                            <span>Jml Diperiksa</span>
                            <span class="fw-bold">{{ $lab->jumlah_sampel_diperiksa ?? '-' }} ekor</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded d-flex justify-content-between">
                            <span>Jml Terinfeksi</span>
                            <span class="fw-bold">{{ $lab->jumlah_ikan_terinfeksi ?? '-' }} ekor</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-blue-lt rounded d-flex justify-content-between">
                            <span>Prevalensi</span>
                            <span class="fw-bold text-blue">{{ $lab->prevalensi ?? '-' }}%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-azure-lt rounded d-flex justify-content-between">
                            <span>Insidensi</span>
                            <span class="fw-bold text-azure">{{ $lab->insidensi ?? '-' }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Link Pelaksanaan --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Data Lapangan Asal</h3></div>
            <div class="card-body">
                <div class="text-muted small">Lokasi Sampling</div>
                <div class="fw-semibold mb-2">{{ $lab->pelaksanaan->lokasi_pengambilan_sampel }}</div>
                
                <div class="text-muted small">Komoditas</div>
                <div class="fw-semibold mb-2">{{ $lab->pelaksanaan->jenis_ikan }}</div>
                
                <a href="{{ route('pelaksanaan.show', $lab->pelaksanaan_id) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="ti ti-eye me-1"></i>Lihat Rincian Lapangan
                </a>
            </div>
        </div>

        {{-- Tambahan Info --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Info Teknis</h3></div>
            <div class="card-body">
                <div class="text-muted small">Jumlah Kolam Uji</div>
                <div class="fw-semibold mb-2">{{ $lab->jumlah_kolam_uji ?? '-' }} unit</div>
                
                <div class="text-muted small">Periode Pengamatan</div>
                <div class="fw-semibold mb-0">{{ $lab->periode_pengamatan ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
