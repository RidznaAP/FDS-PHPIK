@extends('layouts.app')

@section('title', 'Form Evaluasi')
@section('page_title', 'Evaluasi Akhir')
@section('page_subtitle', $perencanaan->kab_kota . ', ' . $perencanaan->provinsi)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- Ringkasan Hasil Lab --}}
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Ringkasan Hasil Laboratorium</h3></div>
            <div class="table-responsive">
                <table class="table table-sm card-table">
                    <thead>
                        <tr>
                            <th>Lokasi Sampling</th>
                            <th>Kode Sampel</th>
                            <th>Metode</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perencanaan->pelaksanaans as $p)
                        <tr>
                            <td>{{ $p->lokasi_pengambilan_sampel }}</td>
                            <td>{{ $p->laboratorium->kode_sampel ?? '-' }}</td>
                            <td>{{ $p->laboratorium->metode_uji ?? '-' }}</td>
                            <td>
                                @if($p->laboratorium)
                                    @php $hasil = $p->laboratorium->hasil_uji; @endphp
                                    <span class="badge {{ $hasil === 'Negatif' ? 'bg-success-lt text-success' : ($hasil === 'Positif' ? 'bg-danger-lt text-danger' : 'bg-warning-lt text-warning') }}">
                                        {{ $hasil }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-lt">Belum diuji</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Evaluasi --}}
        <form action="{{ route('evaluasi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="perencanaan_id" value="{{ $perencanaan->id }}">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Penetapan Evaluasi</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Kesimpulan</label>
                            <select name="kesimpulan" class="form-select" required>
                                <option value="">— Pilih Kesimpulan —</option>
                                <option value="Bebas HPIK">🟢 Bebas HPIK</option>
                                <option value="Waspada">🟡 Waspada</option>
                                <option value="Positif HPIK">🔴 Positif HPIK</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Status Warna (GIS)</label>
                            <select name="status_warna" class="form-select" required>
                                <option value="">— Pilih Warna —</option>
                                <option value="hijau">🟢 Hijau (Bebas)</option>
                                <option value="kuning">🟡 Kuning (Waspada)</option>
                                <option value="merah">🔴 Merah (Positif)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Rekomendasi</label>
                            <textarea name="rekomendasi" class="form-control" rows="3" placeholder="Tuliskan rekomendasi tindak lanjut..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Evaluator (Nama Pejabat)</label>
                            <input type="text" name="evaluator" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Evaluasi</label>
                            <input type="date" name="tanggal_evaluasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Simpan Evaluasi
                    </button>
                    <a href="{{ route('evaluasi.index') }}" class="btn btn-link">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
