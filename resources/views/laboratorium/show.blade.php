@extends('layouts.app')

@section('title', 'Detail Hasil Laboratorium')

@section('content')
<div class="row detail-header align-items-center">
    <div class="col">
        <div class="detail-subtitle">Modul Hasil Laboratorium</div>
        <h1 class="detail-title">Hasil Uji: {{ $lab->kode_sampel }}</h1>
        <div class="detail-subtitle">
            <i class="ti ti-test-pipe me-1"></i>{{ $lab->diagnosis_akhir ?? 'Diagnosis Belum Ditentukan' }}
        </div>
    </div>
    <div class="col-auto">
        <a href="{{ route('laboratorium.index') }}" class="btn btn-outline-secondary btn-pill shadow-sm">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Card: Info Lab Utama --}}
        <div class="card card-premium mb-4 overflow-hidden">
            <div class="card-header border-0 pb-0 shadow-none">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">
                    Data Pengujian Real-Time
                </h3>
                <div class="card-options">
                    @php
                        $badgeRes = match($lab->hasil_uji) {
                            'Positif' => 'bg-danger-lt text-danger',
                            'Negatif' => 'bg-success-lt text-success',
                            default   => 'bg-azure-lt text-azure',
                        };
                    @endphp
                    <span class="badge badge-premium {{ $badgeRes }}">{{ $lab->hasil_uji }}</span>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-microscope"></i></div>
                                <div class="info-content">
                                    <label>Diagnosis Akhir</label>
                                    <span class="h3 text-primary mb-0 fw-bold">{{ $lab->diagnosis_akhir ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon bg-azure-lt text-azure"><i class="ti ti-target"></i></div>
                                <div class="info-content">
                                    <label>HPIK Diuji</label>
                                    <span>{{ $lab->jenis_hpik_diuji ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon bg-green-lt text-green"><i class="ti ti-calendar-event"></i></div>
                                <div class="info-content">
                                    <label>Tanggal Pelaksanaan Uji</label>
                                    <span>{{ $lab->tanggal_uji->format('d F Y') }}</span>
                                    <div class="sub-text">Selesai: {{ $lab->tanggal_hasil ? $lab->tanggal_hasil->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon bg-red-lt text-red"><i class="ti ti-id"></i></div>
                                <div class="info-content">
                                    <label>Kode Sampel & Lab</label>
                                    <span>#{{ $lab->kode_sampel }}</span>
                                    <div class="sub-text">{{ $lab->lab_penguji }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row for Breakdown & Stats --}}
        <div class="row g-4">
            <div class="col-md-5">
                {{-- Card: Breakdown Patogen --}}
                <div class="card card-premium h-100 shadow-sm border-0">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Deteksi Patogen</h3>
                    </div>
                    <div class="card-body p-0 pt-3">
                        <table class="table table-vcenter card-table table-borderless">
                            <tbody>
                                @foreach(['Virus' => 'hasil_virus', 'Bakteri' => 'hasil_bakteri', 'Parasit' => 'hasil_parasit', 'Jamur' => 'hasil_jamur'] as $label => $field)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4">{{ $label }}</td>
                                    <td class="text-end pe-4">
                                        @php $val = $lab->$field ?? 'NT'; @endphp
                                        @if($val == '+')
                                            <span class="badge bg-danger rounded-circle p-1" title="Positif"><i class="ti ti-plus text-white"></i></span>
                                        @elseif($val == '-')
                                            <span class="badge bg-success rounded-circle p-1" title="Negatif"><i class="ti ti-minus text-white"></i></span>
                                        @else
                                            <span class="badge bg-light text-muted small px-2">N/T</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                {{-- Card: Statistik Sampel Lab --}}
                <div class="card card-premium h-100 shadow-sm border-0">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Parameter Statistik</h3>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Diperiksa</div>
                                    <div class="h3 mb-0">{{ $lab->jumlah_sampel_diperiksa ?? '-' }} <small class="text-muted fw-normal">Ekor</small></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-red-lt rounded-3">
                                    <div class="text-red small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Terinfeksi</div>
                                    <div class="h3 mb-0 text-red">{{ $lab->jumlah_ikan_terinfeksi ?? '-' }} <small class="fw-normal opacity-75">Ekor</small></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-azure-lt rounded-3 border-start border-azure border-4">
                                    <div class="text-azure small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Prevalensi</div>
                                    <div class="h3 mb-0 text-azure">{{ $lab->prevalensi ?? '-' }}%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-indigo-lt rounded-3 border-start border-indigo border-4">
                                    <div class="text-indigo small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Insidensi</div>
                                    <div class="h3 mb-0 text-indigo">{{ $lab->insidensi ?? '-' }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Link Pelaksanaan --}}
        <div class="card card-premium mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Data Asal Lapangan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-blue-lt p-2 rounded-3 me-3">
                        <i class="ti ti-map-pin text-blue" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Lokasi Sampling</div>
                        <div class="fw-bold">{{ $lab->pelaksanaan->lokasi_pengambilan_sampel }}</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-orange-lt p-2 rounded-3 me-3">
                        <i class="ti ti-fish text-orange" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Komoditas</div>
                        <div class="fw-bold">{{ $lab->pelaksanaan->jenis_ikan }}</div>
                    </div>
                </div>
                
                <a href="{{ route('pelaksanaan.show', $lab->pelaksanaan_id) }}" class="btn btn-primary w-100 py-2 shadow-sm">
                    <i class="ti ti-arrow-up-right me-1"></i>Detail Lapangan
                </a>
            </div>
        </div>

        {{-- Tambahan Info --}}
        <div class="card card-premium">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Informasi Teknis Uji</h3>
            </div>
            <div class="card-body">
                <div class="info-group">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-2 hover-bg-light">
                        <span class="text-muted small"><i class="ti ti-box me-1"></i> Kolam Uji</span>
                        <span class="fw-bold">{{ $lab->jumlah_kolam_uji ?? '-' }} <small class="fw-normal">Unit</small></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-2 hover-bg-light">
                        <span class="text-muted small"><i class="ti ti-clock me-1"></i> Periode Pengamatan</span>
                        <span class="fw-bold">{{ $lab->periode_pengamatan ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">ID Lab</span>
                    <span class="fw-mono small">LAB-{{ str_pad($lab->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

