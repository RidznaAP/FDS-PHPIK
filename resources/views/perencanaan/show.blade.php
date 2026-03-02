@extends('layouts.app')

@section('title', 'Detail Perencanaan')

@section('content')
<div class="row detail-header align-items-center">
    <div class="col">
        <div class="detail-subtitle">Modul Perencanaan</div>
        <h1 class="detail-title">{{ $p->jenis_mp }}</h1>
        <div class="detail-subtitle">
            <i class="ti ti-map-pin me-1"></i>{{ $p->kab_kota }}, {{ $p->provinsi }}
        </div>
    </div>
    <div class="col-auto">
        <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-secondary btn-pill">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- ── Kiri: Info Utama ── --}}
    <div class="col-lg-8">
        {{-- Card 1: Identitas Rencana --}}
        <div class="card card-premium mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">
                    Identitas Rencana Pemantauan
                </h3>
                <div class="card-options">
                    @php
                        $statusMap = [
                            'draft'    => ['label'=>'Draft',             'class'=>'bg-secondary-lt text-secondary'],
                            'waiting'  => ['label'=>'Menunggu Validasi', 'class'=>'bg-warning-lt text-warning'],
                            'approved' => ['label'=>'Disetujui',         'class'=>'bg-success-lt text-success'],
                        ];
                        $s = $statusMap[$p->status] ?? $statusMap['draft'];
                    @endphp
                    <span class="badge badge-premium {{ $s['class'] }}">{{ $s['label'] }}</span>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-map-pin"></i></div>
                                <div class="info-content">
                                    <label>Wilayah Kerja</label>
                                    <span>{{ $p->kab_kota }}, {{ $p->provinsi }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-fish"></i></div>
                                <div class="info-content">
                                    <label>Media Pembawa</label>
                                    <span>{{ $p->jenis_mp }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-target"></i></div>
                                <div class="info-content">
                                    <label>Target HPIK</label>
                                    <span>{{ $p->jenis_hpik }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-building-community"></i></div>
                                <div class="info-content">
                                    <label>Kemampuan Uji UPT</label>
                                    <span>{{ $p->kemampuan_uji_upt }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-microscope"></i></div>
                                <div class="info-content">
                                    <label>Metode & Lab Uji</label>
                                    <span>{{ $p->metode_pengujian }}</span>
                                    <div class="sub-text">{{ $p->lab_uji }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="ti ti-user"></i></div>
                                <div class="info-content">
                                    <label>Dibuat Oleh</label>
                                    <span>{{ $p->user->name ?? '-' }}</span>
                                    <div class="sub-text">{{ $p->created_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Target Kuartal --}}
        <div class="mb-4">
            <h3 class="fw-bold mb-3" style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Target Uji Per Kuartal</h3>
            <div class="row g-3">
                @foreach(['TW 1'=>$p->tw1, 'TW 2'=>$p->tw2, 'TW 3'=>$p->tw3, 'TW 4'=>$p->tw4] as $label => $val)
                <div class="col-6 col-md-3">
                    <div class="stat-box {{ $val > 0 ? 'active' : '' }}">
                        <div class="stat-value">{{ $val ?? 0 }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="info-icon me-3 bg-azure-lt"><i class="ti ti-sum"></i></div>
                    <span class="fw-bold text-muted text-uppercase small" style="letter-spacing: 0.05em;">Total Target</span>
                </div>
                <div class="h3 mb-0 fw-bold text-primary">{{ $p->target_uji }} <small class="fw-normal text-muted">Sampel</small></div>
            </div>
        </div>

        {{-- Card 3: Data Pelaksanaan Terkait --}}
        <div class="card card-premium shadow-sm">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">
                    Realisasi Pelaksanaan Lapangan
                </h3>
                <div class="card-options">
                    <span class="badge bg-blue-lt">{{ $p->pelaksanaans->count() }} Record</span>
                </div>
            </div>
            <div class="card-body">
                @if($p->pelaksanaans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th class="text-uppercase small fw-bold">Tanggal</th>
                                <th class="text-uppercase small fw-bold">Lokasi</th>
                                <th class="text-uppercase small fw-bold">Sampel</th>
                                <th class="text-uppercase small fw-bold">Status Lab</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($p->pelaksanaans as $pel)
                            <tr>
                                <td>{{ optional($pel->tanggal_pemantauan)->format('d/m/Y') ?? '-' }}</td>
                                <td class="fw-semibold">{{ Str::limit($pel->lokasi_pengambilan_sampel, 30) }}</td>
                                <td>{{ $pel->jumlah_sampel }} Ekor</td>
                                <td>
                                    @if($pel->laboratorium)
                                        @php
                                            $labRes = $pel->laboratorium->hasil_uji;
                                            $labClass = $labRes === 'Negatif' ? 'bg-success-lt text-success' : ($labRes === 'Positif' ? 'bg-danger-lt text-danger' : 'bg-azure-lt text-azure');
                                        @endphp
                                        <span class="badge {{ $labClass }}">{{ $labRes }}</span>
                                    @else
                                        <span class="badge bg-warning-lt text-warning">Menunggu Uji</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pelaksanaan.show', $pel->id) }}" class="btn btn-icon btn-sm btn-ghost-primary rounded-circle">
                                        <i class="ti ti-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty py-4">
                    <div class="empty-icon text-muted">
                        <i class="ti ti-map-pin-off" style="font-size: 3rem;"></i>
                    </div>
                    <p class="empty-title">Belum ada realisasi</p>
                    <p class="empty-subtitle text-muted">Data pengambilan sampel di lapangan belum diinput untuk perencanaan ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Kanan: Sidebar info ── --}}
    <div class="col-lg-4">
        {{-- Evaluasi --}}
        <div class="card card-premium mb-4 overflow-hidden">
            <div class="card-header border-0 bg-transparent">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">
                    Hasil Evaluasi Akhir
                </h3>
            </div>
            <div class="card-body pt-0 text-center">
                @if($p->evaluasi)
                    @php
                        $warnaMap = [
                            'hijau'  => ['bg-success-lt text-success', 'ti-circle-check', 'Status Aman'],
                            'kuning' => ['bg-warning-lt text-warning', 'ti-alert-triangle', 'Waspada'],
                            'merah'  => ['bg-danger-lt text-danger', 'ti-circle-x', 'Bahaya / Wabah']
                        ];
                        $we = $warnaMap[$p->evaluasi->status_warna] ?? ['bg-secondary-lt text-secondary', 'ti-circle', 'Belum Ditentukan'];
                    @endphp
                   
                    <div class="mb-3">
                        <div class="p-4 rounded-4 {{ $we[0] }} mb-3">
                            <i class="ti {{ $we[1] }} mb-2" style="font-size: 3.5rem;"></i>
                            <h2 class="fw-bold mb-1">{{ $p->evaluasi->kesimpulan }}</h2>
                            <div class="small fw-bold text-uppercase opacity-75">{{ $we[2] }}</div>
                        </div>
                    </div>

                    <div class="info-group text-start">
                        <div class="d-flex justify-content-between p-2 rounded-2 hover-bg-light">
                            <span class="text-muted"><i class="ti ti-chart-line me-1"></i> Prevalensi</span>
                            <span class="fw-bold">{{ $p->evaluasi->prevalensi ?? '-' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded-2 hover-bg-light">
                            <span class="text-muted"><i class="ti ti-chart-arrows me-1"></i> Insidensi</span>
                            <span class="fw-bold">{{ $p->evaluasi->insidensi ?? '-' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded-2 hover-bg-light border-bottom pb-2">
                            <span class="text-muted"><i class="ti ti-test-pipe me-1"></i> Realisasi Uji</span>
                            <span class="fw-bold">{{ $p->evaluasi->realisasi_uji ?? '-' }} <small class="fw-normal">Sampel</small></span>
                        </div>
                    </div>

                    @if($p->evaluasi->catatan)
                        <div class="mt-3 text-start">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Catatan Evaluator:</div>
                            <div class="p-3 bg-light rounded-3 italic small border-start border-primary border-4">
                                "{{ $p->evaluasi->catatan }}"
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty py-4">
                        <div class="empty-icon text-muted">
                            <i class="ti ti-chart-bar-off" style="font-size: 2.5rem;"></i>
                        </div>
                        <p class="empty-subtitle">Siklus belum dievaluasi</p>
                        @if((Auth::user()->isBbkhit() || Auth::user()->isPusat()) && $p->status === 'approved' && !$p->evaluasi)
                            <div class="mt-3">
                                <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-warning w-100">
                                    <i class="ti ti-chart-bar me-1"></i>Input Evaluasi
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card card-premium">
            <div class="card-header border-0">
                <h3 class="card-title fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #64748b; font-size: 0.8rem;">Aksi Cepat</h3>
            </div>
            <div class="card-body d-grid gap-2">
                @if(Auth::user()->isBkhit() && $p->status === 'draft' && $p->user_id === Auth::id())
                    <a href="{{ route('perencanaan.edit', $p->id) }}" class="btn btn-azure w-100 py-2">
                        <i class="ti ti-pencil me-1"></i>Edit Perencanaan
                    </a>
                @endif
                @if(Auth::user()->isBkhit() && $p->status === 'approved')
                    <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="ti ti-plus me-2"></i>Input Lapangan
                    </a>
                @endif
                <a href="{{ route('perencanaan.export') }}" class="btn btn-ghost-success w-100">
                    <i class="ti ti-download me-2"></i>Ekspor ke Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

