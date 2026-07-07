@extends('layouts.app')

@section('title', 'Cetak Laporan Pelaksanaan - ' . $item->kode_sampel)

@section('content')
<div class="container-tight py-4" id="printable-area">
    <div class="card border-0 shadow-none bg-white p-5" style="color: #1e293b;">
        {{-- Header KOP --}}
        <div class="text-center border-bottom pb-4 mb-5">
            <h1 class="fw-extrabold mb-1">LAPORAN HASIL PEMANTAUAN HPIK</h1>
            <div class="text-muted small text-uppercase fw-bold tracking-widest">Sistem Informasi Pemantauan - SIP HPIK</div>
            <div class="mt-2 fw-bold text-primary">{{ $item->perencanaan->user->upt_asal ?? 'BALAI KARANTINA HEWAN, IKAN DAN TUMBUHAN' }}</div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-6">
                <div class="small text-muted text-uppercase fw-bold mb-1">Identitas Sampel</div>
                <table class="table table-sm table-borderless">
                    <tr><td class="ps-0 py-1 w-1">Media Pembawa</td><td class="py-1 fw-bold">: {{ $item->jenis_ikan }}</td></tr>
                    <tr><td class="ps-0 py-1">Lokasi</td><td class="py-1 fw-bold">: {{ $item->lokasi_pengambilan_sampel }}</td></tr>
                    <tr><td class="ps-0 py-1">Tanggal</td><td class="py-1 fw-bold">: {{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d M Y') }}</td></tr>
                    <tr><td class="ps-0 py-1">Petugas Lapangan</td><td class="py-1 fw-bold">: {{ implode(', ', $item->pengambil_sampel ?? []) }}</td></tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <div class="small text-muted text-uppercase fw-bold mb-1">Kode Registrasi</div>
                <div class="h3 fw-bold text-dark mb-1">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="badge bg-primary-lt px-3 py-1">{{ $item->laboratorium->kode_sampel ?? 'PENDING' }}</div>
            </div>
        </div>

        {{-- Seksi 1: Data Lapangan --}}
        <div class="mb-5">
            <h3 class="border-bottom pb-2 mb-3 fw-bold"><i class="ti ti-map-pin me-2"></i>I. DATA PELAKSANAAN LAPANGAN</h3>
            <div class="row g-3">
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Jumlah Sampel</div>
                        <div class="h3 fw-bold mb-0">{{ $item->jumlah_sampel }} Ekor</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Panjang Rata-rata</div>
                        <div class="h3 fw-bold mb-0">{{ $item->laboratorium->panjang ?? $item->panjang_cm ?? '-' }} cm</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Berat Rata-rata</div>
                        <div class="h3 fw-bold mb-0">{{ $item->laboratorium->berat ?? $item->berat_gram ?? '-' }} g</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Gejala Klinis Teramati</div>
                        <div class="fw-bold">{{ $item->laboratorium->gejala_klinis ?? $item->gejala_klinis ?? 'NIHIL' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seksi 2: Hasil Laboratorium --}}
        @if($item->laboratorium)
        <div class="mb-5">
            <h3 class="border-bottom pb-2 mb-3 fw-bold text-uppercase"><i class="ti ti-flask me-2"></i>II. HASIL UJI LABORATORIUM</h3>
            <div class="p-4 rounded-4 border-start border-4 shadow-sm mb-4 {{ $item->laboratorium->hasil_uji === 'Positif' ? 'border-danger bg-red-lt' : 'border-success bg-green-lt' }}">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="small fw-bold mb-1 opacity-75">KESIMPULAN DIAGNOSIS:</div>
                        <div class="h1 fw-extrabold mb-0">{{ strtoupper($item->laboratorium->diagnosis_akhir) }}</div>
                        <div class="fw-bold">Status: {{ strtoupper($item->laboratorium->hasil_uji) }}</div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="h3 fw-bold mb-0">Prevalensi: {{ $item->laboratorium->prevalensi }}%</div>
                        <div class="small fw-bold opacity-75">Lab: {{ $item->laboratorium->lab_penguji }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <table class="table table-sm border">
                        <thead class="bg-light">
                            <tr><th colspan="2" class="p-2 fw-bold">RINCIAN PATOGEN</th></tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-2">Parasit</td><td class="p-2 fw-bold text-end">{{ $item->laboratorium->hasil_parasit ?: 'Negatif' }}</td></tr>
                            <tr><td class="p-2">Bakteri</td><td class="p-2 fw-bold text-end">{{ $item->laboratorium->hasil_bakteri ?: 'Negatif' }}</td></tr>
                            <tr><td class="p-2">Virus</td><td class="p-2 fw-bold text-end">{{ $item->laboratorium->hasil_virus ?: 'Negatif' }}</td></tr>
                            <tr><td class="p-2">Jamur</td><td class="p-2 fw-bold text-end">{{ $item->laboratorium->hasil_jamur ?: 'Negatif' }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <div class="card border h-100 p-3">
                        <div class="small text-muted fw-bold mb-2">METODE & PETUGAS</div>
                        <div class="mb-1"><strong>Metode:</strong> {{ $item->laboratorium->metode_uji }}</div>
                        <div class="mb-1"><strong>Petugas Uji:</strong> {{ $item->laboratorium->nama_petugas_uji ?? '-' }}</div>
                        <div class="mb-1"><strong>Mulai Uji:</strong> {{ $item->laboratorium->tanggal_uji->format('d M Y') }}</div>
                        <div class="mb-1"><strong>Hasil Terbit:</strong> {{ $item->laboratorium->tanggal_hasil ? $item->laboratorium->tanggal_hasil->format('d M Y') : 'Selesai' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5 border border-dashed rounded-4">
            <h3 class="text-muted">HASIL LABORATORIUM BELUM TERBIT</h3>
        </div>
        @endif

        {{-- Footer TTD --}}
        <div class="mt-5 pt-5">
            <div class="row">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    <div class="small fw-bold mb-5">Dicetak pada: {{ date('d M Y H:i') }}</div>
                    <div class="mt-5 border-bottom border-dark d-inline-block px-5"></div>
                    <div class="small fw-bold mt-1">Admin SIP-HPIK</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center d-print-none">
        <button onclick="window.print()" class="btn btn-primary btn-lg btn-pill px-5">
            <i class="ti ti-printer me-2"></i>KONFIRMASI CETAK
        </button>
        <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-link link-secondary ms-3">Batal & Kembali</a>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 15mm;
    }
    body { 
        background: white !important; 
        color: black !important;
        font-size: 11pt;
    }
    .navbar, .footer, .d-print-none, .breadcrumb, .card-header button { display: none !important; }
    .container-tight { max-width: 100% !important; padding: 0 !important; width: 100% !important; }
    .card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important; }
    .page-wrapper { margin: 0 !important; padding: 0 !important; }
    #printable-area { width: 100%; margin: 0; }
    
    /* Ensure content doesn't break awkwardly */
    h3 { page-break-after: avoid; }
    .row { page-break-inside: auto; }
    .col-6, .col-4, .col-12 { page-break-inside: avoid; }
    .card-premium { page-break-inside: avoid; }
    
    .bg-light { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
    .bg-primary-lt { background-color: #eef6ff !important; -webkit-print-color-adjust: exact; }
    .bg-red-lt { background-color: #fff5f5 !important; -webkit-print-color-adjust: exact; }
    .bg-green-lt { background-color: #f0fff4 !important; -webkit-print-color-adjust: exact; }
    .text-primary { color: #206bc4 !important; }
    .border { border: 1px solid #e2e8f0 !important; }
}
</style>
@endsection
