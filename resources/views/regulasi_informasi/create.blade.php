@extends('layouts.app')

@section('title', 'Tambah Regulasi Informasi')
@section('page_title', 'Tambah Informasi Baru')
@section('page_subtitle', 'Publikasikan regulasi atau informasi untuk seluruh unit')

@section('content')

<style>
    .form-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 8px 24px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .form-card .card-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        padding: 1.5rem 2rem;
        border: none;
    }
    .form-card .card-body {
        padding: 2rem;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 0.85rem;
        font-size: 0.875rem;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: #f8fafc;
        position: relative;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: #2563eb;
        background: #eff6ff;
    }
    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .upload-preview {
        margin-top: 1rem;
        display: none;
    }
    .upload-preview img {
        max-height: 180px;
        border-radius: 10px;
        object-fit: contain;
        box-shadow: 0 2px 8px rgba(0,0,0,.1);
    }
    .upload-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-top: 1rem;
        display: none;
    }
    .char-counter {
        font-size: 0.72rem;
        color: #94a3b8;
        text-align: right;
    }
    .char-counter.warning { color: #f59e0b; }
    .char-counter.danger { color: #ef4444; }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">

        {{-- Back button --}}
        <div class="mb-3">
            <a href="{{ route('regulasi.index') }}" class="btn btn-ghost-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="form-card card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:12px;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="ti ti-clipboard-plus text-white" style="font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <h4 class="text-white fw-bold mb-0" style="font-size:1.05rem;">Tambah Informasi Baru</h4>
                        <p class="mb-0" style="color:rgba(255,255,255,0.65);font-size:0.8rem;">
                            Akan dipublikasikan ke seluruh unit BKHIT &amp; BBKHIT
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:10px;">
                    <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i> Terdapat kesalahan:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li style="font-size:0.85rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('regulasi.store') }}"
                      enctype="multipart/form-data" id="formRegulasi">
                    @csrf

                    {{-- Judul --}}
                    <div class="mb-4">
                        <label class="form-label" for="judul">
                            Judul Informasi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="judul"
                               name="judul"
                               class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul') }}"
                               placeholder="Contoh: Peraturan Karantina Ikan No. 12 Tahun 2026"
                               maxlength="255"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label class="form-label" for="deskripsi">
                            Deskripsi / Isi Informasi <span class="text-danger">*</span>
                        </label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  rows="6"
                                  maxlength="5000"
                                  placeholder="Tuliskan isi informasi, ringkasan peraturan, atau pengumuman secara lengkap..."
                                  required>{{ old('deskripsi') }}</textarea>
                        <div class="char-counter mt-1" id="charCounter">0 / 5000 karakter</div>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Lampiran --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Lampiran <span class="text-muted fw-normal">(Opsional — maks. 2 MB)</span>
                        </label>
                        <div class="upload-zone" id="uploadZone">
                            <input type="file"
                                   name="file_upload"
                                   id="file_upload"
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                            <div id="uploadPlaceholder">
                                <div style="width:56px;height:56px;background:linear-gradient(135deg,#e0f2fe,#bae6fd);
                                            border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;">
                                    <i class="ti ti-cloud-upload" style="font-size:1.6rem;color:#0369a1;"></i>
                                </div>
                                <p class="mb-1 fw-semibold text-dark" style="font-size:0.9rem;">
                                    Seret file ke sini atau klik untuk memilih
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.78rem;">
                                    Foto: JPG, PNG, GIF, WebP &nbsp;|&nbsp; Dokumen: PDF, Word, Excel, PPT, ZIP, RAR
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.75rem; margin-top:4px;">
                                    Ukuran maksimal: <strong>2 MB</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Preview foto --}}
                        <div class="upload-preview" id="previewFoto">
                            <img id="previewImg" src="" alt="Preview">
                        </div>

                        {{-- Info dokumen --}}
                        <div class="upload-info" id="uploadInfo">
                            <i class="ti ti-file-text text-primary fs-4"></i>
                            <div>
                                <div class="fw-semibold text-dark" id="uploadFileName" style="font-size:0.85rem;"></div>
                                <div class="text-muted" id="uploadFileSize" style="font-size:0.75rem;"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-ghost-danger ms-auto"
                                    id="clearFile" title="Hapus file">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>

                        @error('file_upload')
                            <div class="text-danger mt-2" style="font-size:0.83rem;">
                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"
                                style="border-radius:10px;" id="btnSubmit">
                            <i class="ti ti-send me-1"></i> Publikasikan
                        </button>
                        <a href="{{ route('regulasi.index') }}"
                           class="btn btn-ghost-secondary px-4 py-2"
                           style="border-radius:10px;">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Character counter ─────────────────────────────────────
    const deskripsi   = document.getElementById('deskripsi');
    const charCounter = document.getElementById('charCounter');

    function updateCounter() {
        const len = deskripsi.value.length;
        charCounter.textContent = len + ' / 5000 karakter';
        charCounter.className   = 'char-counter mt-1';
        if (len > 4500) charCounter.classList.add('danger');
        else if (len > 4000) charCounter.classList.add('warning');
    }

    deskripsi.addEventListener('input', updateCounter);
    updateCounter();

    // ── File upload preview ───────────────────────────────────
    const fileInput    = document.getElementById('file_upload');
    const uploadZone   = document.getElementById('uploadZone');
    const previewFoto  = document.getElementById('previewFoto');
    const previewImg   = document.getElementById('previewImg');
    const uploadInfo   = document.getElementById('uploadInfo');
    const uploadFName  = document.getElementById('uploadFileName');
    const uploadFSize  = document.getElementById('uploadFileSize');
    const clearBtn     = document.getElementById('clearFile');
    const placeholder  = document.getElementById('uploadPlaceholder');

    function formatBytes(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024)    return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }

    function handleFile(file) {
        if (!file) return;

        // Cek ukuran di sisi klien
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file melebihi batas 2 MB. Silakan pilih file yang lebih kecil.');
            fileInput.value = '';
            return;
        }

        const isImage = file.type.startsWith('image/');
        placeholder.style.display = 'none';

        if (isImage) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewFoto.style.display = 'block';
            };
            reader.readAsDataURL(file);
            uploadInfo.style.display = 'none';
        } else {
            previewFoto.style.display = 'none';
            uploadFName.textContent   = file.name;
            uploadFSize.textContent   = formatBytes(file.size);
            uploadInfo.style.display  = 'flex';
        }
    }

    fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

    // Drag & drop
    uploadZone.addEventListener('dragover', e => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt  = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });

    // Clear file
    clearBtn.addEventListener('click', () => {
        fileInput.value      = '';
        previewFoto.style.display = 'none';
        uploadInfo.style.display  = 'none';
        placeholder.style.display = '';
    });

    // ── Loading state submit ──────────────────────────────────
    document.getElementById('formRegulasi').addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mempublikasikan...';
    });
});
</script>

@endsection
