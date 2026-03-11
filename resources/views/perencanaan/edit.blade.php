@extends('layouts.app')

@section('title', 'Edit Perencanaan')
@section('no_header', true)

@section('content')
<div class="row justify-content-center animate-fade-in">
    <div class="col-lg-10">
        <form action="{{ route('perencanaan.update', $perencanaan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Bagian 1: Identitas & Lokasi --}}
            <div class="card card-premium mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold text-primary">
                        <i class="ti ti-map-2 me-2"></i> DATA LOKASI & KOMODITAS
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Provinsi</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-map"></i></span>
                                <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                                    value="{{ old('provinsi', $perencanaan->provinsi) }}" 
                                    placeholder="Contoh: Jawa Barat" required
                                    @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit()) readonly @endif>
                            </div>
                            @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">Kabupaten / Kota</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                                <input type="text" name="kab_kota" class="form-control @error('kab_kota') is-invalid @enderror" 
                                    value="{{ old('kab_kota', $perencanaan->kab_kota) }}" placeholder="Contoh: Kota Bogor" required>
                            </div>
                            @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">
                                <i class="ti ti-fish me-1 text-primary"></i> Media Pembawa (Inang Rentan)
                            </label>
                            @php $selectedMp = array_map('trim', explode(',', $perencanaan->jenis_mp)); @endphp
                            <select name="jenis_mp[]" id="jenis_mp_select" class="form-control" multiple required>
                                @foreach($mediaPembawas ?? [] as $mp)
                                    <option value="{{ $mp->nama }}" {{ in_array($mp->nama, $selectedMp) ? 'selected' : '' }}>
                                        {{ $mp->nama }}
                                    </option>
                                @endforeach
                                {{-- Handle items not in master data --}}
                                @foreach($selectedMp as $s)
                                    @if(!collect($mediaPembawas)->contains('nama', $s) && !empty($s))
                                        <option value="{{ $s }}" selected>{{ $s }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-hint mt-2">Target komoditas pemantauan.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required fw-bold mb-2">
                                <i class="ti ti-virus me-1 text-primary"></i> Jenis HPIK
                            </label>
                            @php $selectedHpik = array_map('trim', explode(',', $perencanaan->jenis_hpik)); @endphp
                            <select name="jenis_hpik[]" id="jenis_hpik_select" class="form-control" multiple required>
                                @foreach($jenisPenyakits ?? [] as $jp)
                                    @php $val = $jp->nama . ($jp->singkatan ? ' (' . $jp->singkatan . ')' : ''); @endphp
                                    <option value="{{ $val }}" {{ in_array($val, $selectedHpik) ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                                {{-- Handle virtues that might not be in master data --}}
                                @foreach($selectedHpik as $s)
                                    @if(!collect($jenisPenyakits)->contains(fn($jp) => ($jp->nama . ($jp->singkatan ? ' (' . $jp->singkatan . ')' : '')) === $s) && !empty($s))
                                         <option value="{{ $s }}" selected>{{ $s }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-hint mt-2">Target penyakit yang dipantau.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Teknis & Lab --}}
            <div class="card card-premium mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold text-azure">
                        <i class="ti ti-microscope me-2"></i> KAPASITAS UJI & METODE
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label required fw-bold mb-2">
                                <i class="ti ti-settings me-1 text-azure"></i> Kemampuan Uji UPT
                            </label>
                            @php $selectedUji = array_map('trim', explode(',', $perencanaan->kemampuan_uji_upt)); @endphp
                            <select name="kemampuan_uji_upt[]" id="kemampuan_uji_upt_select" class="form-control" multiple required>
                                @php $opts = ['PCR', 'Real-time PCR', 'ELISA', 'Kultur Bakteri', 'Histopatologi', 'Isolasi & Identifikasi']; @endphp
                                @foreach($opts as $opt)
                                    <option value="{{ $opt }}" {{ in_array($opt, $selectedUji) ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                                @foreach($selectedUji as $s)
                                    @if(!in_array($s, $opts) && !empty($s))
                                        <option value="{{ $s }}" selected>{{ $s }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-bold mb-2">Metode Pengujian</label>
                            <input type="text" name="metode_pengujian" class="form-control" 
                                value="{{ old('metode_pengujian', $perencanaan->metode_pengujian) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-bold mb-2">Lab Uji Terakreditasi</label>
                            <input type="text" name="lab_uji" class="form-control" 
                                value="{{ old('lab_uji', $perencanaan->lab_uji) }}" required>
                        </div>
                        
                        <div class="col-12"><div class="hr-text text-muted my-2">RENCANA SAMPLING</div></div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-2">Rencana Lokasi Pengambilan Sampel</label>
                            <input type="text" name="rencana_lokasi" class="form-control" 
                                value="{{ old('rencana_lokasi', $perencanaan->rencana_lokasi) }}" placeholder="Contoh: Tambak rakyat / Unit pembenihan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-2">Target Uji</label>
                            <input type="number" name="rencana_jumlah_sampel" class="form-control rounded-3 border-light-dark shadow-sm" 
                                value="{{ old('rencana_jumlah_sampel', $perencanaan->rencana_jumlah_sampel) }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-2">Rencana Metode Sampling</label>
                            <select name="rencana_metode_sampling" class="form-select">
                                <option value="Acak" {{ old('rencana_metode_sampling', $perencanaan->rencana_metode_sampling) == 'Acak' ? 'selected' : '' }}>Acak (Random)</option>
                                <option value="Selektif" {{ old('rencana_metode_sampling', $perencanaan->rencana_metode_sampling) == 'Selektif' ? 'selected' : '' }}>Selektif</option>
                                <option value="Sensus" {{ old('rencana_metode_sampling', $perencanaan->rencana_metode_sampling) == 'Sensus' ? 'selected' : '' }}>Sensus</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 3: Target Kuartal --}}
            <div class="card card-premium mb-4 border-0 shadow-sm border-top border-warning border-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold text-orange">
                        <i class="ti ti-chart-dots me-2"></i> TARGET PER KUARTAL (TW)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-8">
                            <div class="row g-3">
                                @foreach([1, 2, 3, 4] as $tw)
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-light rounded-3 text-center border">
                                        <label class="form-label fw-bold mb-2 small text-uppercase">TW {{ $tw }}</label>
                                        @php $twField = 'tw'.$tw; @endphp
                                        <input type="number" name="tw{{ $tw }}" class="form-control text-center fw-bold fs-3 border-0 bg-transparent" 
                                            value="{{ old($twField, $perencanaan->$twField) }}" min="0" onchange="calculateTotal()">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-orange text-white rounded-4 shadow-sm text-center">
                                <div class="small fw-bold text-uppercase opacity-75 mb-1">Total Target Tahunan</div>
                                <div class="h1 mb-0 fw-bold" id="total-display">{{ $perencanaan->target_uji }}</div>
                                <input type="hidden" name="target_uji" id="target_uji_hidden" value="{{ old('target_uji', $perencanaan->target_uji) }}">
                                <div class="small mt-1">SAMPEL / EKOR</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex gap-3 pb-4">
                    <button type="submit" class="btn btn-warning btn-pill px-4 py-2 fs-3 shadow-sm">
                        <i class="ti ti-device-floppy me-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('perencanaan.index') }}" class="btn btn-link link-secondary">Batal & Kembali</a>
                </div>
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
            plugins: ['remove_button'],
            create: true,
            persist: false,
        });

        new TomSelect('#jenis_hpik_select', {
            plugins: ['remove_button'],
            create: true,
            persist: false,
        });

        new TomSelect('#kemampuan_uji_upt_select', {
            plugins: ['remove_button'],
            create: true,
            persist: false,
            options: [
                {value: 'PCR', text: 'PCR'},
                {value: 'Real-time PCR', text: 'Real-time PCR'},
                {value: 'ELISA', text: 'ELISA'},
                {value: 'Kultur Bakteri', text: 'Kultur Bakteri'},
                {value: 'Histopatologi', text: 'Histopatologi'},
                {value: 'Isolasi & Identifikasi', text: 'Isolasi & Identifikasi'}
            ]
        });

        function calculateTotal() {
            let t1 = parseInt(document.getElementsByName('tw1')[0].value) || 0;
            let t2 = parseInt(document.getElementsByName('tw2')[0].value) || 0;
            let t3 = parseInt(document.getElementsByName('tw3')[0].value) || 0;
            let t4 = parseInt(document.getElementsByName('tw4')[0].value) || 0;
            
            let total = t1 + t2 + t3 + t4;
            document.getElementById('total-display').textContent = total;
            document.getElementById('target_uji_hidden').value = total;
        }

        window.calculateTotal = calculateTotal;
    });
</script>
@endpush
@endsection
