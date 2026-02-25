@extends('layouts.app')

@section('title', 'Detail Perencanaan')
@section('page_title', 'Detail Perencanaan')
@section('page_subtitle', $p->jenis_mp . ' — ' . $p->kab_kota . ', ' . $p->provinsi)

@section('page_actions')
<a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-1"></i>Kembali
</a>
@endsection

@section('content')
<div class="row g-3">

    {{-- ── Kiri: Info Utama ── --}}
    <div class="col-lg-8">

        {{-- Card 1: Identitas Rencana --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-clipboard-list me-2"></i>Identitas Rencana Pemantauan</h3>
                <div class="card-options">
                    @php
                        $statusMap = [
                            'draft'    => ['label'=>'Draft',             'class'=>'bg-secondary text-white'],
                            'waiting'  => ['label'=>'Menunggu Validasi', 'class'=>'bg-warning text-dark'],
                            'approved' => ['label'=>'Disetujui',         'class'=>'bg-success text-white'],
                        ];
                        $s = $statusMap[$p->status] ?? $statusMap['draft'];
                    @endphp
                    <span class="badge {{ $s['class'] }} fs-6">{{ $s['label'] }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Provinsi</div>
                        <div class="fw-semibold">{{ $p->provinsi }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kabupaten / Kota</div>
                        <div class="fw-semibold">{{ $p->kab_kota }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jenis MP (Media Pembawa)</div>
                        <div class="fw-semibold">{{ $p->jenis_mp }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jenis HPIK (Target)</div>
                        <div class="fw-semibold">{{ $p->jenis_hpik }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Kemampuan Uji UPT</div>
                        <div class="fw-semibold">{{ $p->kemampuan_uji_upt }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Metode Pengujian</div>
                        <div class="fw-semibold">{{ $p->metode_pengujian }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Lab Uji</div>
                        <div class="fw-semibold">{{ $p->lab_uji }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Dibuat oleh</div>
                        <div class="fw-semibold">{{ $p->user->name ?? '-' }}</div>
                        <div class="small text-muted">{{ $p->user->instansi ?? '' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Dibuat</div>
                        <div class="fw-semibold">{{ $p->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Target Kuartal --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-calendar-stats me-2"></i>Target Uji Per Kuartal</h3>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    @foreach(['TW 1'=>$p->tw1, 'TW 2'=>$p->tw2, 'TW 3'=>$p->tw3, 'TW 4'=>$p->tw4] as $label => $val)
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-blue-lt rounded">
                            <div class="h3 mb-0 text-blue">{{ $val ?? 0 }}</div>
                            <div class="text-muted small">{{ $label }}</div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-12">
                        <div class="p-3 bg-primary-lt rounded d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total Target Uji</span>
                            <span class="h4 mb-0 text-primary">{{ $p->target_uji }} sampel</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Data Pelaksanaan Terkait --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-map-pin me-2"></i>Data Pelaksanaan Lapangan</h3>
                <div class="card-options">
                    <span class="badge bg-blue-lt">{{ $p->pelaksanaans->count() }} record</span>
                </div>
            </div>
            @if($p->pelaksanaans->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Sampel</th>
                            <th>Status Lab</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($p->pelaksanaans as $pel)
                        <tr>
                            <td class="text-muted small">{{ optional($pel->tanggal_pemantauan)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ Str::limit($pel->lokasi_pengambilan_sampel, 40) }}</td>
                            <td>{{ $pel->jumlah_sampel }} ekor</td>
                            <td>
                                @if($pel->laboratorium)
                                    <span class="badge bg-success-lt text-success">{{ $pel->laboratorium->hasil_uji }}</span>
                                @else
                                    <span class="badge bg-warning-lt text-warning">Belum Diuji</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pelaksanaan.show', $pel->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted text-center py-3">
                <i class="ti ti-map-pin-off" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>
                Belum ada data lapangan untuk perencanaan ini.
            </div>
            @endif
        </div>
    </div>

    {{-- ── Kanan: Sidebar info ── --}}
    <div class="col-lg-4">

        {{-- Evaluasi --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Hasil Evaluasi</h3>
            </div>
            <div class="card-body">
                @if($p->evaluasi)
                    @php
                        $warnaMap = ['hijau'=>['bg-success text-white','✅'],'kuning'=>['bg-warning text-dark','⚠️'],'merah'=>['bg-danger text-white','🔴']];
                        $we = $warnaMap[$p->evaluasi->status_warna] ?? ['bg-secondary text-white','⚪'];
                    @endphp
                    <div class="text-center mb-3">
                        <span class="badge fs-5 px-4 py-2 {{ $we[0] }}">
                            {{ $we[1] }} {{ $p->evaluasi->kesimpulan }}
                        </span>
                    </div>
                    <table class="table table-sm">
                        <tr><td class="text-muted">Prevalensi</td><td class="fw-semibold">{{ $p->evaluasi->prevalensi ?? '-' }}%</td></tr>
                        <tr><td class="text-muted">Insidensi</td><td class="fw-semibold">{{ $p->evaluasi->insidensi ?? '-' }}%</td></tr>
                        <tr><td class="text-muted">Realisasi Uji</td><td class="fw-semibold">{{ $p->evaluasi->realisasi_uji ?? '-' }} sampel</td></tr>
                    </table>
                    @if($p->evaluasi->catatan)
                        <div class="alert alert-info alert-dismissible small mb-0">
                            <i class="ti ti-notes me-1"></i>{{ $p->evaluasi->catatan }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="ti ti-chart-bar-off" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>
                        Belum ada evaluasi.
                        @if((Auth::user()->isBbkhit() || Auth::user()->isPusat()) && $p->status === 'approved' && !$p->evaluasi)
                            <div class="mt-2">
                                <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-orange">
                                    <i class="ti ti-chart-bar me-1"></i>Input Evaluasi
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Aksi</h3></div>
            <div class="card-body d-grid gap-2">
                @if(Auth::user()->isBkhit() && $p->status === 'draft' && $p->user_id === Auth::id())
                    <a href="{{ route('perencanaan.edit', $p->id) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-pencil me-1"></i>Edit Perencanaan
                    </a>
                @endif
                @if(Auth::user()->isBkhit() && $p->status === 'approved')
                    <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Input Data Lapangan
                    </a>
                @endif
                <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-list me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
