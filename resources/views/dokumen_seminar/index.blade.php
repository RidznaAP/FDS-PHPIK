@extends('layouts.app')

@section('title', $judulModul)
@section('page_title', 'Modul ' . $judulModul)
@section('page_subtitle', 'Unggah dan kelola dokumen hasil seminar per UPT')

@section('content')

{{-- Alert sukses/error --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
        <i class="ti ti-circle-check text-success fs-4"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- ═══════════════════════════════════════ --}}
    {{-- Kolom Kiri: Form Upload                 --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm overflow-hidden sticky-top" style="top:1rem;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-3
                {{ $modul === 'pelaporan' ? 'bg-blue-lt' : 'bg-purple-lt' }}">
                <div class="{{ $modul === 'pelaporan' ? 'bg-blue' : 'bg-purple' }} text-white p-2 rounded-3">
                    <i class="ti ti-cloud-upload fs-3"></i>
                </div>
                <div>
                    <div class="{{ $modul === 'pelaporan' ? 'text-blue' : 'text-purple' }} small fw-bold text-uppercase" style="letter-spacing:.05em;">UNGGAH DOKUMEN</div>
                    <div class="fw-bold">{{ $judulModul }} Baru</div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('seminar.store', $modul) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if($modul === 'pelaporan')
                    <div class="alert alert-info border-0 rounded-3 mb-4 shadow-sm bg-blue-lt">
                        <div class="fw-bold mb-2 text-blue"><i class="ti ti-checkup-list me-1"></i>Dokumen Yang Perlu Diupload:</div>
                        <ul class="mb-0 small ps-3 text-dark">
                            <li class="mb-1">Laporan Perencanaan</li>
                            <li class="mb-1">Laporan Kompilasi Hasil Uji</li>
                            <li class="mb-1">Laporan Akhir Pemantauan</li>
                            <li>Laporan Melaksanakan Seminar Regional</li>
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label required fw-bold">Judul Dokumen</label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                            value="{{ old('judul') }}"
                            placeholder="Contoh: Laporan Seminar HPIK 2026 - BKHIT Jambi" required>
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-bold">File Dokumen</label>
                        <div class="upload-zone border border-dashed rounded-3 p-4 text-center"
                             id="upload-zone"
                             onclick="document.getElementById('file-input').click()"
                             style="cursor:pointer; transition: all .2s;">
                            <input type="file" name="file_upload" id="file-input" class="d-none @error('file_upload') is-invalid @enderror"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" required
                                onchange="previewFile(this)">
                            <div id="upload-placeholder">
                                <i class="ti ti-file-upload text-muted" style="font-size:2.5rem;"></i>
                                <div class="fw-bold text-muted mt-2">Klik untuk pilih file</div>
                                <div class="text-muted small mt-1">PDF, Word, Excel, PPT, ZIP, RAR</div>
                                <div class="text-muted small">Maks. 20 MB</div>
                            </div>
                            <div id="upload-preview" class="d-none">
                                <i class="ti ti-file-check text-success" style="font-size:2rem;"></i>
                                <div class="fw-bold text-success mt-2" id="file-name-display">—</div>
                                <div class="text-muted small" id="file-size-display">—</div>
                                <div class="text-muted small mt-2">Klik untuk ganti file</div>
                            </div>
                        </div>
                        @error('file_upload')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Deskripsi singkat dokumen ini...">{{ old('keterangan') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-{{ $modul === 'pelaporan' ? 'primary' : 'purple' }} w-100 btn-pill fw-bold shadow-sm">
                        <i class="ti ti-cloud-upload me-2"></i>Unggah Dokumen
                    </button>
                </form>
            </div>
            <div class="card-footer bg-light border-0 py-3">
                <div class="text-muted small text-center">
                    <i class="ti ti-info-circle me-1"></i>
                    Dokumen yg diunggah dapat diunduh dan ditinjau oleh BBKHIT & Pusat.
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- Kolom Kanan: Daftar Dokumen             --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="fw-bold fs-5">
                    <i class="ti ti-files me-2 text-muted"></i>Daftar Dokumen {{ $judulModul }}
                </div>
                <span class="badge bg-{{ $modul === 'pelaporan' ? 'blue' : 'purple' }}-lt">
                    {{ $dokumens->total() }} dokumen
                </span>
            </div>

            @if($dokumens->isEmpty())
            <div class="card-body p-0">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ti ti-folder-open" style="font-size:1.75rem; color:#94a3b8;"></i>
                    </div>
                    <h4>Belum Ada Dokumen</h4>
                    <p>Unggah dokumen hasil seminar UPT Anda menggunakan form di samping kiri.</p>
                </div>
            </div>
            @else
            <div class="list-group list-group-flush">
                @foreach($dokumens as $dok)
                @php
                    $ext = strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION));
                    $iconMap = [
                        'pdf'  => ['ti-file-type-pdf', 'text-danger'],
                        'doc'  => ['ti-file-type-doc', 'text-blue'],
                        'docx' => ['ti-file-type-doc', 'text-blue'],
                        'xls'  => ['ti-file-type-xls', 'text-green'],
                        'xlsx' => ['ti-file-type-xls', 'text-green'],
                        'ppt'  => ['ti-file-type-ppt', 'text-orange'],
                        'pptx' => ['ti-file-type-ppt', 'text-orange'],
                        'zip'  => ['ti-file-zip', 'text-yellow'],
                        'rar'  => ['ti-file-zip', 'text-yellow'],
                    ];
                    [$icon, $color] = $iconMap[$ext] ?? ['ti-file', 'text-muted'];
                @endphp
                <div class="list-group-item px-4 py-3 hover-shadow">
                    <div class="d-flex align-items-start gap-3">
                        {{-- Ikon file --}}
                        <div class="flex-shrink-0">
                            <i class="ti {{ $icon }} {{ $color }}" style="font-size:2.2rem;"></i>
                        </div>
                        {{-- Info --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-truncate">{{ $dok->judul }}</div>
                            <div class="text-muted small mt-1">
                                <span class="me-3"><i class="ti ti-user me-1"></i>{{ $dok->user->name ?? 'Unknown' }}</span>
                                <span class="me-3"><i class="ti ti-file me-1"></i>{{ $dok->nama_file }}</span>
                                @if($dok->ukuran_file)
                                <span class="me-3"><i class="ti ti-database me-1"></i>{{ $dok->ukuran_file }}</span>
                                @endif
                                <span><i class="ti ti-clock me-1"></i>{{ $dok->created_at->diffForHumans() }}</span>
                            </div>
                            @if($dok->keterangan)
                            <div class="text-muted small mt-1 fst-italic">{{ Str::limit($dok->keterangan, 100) }}</div>
                            @endif
                        </div>
                        {{-- Aksi --}}
                        <div class="flex-shrink-0 d-flex gap-2">
                            <a href="{{ route('seminar.download', $dok->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Download dokumen">
                                <i class="ti ti-download me-1"></i>Unduh
                            </a>
                            @if(Auth::id() === $dok->user_id || Auth::user()->isPusat())
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus dokumen ini"
                                onclick="confirmAction('{{ route('seminar.destroy', $dok->id) }}', 'Hapus dokumen \'{{ addslashes($dok->judul) }}\'?', 'DELETE', 'btn-danger')">
                                <i class="ti ti-trash"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($dokumens->hasPages())
            <div class="card-footer border-0 bg-light py-3">
                {{ $dokumens->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('upload-placeholder').classList.add('d-none');
    document.getElementById('upload-preview').classList.remove('d-none');
    document.getElementById('file-name-display').textContent = file.name;
    const size = file.size >= 1048576
        ? (file.size / 1048576).toFixed(2) + ' MB'
        : (file.size / 1024).toFixed(2) + ' KB';
    document.getElementById('file-size-display').textContent = size;
}

// Drag & Drop
const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-primary', 'bg-blue-lt'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-primary', 'bg-blue-lt'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('border-primary', 'bg-blue-lt');
    const dt = e.dataTransfer;
    const fileInput = document.getElementById('file-input');
    fileInput.files = dt.files;
    previewFile(fileInput);
});
</script>
@endpush
