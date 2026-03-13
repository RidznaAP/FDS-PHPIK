@extends('layouts.app')

@section('title', 'Penetapan Evaluasi')
@section('page_title', 'Penetapan Evaluasi Akhir')
@section('page_subtitle', 'Analisis hasil laboratorium dan penetapan status wilayah: ' . $perencanaan->kab_kota . ', ' . $perencanaan->provinsi)

@section('content')
<div class="row justify-content-center animate-fade-in">
    <div class="col-lg-10">

        {{-- Ringkasan Hasil Lab (Data Board) --}}
        <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h3 class="card-title fw-bold text-azure">
                    <i class="ti ti-table me-2"></i> RINGKASAN DATA LABORATORIUM
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-nowrap">
                        <thead>
                            <tr class="bg-light text-muted small fw-bold text-uppercase">
                                <th class="ps-4">Lokasi Sampling</th>
                                <th>Kode Sampel</th>
                                <th>Metode & Patogen</th>
                                <th class="text-center">Hasil Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perencanaan->pelaksanaans as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $p->lokasi_pengambilan_sampel }}</div>
                                    <div class="small text-muted">{{ $p->tanggal_pemantauan->format('d/m/Y') }}</div>
                                </td>
                                <td class="font-monospace small">{{ $p->laboratorium->kode_sampel ?? '-' }}</td>
                                <td>
                                    @if($p->laboratorium)
                                        <div class="fw-bold">{{ $p->laboratorium->metode_uji }}</div>
                                        <div class="small text-muted">{{ $p->laboratorium->jenis_hpik_diuji }}</div>
                                    @else
                                        <span class="text-muted small italic">Data belum tersedia</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->laboratorium)
                                        @php $hasil = $p->laboratorium->hasil_uji; @endphp
                                        <span class="status-indicator status-{{ $hasil === 'Negatif' ? 'success' : ($hasil === 'Positif' ? 'danger' : 'warning') }} me-1"></span>
                                        <span class="badge {{ $hasil === 'Negatif' ? 'bg-success text-white' : ($hasil === 'Positif' ? 'bg-danger text-white' : 'bg-warning text-white') }} btn-pill px-3">
                                            {{ $hasil }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-lt btn-pill px-3">Belum Diuji</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Evaluasi --}}
        <form action="{{ route('evaluasi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="perencanaan_id" value="{{ $perencanaan->id }}">
            
            <div class="card card-premium mb-4 border-0 shadow-sm border-top border-primary border-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold text-primary">
                        <i class="ti ti-checklist me-2"></i> FORM PENETAPAN STATUS
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Kesimpulan Akhir</label>
                            <select name="kesimpulan" class="form-select form-select-lg @error('kesimpulan') is-invalid @enderror" required>
                                <option value="">— Pilih Kesimpulan —</option>
                                <option value="Bebas HPIK" {{ old('kesimpulan') === 'Bebas HPIK' ? 'selected' : '' }}>🟢 Bebas HPIK (Negatif)</option>
                                <option value="Waspada" {{ old('kesimpulan') === 'Waspada' ? 'selected' : '' }}>🟡 Waspada (Inkonklusif/Gejala Klinis)</option>
                                <option value="Positif HPIK" {{ old('kesimpulan') === 'Positif HPIK' ? 'selected' : '' }}>🔴 Positif HPIK (Terdeteksi)</option>
                            </select>
                            @error('kesimpulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Status Warna (GIS Dashboard)</label>
                            <select name="status_warna" class="form-select form-select-lg @error('status_warna') is-invalid @enderror" required>
                                <option value="">— Pilih Warna Peta —</option>
                                <option value="hijau" {{ old('status_warna') === 'hijau' ? 'selected' : '' }}>🟢 HIJAU (Aman / Bebas)</option>
                                <option value="kuning" {{ old('status_warna') === 'kuning' ? 'selected' : '' }}>🟡 KUNING (Waspada)</option>
                                <option value="merah" {{ old('status_warna') === 'merah' ? 'selected' : '' }}>🔴 MERAH (Wabah / Positif)</option>
                            </select>
                            <div class="form-hint mt-2">Warna ini akan tampil secara otomatis pada peta sebaran penyakit.</div>
                            @error('status_warna')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">Rekomendasi Tindak Lanjut</label>
                            <textarea name="rekomendasi" class="form-control" rows="4" 
                                placeholder="Jelaskan langkah strategis, penanganan, atau tindak lanjut yang harus dilakukan oleh UPT/Dinas terkait...">{{ old('rekomendasi') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Evaluator (Nama Pejabat)</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-user-check"></i></span>
                                <input type="text" name="evaluator" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Tanggal Penetapan</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-calendar-check"></i></span>
                                <input type="date" name="tanggal_evaluasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex gap-3 pb-4">
                    <button type="submit" class="btn btn-primary btn-pill px-5 shadow-sm fs-3">
                        <i class="ti ti-circle-check me-2"></i> Simpan & Selesaikan Evaluasi
                    </button>
                    <a href="{{ route('perencanaan.show', $perencanaan->id) }}" class="btn btn-link link-secondary">Batalkan</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
