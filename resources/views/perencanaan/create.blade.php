@extends('layouts.app')

@section('title', 'Form Perencanaan Baru')
@section('no_header', true)

@section('content')
<div class="row justify-content-center animate-fade-in px-2">
    <div class="col-lg-10">
        {{-- High-End Page Header --}}
        <div class="row align-items-center mb-5 g-4 shadow-sm p-4 bg-white rounded-4 border-start border-indigo border-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-4">
                    <div class="bg-indigo text-white p-4 rounded-4 shadow-lg animate-bounce-in d-none d-md-block">
                        <i class="ti ti-calendar-plus fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-indigo-lt text-indigo px-3 fs-6 rounded-pill">MODUL PERENCANAAN</span>
                        </div>
                        <h1 class="display-5 fw-bold text-dark mb-1 tracking-tight">Perencanaan Strategis Baru</h1>
                        <div class="text-muted fs-3">Formulir rencana pemantauan HPIK komprehensif tahun {{ date('Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('perencanaan.index') }}" class="btn btn-white btn-pill px-4 border shadow-sm">
                    <i class="ti ti-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </div>

        <form action="{{ route('perencanaan.store') }}" method="POST">
            @csrf
            
            <div class="row g-4 mb-4">
                {{-- Lokasi & Komoditas --}}
                <div class="col-12">
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-map-2 me-2 text-indigo"></i> GEOGRAFI & IDENTITAS SAMPEL
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Provinsi Wilayah Kerja</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map"></i></span>
                                        <input type="text" name="provinsi" class="form-control rounded-3 border-light-dark @error('provinsi') is-invalid @enderror" 
                                            value="{{ old('provinsi', (Auth::user()->isBkhit() || Auth::user()->isBbkhit()) ? (Auth::user()->upt_asal ?? '') : '') }}" 
                                            placeholder="Provinsi..." required
                                            @if((Auth::user()->isBkhit() || Auth::user()->isBbkhit()) && !empty(Auth::user()->upt_asal)) readonly @endif>
                                    </div>
                                    @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Kabupaten / Kota</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                        <input type="text" name="kab_kota" class="form-control rounded-3 border-light-dark @error('kab_kota') is-invalid @enderror" value="{{ old('kab_kota') }}" placeholder="Kab/Kota..." required>
                                    </div>
                                    @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <label class="form-label required fw-bold mb-2">
                                        <i class="ti ti-fish me-1 text-indigo"></i> Media Pembawa (Inang Rentan)
                                    </label>
                                    <select name="jenis_mp" id="jenis_mp_select" class="form-select rounded-3 border-light-dark" required>
                                        <option value="" disabled {{ old('jenis_mp') ? '' : 'selected' }}>Pilih Komoditas...</option>
                                        @foreach($mediaPembawas ?? [] as $mp)
                                            <option value="{{ $mp->nama }}">{{ $mp->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <label class="form-label required fw-bold mb-2">
                                        <i class="ti ti-virus me-1 text-indigo"></i> Jenis HPIK
                                    </label>
                                    <select name="jenis_hpik[]" id="jenis_hpik_select" class="form-control" multiple required>
                                        @foreach($jenisPenyakits ?? [] as $jp)
                                            <option value="{{ $jp->nama }}{{ $jp->singkatan ? ' (' . $jp->singkatan . ')' : '' }}">{{ $jp->nama }}{{ $jp->singkatan ? ' (' . $jp->singkatan . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint mt-2 text-muted small"><i class="ti ti-info-circle me-1"></i>Dapat memilih lebih dari 1 HPIK.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Teknis Lab --}}
                <div class="col-12">
                    <div class="card card-premium h-100 border-0 shadow-sm bg-white">
                         <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-1">
                            <h3 class="card-title fw-bold text-muted small text-uppercase tracking-widest">
                                <i class="ti ti-microscope me-2 text-azure"></i> METODOLOGI & KAPASITAS LAB
                            </h3>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-12 mb-2">
                                    <label class="form-label required fw-bold mb-2">
                                        <i class="ti ti-settings me-1 text-azure"></i> Kemampuan Uji UPT
                                    </label>
                                    <select name="kemampuan_uji_upt[]" id="kemampuan_uji_upt_select" class="form-control" multiple required>
                                        @foreach($jenisPenyakits ?? [] as $jp)
                                            <option value="{{ $jp->nama }}{{ $jp->singkatan ? ' (' . $jp->singkatan . ')' : '' }}" {{ in_array($jp->nama . ($jp->singkatan ? ' (' . $jp->singkatan . ')' : ''), old('kemampuan_uji_upt', [])) ? 'selected' : '' }}>
                                                {{ $jp->nama }}{{ $jp->singkatan ? ' (' . $jp->singkatan . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint mt-2 text-muted small"><i class="ti ti-info-circle me-1"></i>Dapat memilih lebih dari 1 metode uji.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Metode Pengujian</label>
                                    <select name="metode_pengujian[]" id="metode_pengujian_select" class="form-control" multiple required>
                                        <option value="PCR">PCR</option>
                                        <option value="RT-PCR">RT-PCR</option>
                                        <option value="Real-Time PCR (qPCR)">Real-Time PCR (qPCR)</option>
                                        <option value="Sekuensing DNA">Sekuensing DNA</option>
                                        <option value="Isolasi Bakteri">Isolasi Bakteri</option>
                                        <option value="Uji Biokimia">Uji Biokimia</option>
                                        <option value="Uji Sensitivitas/Antibiogram">Uji Sensitivitas/Antibiogram</option>
                                        <option value="Natif/Scrapping">Natif/Scrapping</option>
                                        <option value="Sediaan Ulas (Smear)">Sediaan Ulas (Smear)</option>
                                        <option value="Kultur Jamur">Kultur Jamur</option>
                                        <option value="Pemeriksaan Mikroskopis Struktur Jamur">Pemeriksaan Mikroskopis Struktur Jamur</option>
                                        <option value="Pemeriksaan Jaringan (Slide)">Pemeriksaan Jaringan (Slide)</option>
                                        <option value="Isolasi Virus">Isolasi Virus</option>
                                        <option value="ELISA">ELISA</option>
                                        <option value="IFAT">IFAT</option>
                                    </select>
                                    <div class="form-hint mt-2 text-muted small"><i class="ti ti-info-circle me-1"></i>Dapat memilih lebih dari 1.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold mb-2">Lab Pelaksana</label>
                                    <input type="text" name="lab_uji" class="form-control rounded-3 border-light-dark" 
                                        value="{{ old('lab_uji', 'Laboratorium Karantina Ikan ' . (Auth::user()->upt_asal ?? '')) }}" required>
                                </div>
                                
                                <div class="col-12 border-top pt-2">
                                    <label class="form-label fw-bold mb-2">Lokasi Pengambilan Sampel</label>
                                    <textarea name="rencana_lokasi" class="form-control rounded-3 border-light-dark" 
                                        rows="3" placeholder="Contoh: Tambak rakyat, Hatchery Desa Suka Maju, Kab. Indramayu...">{{ old('rencana_lokasi') }}</textarea>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Metode Sampling</label>
                                    <select name="rencana_metode_sampling" class="form-select rounded-3 border-light-dark">
                                        <option value="Acak" {{ old('rencana_metode_sampling') == 'Acak' ? 'selected' : '' }}>Acak / Random</option>
                                        <option value="Selektif" {{ old('rencana_metode_sampling') == 'Selektif' ? 'selected' : '' }}>Selektif</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label fw-bold mb-2">Target Uji</label>
                                            <div class="input-icon">
                                                <span class="input-icon-addon"><i class="ti ti-target"></i></span>
                                                <input type="number" name="target_uji" class="form-control rounded-3 border-light-dark shadow-sm" value="{{ old('target_uji', 0) }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold mb-2">Jumlah Sampel</label>
                                            <div class="input-icon">
                                                <span class="input-icon-addon"><i class="ti ti-box"></i></span>
                                                <input type="number" name="rencana_jumlah_sampel" class="form-control rounded-3 border-light-dark shadow-sm" value="{{ old('rencana_jumlah_sampel', 0) }}" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 3: Target Kuartal Terminal --}}
            <div class="card card-premium mb-3 border-0 shadow-sm border-top border-primary border-5 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h3 class="card-title fw-bold text-indigo">
                        <i class="ti ti-chart-arrows me-2"></i> TARGET OPERASIONAL PER KUARTAL
                    </h3>
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="row g-4 align-items-stretch">
                        <div class="col-md-8">
                            <div class="row g-4">
                                {{-- Periode 1 --}}
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-blue text-white px-3 py-1 rounded-pill">PERIODE 1</span>
                                        <span class="text-muted small fw-bold text-uppercase">Kuartal 1 & 2</span>
                                    </div>
                                    <div class="row g-3">
                                        @foreach([1, 2] as $tw)
                                        <div class="col-md-6">
                                            <div class="p-4 bg-light-soft rounded-4 text-center border transition-all hover-border-primary hover-shadow-sm">
                                                <label class="form-label fw-extrabold mb-3 small text-uppercase tracking-widest text-muted">TRIWULAN {{ $tw }}</label>
                                                <input type="number" name="tw{{ $tw }}" class="form-control text-center fw-extrabold fs-2 border-0 bg-transparent text-primary p-0" value="{{ old('tw'.$tw, 0) }}" min="0" onchange="calculateTotal()" onclick="this.select()">
                                                <div class="small text-muted fw-bold mt-2">SAMPEL</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                {{-- Periode 2 --}}
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-azure text-white px-3 py-1 rounded-pill">PERIODE 2</span>
                                        <span class="text-muted small fw-bold text-uppercase">Kuartal 3 & 4</span>
                                    </div>
                                    <div class="row g-3">
                                        @foreach([3, 4] as $tw)
                                        <div class="col-md-6">
                                            <div class="p-4 bg-light-soft rounded-4 text-center border transition-all hover-border-primary hover-shadow-sm">
                                                <label class="form-label fw-extrabold mb-3 small text-uppercase tracking-widest text-muted">TRIWULAN {{ $tw }}</label>
                                                <input type="number" name="tw{{ $tw }}" class="form-control text-center fw-extrabold fs-2 border-0 bg-transparent text-primary p-0" value="{{ old('tw'.$tw, 0) }}" min="0" onchange="calculateTotal()" onclick="this.select()">
                                                <div class="small text-muted fw-bold mt-2">SAMPEL</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-primary-lt rounded-4 border border-primary border-dashed text-center">
                                <i class="ti ti-checkup-list me-2"></i><b>Instruksi:</b> Masukkan target jumlah sampel yang akan dipantau pada setiap periode kuartal.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-5 h-100 bg-indigo text-white rounded-4 shadow-lg text-center d-flex flex-column justify-content-center animate-scale-up border-0">
                                <div class="small fw-extrabold text-uppercase opacity-75 mb-2 tracking-widest">Total Akumulasi Tahunan</div>
                                <div class="display-1 mb-0 fw-extrabold tracking-tighter" id="total-display">0</div>
                                <div class="h4 mt-2 fw-bold italic opacity-75">Terkalkulasi Otomatis</div>
                                <div class="mt-4">
                                    <div class="bg-white-transparent p-2 rounded-pill small fw-bold"><i class="ti ti-status-up me-1"></i>AUTO-CALCULATED</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
                <a href="{{ route('perencanaan.index') }}" class="btn btn-outline-danger shadow-sm btn-pill px-5 py-3 fs-3 fw-bold hover-scale transition-all">
                    <i class="ti ti-arrow-left me-2"></i> Batal & Keluar
                </a>
                <button type="submit" class="btn btn-primary btn-pill px-5 py-3 fs-3 shadow-lg fw-extrabold hover-scale transition-all">
                    <i class="ti ti-device-floppy me-2"></i> SIMPAN PERENCANAAN
                </button>
            </div>

        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Ultra-Modern Premium Select Styling */
    .ts-wrapper .ts-control { 
        border: 1.5px solid #e2e8f0 !important; 
        padding: 0.75rem 1rem !important; 
        border-radius: 0.75rem !important;
        background-color: #f8fafc !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 52px;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02) !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #6366f1 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    .ts-wrapper .ts-control > input {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        width: 100% !important;
    }
    .ts-dropdown { 
        border-radius: 1rem !important; 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; 
        border: 1px solid rgba(226, 232, 240, 0.8) !important; 
        margin-top: 10px !important;
        padding: 8px !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px);
        z-index: 2000 !important;
    }
    .ts-dropdown .option {
        border-radius: 0.6rem !important;
        padding: 10px 15px !important;
        margin-bottom: 2px;
        transition: all 0.2s ease;
    }
    .ts-dropdown .active { background-color: #6366f1 !important; color: white !important; }
    .ts-dropdown .option:hover:not(.active) { background-color: #f1f5f9 !important; }
    
    .ts-wrapper .items { display: flex; flex-wrap: wrap; gap: 6px !important; padding: 6px 12px !important; }
    
    /* Premium Tag (Item) Styles */
    .ts-wrapper .item { 
        border-radius: 100px !important;
        padding: 5px 14px !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
    }
    .ts-wrapper .item:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.08) !important; }
    
    /* Individual Field Color Identities */
    #jenis_mp_select + .ts-wrapper .item { background: #e0e7ff !important; color: #4338ca !important; border: 1px solid #c7d2fe !important; }
    #jenis_hpik_select + .ts-wrapper .item { background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; }
    #kemampuan_uji_upt_select + .ts-wrapper .item { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
    #metode_pengujian_select + .ts-wrapper .item { background: #f3e8ff !important; color: #7e22ce !important; border: 1px solid #e9d5ff !important; }
    
    .ts-wrapper .item .remove { 
        margin-left: 8px; 
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.06);
        transition: all 0.2s;
        text-decoration: none !important;
        font-size: 10px;
    }
    .ts-wrapper .item .remove:hover { background: rgba(0,0,0,0.15); color: inherit !important; }
    select.tomselected { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialization for Tom Select
        new TomSelect('#jenis_mp_select', {
            dropdownParent: 'body',
            create: true,
            persist: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        new TomSelect('#metode_pengujian_select', {
            dropdownParent: 'body',
            plugins: ['remove_button'],
            create: true,
            persist: false,
        });

        new TomSelect('#jenis_hpik_select', {
            dropdownParent: 'body',
            plugins: ['remove_button'],
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        new TomSelect('#kemampuan_uji_upt_select', {
            dropdownParent: 'body',
            plugins: ['remove_button'],
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        function calculateTotal() {
            let t1 = parseInt(document.getElementsByName('tw1')[0].value) || 0;
            let t2 = parseInt(document.getElementsByName('tw2')[0].value) || 0;
            let t3 = parseInt(document.getElementsByName('tw3')[0].value) || 0;
            let t4 = parseInt(document.getElementsByName('tw4')[0].value) || 0;
            
            let total = t1 + t2 + t3 + t4;
            document.getElementById('total-display').textContent = total;
        }
        
        // Expose to window for inline onchange
        window.calculateTotal = calculateTotal;
        calculateTotal();
    });
</script>
@endpush
@endsection
