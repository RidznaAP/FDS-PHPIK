@extends('layouts.app')

@section('title', $judulModul)
@section('page_title', 'Modul ' . $judulModul)
@section('page_subtitle', Auth::user()->isPusat() && ($modul === 'pelaporan' || $modul === 'pelaksanaan_pasif') 
    ? 'Peninjauan dan pemantauan dokumen laporan yang diunggah oleh BKHIT dan BBKHIT' 
    : 'Unggah dan kelola dokumen hasil seminar per UPT')

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

    @if($modul === 'evaluasi')
    <div class="col-12 mb-1">
        <div class="alert alert-info border-info border-start border-4 bg-white shadow-sm d-flex align-items-center gap-3 py-3">
            <i class="ti ti-info-circle text-info fs-2"></i>
            <div>
                <h4 class="alert-title mb-1">Mengenai Evaluasi Pemantauan HPIK</h4>
                <p class="text-muted mb-0 small">
                    Pemantauan HPIK hasil kegiatan Pemantauan HPIK dituangkan dalam bentuk rekomendasi pengambilan kebijakan terkait penetapan status bebas HPIK, penetapan atau pencabutan HPIK, penyusunan peta sebaran HPIK dan perbaikan pelaksanaan Pemantauan HPIK. Dokumen di bawah ini merupakan distribusi dokumen rekomendasi tersebut ke UPT terkait.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($modul === 'pelaksanaan_pasif')
    <div class="col-12 mb-1">
        <div class="alert alert-warning border-warning border-start border-4 bg-white shadow-sm d-flex align-items-center gap-3 py-3">
            <i class="ti ti-file-description text-warning fs-2"></i>
            <div>
                <h4 class="alert-title mb-1">Pelaksanaan Pasif — Dokumen Pendukung</h4>
                <p class="text-muted mb-0 small">
                    Unggah dokumen-dokumen yang berkaitan dengan kegiatan Pelaksanaan Pasif, seperti dokumen laporan hasil pemantauan, berita acara, surat tugas, atau dokumen administratif lainnya yang mendukung kegiatan pemantauan HPIK.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($modul === 'pelaporan' && Auth::user()->isPusat())
    <div class="col-12 mb-1">
        <div class="alert alert-primary border-primary border-start border-4 bg-white shadow-sm d-flex align-items-center gap-3 py-3">
            <i class="ti ti-shield-check text-primary fs-2"></i>
            <div>
                <h4 class="alert-title mb-1">Pusat Pelaporan Lapangan (Monitoring Admin Pusat)</h4>
                <p class="text-muted mb-0 small">
                    Halaman ini menampilkan seluruh dokumen Laporan Perencanaan, Kompilasi Hasil Uji, dan Laporan Akhir Pemantauan yang diunggah oleh BKHIT dan BBKHIT di daerah. Gunakan filter tahun dan pencarian untuk mengelompokkan dokumen.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════ --}}
    {{-- Kolom Kiri: Form Upload (Jika diizinkan) --}}
    {{-- ═══════════════════════════════════════ --}}
    @if($canUpload)
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

                    @if($modul === 'evaluasi')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tujuan Dokumen (Ditujukan Kepada)</label>
                        <select name="target_user_id" class="form-select shadow-sm">
                            <option value="">Semua Admin (Umum)</option>
                            @foreach($uptUsers as $u)
                                <option value="{{ $u->id }}" {{ old('target_user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-hint small mt-1">Biarkan "Semua Admin" jika dokumen ini berlaku secara umum.</div>
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
                        <label class="form-label fw-bold">File Dokumen <span class="text-muted fw-normal">(maks 2MB)</span></label>
                        <div class="upload-zone border border-dashed rounded-3 p-4 text-center"
                             id="upload-zone"
                             onclick="document.getElementById('file-input').click()"
                             style="cursor:pointer; transition: all .2s;">
                            <input type="file" name="file_upload" id="file-input" class="d-none @error('file_upload') is-invalid @enderror"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar"
                                onchange="previewFile(this)">
                            <div id="upload-placeholder">
                                <i class="ti ti-file-upload text-muted" style="font-size:2.5rem;"></i>
                                <div class="fw-bold text-muted mt-2">Klik untuk pilih file</div>
                                <div class="text-muted small mt-1">PDF, Word, Excel, PPT, ZIP, RAR</div>
                                <div class="text-muted small text-danger fw-bold">Maks. 2 MB</div>
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

                    <div class="mb-3">
                        <label class="form-label fw-bold">Atau Link Google Drive <span class="text-muted fw-normal">(opsional)</span></label>
                        <div class="input-group input-group-flat shadow-sm">
                            <span class="input-group-text">
                                <i class="ti ti-brand-google-drive text-success"></i>
                            </span>
                            <input type="url" name="link_drive" class="form-control @error('link_drive') is-invalid @enderror"
                                value="{{ old('link_drive') }}"
                                placeholder="https://drive.google.com/...">
                        </div>
                        <div class="form-hint small mt-1">Gunakan link ini jika ukuran file melebihi 2MB. Pastikan akses link sudah diatur.</div>
                        @error('link_drive')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                    @if($modul === 'pelaporan')
                        Dokumen yg diunggah dapat diunduh dan ditinjau oleh BBKHIT & Pusat.
                    @else
                        Dokumen akan dikirim dan diberikan notifikasi kepada UPT yang dipilih.
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════ --}}
    {{-- Kolom Kanan: Daftar Dokumen & Filter    --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="{{ $canUpload ? 'col-lg-8' : 'col-lg-12' }}">
        
        {{-- ── Filter Control Bar ── --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3 px-4">
                <form method="GET" action="{{ route('seminar.index', $modul) }}" class="row g-2 align-items-center">
                    {{-- Filter Tahun Upload --}}
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="ti ti-calendar me-1"></i>Tahun Upload</label>
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Role Pengunggah (Hanya untuk Admin Pusat / Developer) --}}
                    @if(Auth::user()->isPusat() || Auth::user()->isDeveloper())
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="ti ti-building me-1"></i>Asal Instansi</label>
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Pengunggah</option>
                            <option value="bkhit" {{ request('role') == 'bkhit' ? 'selected' : '' }}>🟢 BKHIT</option>
                            <option value="bbkhit" {{ request('role') == 'bbkhit' ? 'selected' : '' }}>🟡 BBKHIT</option>
                        </select>
                    </div>
                    @endif

                    {{-- Search Input --}}
                    <div class="{{ Auth::user()->isPusat() || Auth::user()->isDeveloper() ? 'col-md-4' : 'col-md-6' }} col-12">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="ti ti-search me-1"></i>Pencarian Dokumen</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari judul, keterangan, UPT..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-2 col-12 d-flex align-items-end gap-1 mt-md-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ti ti-filter me-1"></i>Filter
                        </button>
                        @if(request('tahun') || request('role') || request('search'))
                        <a href="{{ route('seminar.index', $modul) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Tabel / List Dokumen ── --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="fw-bold fs-5">
                    <i class="ti ti-files me-2 text-muted"></i>Daftar Dokumen {{ $judulModul }}
                    @if(request('tahun'))
                        <span class="badge bg-primary-lt ms-2">Tahun {{ request('tahun') }}</span>
                    @endif
                </div>
                <span class="badge bg-{{ $modul === 'pelaporan' ? 'blue' : 'purple' }}-lt px-3 py-2 fs-6">
                    Total: {{ $dokumens->total() }} dokumen
                </span>
            </div>

            @if($dokumens->isEmpty())
            <div class="card-body p-0">
                <div class="empty-state py-5 text-center">
                    <div class="empty-state-icon mb-3">
                        <i class="ti ti-folder-open text-muted" style="font-size:3rem;"></i>
                    </div>
                    <h4 class="fw-bold">Belum Ada Dokumen</h4>
                    <p class="text-muted">
                        @if(request('tahun') || request('search') || request('role'))
                            Tidak ada dokumen yang sesuai dengan filter pencarian.
                        @else
                            Belum ada dokumen {{ strtolower($judulModul) }} yang diunggah.
                        @endif
                    </p>
                    @if(request('tahun') || request('search') || request('role'))
                        <a href="{{ route('seminar.index', $modul) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-refresh me-1"></i>Reset Filter
                        </a>
                    @endif
                </div>
            </div>
            @else
            <div class="list-group list-group-flush">
                @foreach($dokumens as $dok)
                @php
                    $ext = $dok->nama_file ? strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION)) : null;
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
                    [$icon, $color] = $iconMap[$ext] ?? ($dok->link_drive ? ['ti-brand-google-drive', 'text-success'] : ['ti-file', 'text-muted']);

                    // Role Badge
                    $roleClass = match(optional($dok->user)->role) {
                        'bkhit'     => 'bg-success-lt text-success',
                        'bbkhit'    => 'bg-warning-lt text-warning',
                        'pusat'     => 'bg-purple-lt text-purple',
                        'developer' => 'bg-danger-lt text-danger',
                        default     => 'bg-secondary-lt text-secondary',
                    };
                    $roleLabel = match(optional($dok->user)->role) {
                        'bkhit'     => 'BKHIT',
                        'bbkhit'    => 'BBKHIT',
                        'pusat'     => 'Admin Pusat',
                        'developer' => 'Developer',
                        default     => 'Pengguna',
                    };
                @endphp
                <div class="list-group-item px-4 py-3 hover-shadow">
                    <div class="d-flex align-items-start gap-3">
                        {{-- Ikon file --}}
                        <div class="flex-shrink-0 mt-1">
                            <i class="ti {{ $icon }} {{ $color }}" style="font-size:2.4rem;"></i>
                        </div>
                        {{-- Info --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="fw-bold text-dark fs-5">{{ $dok->judul }}</span>
                                {{-- Badge UPT & Role Pengunggah --}}
                                <span class="badge {{ $roleClass }} px-2 py-1">
                                    <i class="ti ti-building me-1"></i>{{ $dok->user->upt_asal ?? $dok->user->name ?? 'UPT' }} ({{ $roleLabel }})
                                </span>
                            </div>

                            <div class="text-muted small d-flex align-items-center flex-wrap gap-3 mt-1">
                                {{-- Pengunggah --}}
                                <span>
                                    <i class="ti ti-user me-1 text-primary"></i>Pengunggah: <strong>{{ $dok->user->name ?? 'Unknown' }}</strong>
                                </span>

                                {{-- Nama File / Drive --}}
                                @if($dok->nama_file)
                                <span><i class="ti ti-paperclip me-1"></i>{{ $dok->nama_file }} ({{ $dok->ukuran_file }})</span>
                                @endif
                                @if($dok->link_drive)
                                <span class="text-success fw-bold"><i class="ti ti-brand-google-drive me-1"></i>Google Drive Link</span>
                                @endif

                                {{-- Waktu Upload Jelas --}}
                                <span class="text-dark">
                                    <i class="ti ti-clock-check me-1 text-info"></i>Waktu Upload: <strong>{{ $dok->created_at->translatedFormat('d M Y, H:i') }} WIB</strong> 
                                    <span class="text-muted">({{ $dok->created_at->diffForHumans() }})</span>
                                </span>
                            </div>

                            @if($modul === 'evaluasi')
                            <div class="mt-2">
                                @if($dok->target_user_id)
                                    <span class="badge bg-purple-lt border border-purple-subtle"><i class="ti ti-arrow-forward-up me-1"></i>Tertuju: {{ $dok->targetUser->name ?? 'UPT' }}</span>
                                @else
                                    <span class="badge bg-secondary-lt border border-secondary-subtle"><i class="ti ti-users me-1"></i>Khusus Semua BKHIT/BBKHIT</span>
                                @endif
                            </div>
                            @endif

                            @if($dok->keterangan)
                            <div class="text-muted small mt-2 fst-italic border-start border-3 border-primary ps-2 bg-light py-1 rounded-end">
                                {{ Str::limit($dok->keterangan, 150) }}
                            </div>
                            @endif
                        </div>

                        {{-- Aksi --}}
                        <div class="flex-shrink-0 d-flex gap-2 align-self-center">
                            <a href="{{ route('seminar.download', $dok->id) }}"
                               class="btn btn-sm {{ $dok->path_file ? 'btn-outline-primary' : 'btn-outline-success' }}"
                               {{ !$dok->path_file ? 'target="_blank"' : '' }}
                               title="{{ $dok->path_file ? 'Download dokumen' : 'Buka link Google Drive' }}">
                                <i class="ti {{ $dok->path_file ? 'ti-download' : 'ti-external-link' }} me-1"></i>
                                {{ $dok->path_file ? 'Unduh' : 'Buka Link' }}
                            </a>
                            @if(Auth::id() === $dok->user_id || Auth::user()->isPusat() || Auth::user()->isDeveloper())
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
if (zone) {
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
}
</script>
@endpush
