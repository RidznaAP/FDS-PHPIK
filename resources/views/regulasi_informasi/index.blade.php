@extends('layouts.app')

@section('title', 'Regulasi Informasi')
@section('page_title', 'Regulasi Informasi')
@section('page_subtitle', 'Informasi dan regulasi terbaru dari Deputi Karantina Ikan')

@section('content')

{{-- Alerts --}}
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

<style>
    /* ── Regulasi Informasi Premium Design ── */
    .regulasi-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 60%, #0ea5e9 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .regulasi-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .regulasi-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 30%;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    /* ── Card ── */
    .regulasi-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
        height: 100%;
        cursor: pointer;
    }
    .regulasi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(37,99,235,.13), 0 2px 8px rgba(0,0,0,.06);
    }
    .regulasi-card:hover .card-overlay-hint {
        opacity: 1;
    }
    /* Overlay "klik untuk detail" saat hover */
    .card-media-wrapper {
        position: relative;
        overflow: hidden;
    }
    .card-overlay-hint {
        position: absolute;
        inset: 0;
        background: rgba(37,99,235,0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.22s ease;
        border-radius: 0;
    }
    .card-overlay-hint span {
        background: rgba(255,255,255,0.95);
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 7px 18px;
        border-radius: 30px;
        letter-spacing: 0.02em;
        box-shadow: 0 2px 12px rgba(0,0,0,.15);
    }
    .regulasi-card .card-img-top {
        height: 200px;
        object-fit: cover;
        width: 100%;
        display: block;
    }
    .regulasi-card .card-foto-placeholder {
        height: 200px;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .regulasi-badge-tipe {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .regulasi-card .card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }
    .regulasi-card .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .regulasi-card .card-text {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.6;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }
    .regulasi-card .read-more-hint {
        font-size: 0.75rem;
        color: #2563eb;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: auto;
    }
    .regulasi-card .card-footer {
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 0.75rem 1.25rem;
        font-size: 0.75rem;
    }
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
    }
    .empty-state .empty-icon {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
    }

    /* ── Detail Modal ── */
    .modal-regulasi .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0,0,0,.18);
    }
    .modal-regulasi .modal-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        border: none;
        padding: 1.5rem 2rem;
    }
    .modal-regulasi .modal-body {
        padding: 0;
        max-height: 80vh;
        overflow-y: auto;
    }
    .modal-regulasi .modal-body::-webkit-scrollbar { width: 4px; }
    .modal-regulasi .modal-body::-webkit-scrollbar-track { background: #f1f5f9; }
    .modal-regulasi .modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .modal-regulasi .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
    }
    .detail-foto {
        width: 100%;
        max-height: 380px;
        object-fit: contain;
        background: #0f172a;
        display: block;
    }
    .detail-dokumen-box {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem;
    }
    .detail-deskripsi {
        padding: 1.5rem 2rem;
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.85;
        white-space: pre-line;
        word-break: break-word;
    }
    .detail-meta {
        padding: 0 2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
</style>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- HERO HEADER                                                           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="regulasi-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge" style="background:rgba(255,255,255,0.15);font-size:0.65rem;letter-spacing:0.08em;font-weight:700;">
                    📋 DEPUTI KARANTINA IKAN
                </span>
            </div>
            <h2 class="text-white fw-bold mb-1" style="font-size:1.5rem;letter-spacing:-0.02em;">
                Regulasi &amp; Informasi
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,0.7);font-size:0.85rem;">
                Informasi, peraturan, dan pengumuman resmi dari Deputi Karantina Ikan.
                Dipublikasikan: <strong class="text-white">{{ $regulasis->total() }} dokumen</strong>
            </p>
        </div>
        @if(Auth::user()->isPusat() || Auth::user()->isDeveloper())
        <a href="{{ route('regulasi.create') }}"
           class="btn btn-light fw-bold px-4 py-2 shadow-sm"
           style="border-radius:10px;font-size:0.85rem;">
            <i class="ti ti-plus me-1"></i> Tambah Informasi
        </a>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- GRID KARTU                                                            --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@if($regulasis->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="ti ti-clipboard-text text-primary" style="font-size:2rem;"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Belum Ada Informasi</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Belum ada regulasi atau informasi yang dipublikasikan.<br>
            @if(Auth::user()->isPusat() || Auth::user()->isDeveloper())
                Klik <strong>Tambah Informasi</strong> untuk memulai.
            @else
                Silakan cek kembali nanti.
            @endif
        </p>
    </div>
@else
    <div class="row g-4">
        @foreach($regulasis as $item)
        <div class="col-12 col-sm-6 col-lg-4">
            {{-- Seluruh card bisa diklik untuk buka modal detail --}}
            <div class="card regulasi-card"
                 data-bs-toggle="modal"
                 data-bs-target="#detailModal"
                 data-id="{{ $item->id }}"
                 data-judul="{{ $item->judul }}"
                 data-deskripsi="{{ $item->deskripsi }}"
                 data-tipe="{{ $item->tipe_lampiran }}"
                 data-path="{{ $item->path_file ? Storage::disk('public')->url($item->path_file) : '' }}"
                 data-download="{{ $item->path_file ? route('regulasi.download', $item->id) : '' }}"
                 data-namafile="{{ $item->nama_file ?? '' }}"
                 data-ukuran="{{ $item->ukuran_file ?? '' }}"
                 data-uploader="{{ $item->user->name ?? '-' }}"
                 data-tanggal="{{ $item->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}"
                 data-candelete="{{ (Auth::user()->isPusat() || Auth::user()->isDeveloper()) ? '1' : '0' }}"
                 data-hapusurl="{{ route('regulasi.destroy', $item->id) }}">

                {{-- Media area --}}
                <div class="card-media-wrapper">
                    @if($item->isFoto() && $item->path_file)
                        <img src="{{ Storage::disk('public')->url($item->path_file) }}"
                             alt="{{ $item->judul }}"
                             class="card-img-top"
                             loading="lazy">
                    @else
                        <div class="card-foto-placeholder">
                            @if($item->isDokumen())
                                <div class="text-center">
                                    <i class="ti ti-file-text" style="font-size:2.5rem;color:#0ea5e9;"></i>
                                    <div class="mt-1" style="font-size:0.7rem;color:#64748b;font-weight:600;">DOKUMEN</div>
                                </div>
                            @else
                                <div class="text-center">
                                    <i class="ti ti-news" style="font-size:2.5rem;color:#0ea5e9;"></i>
                                    <div class="mt-1" style="font-size:0.7rem;color:#64748b;font-weight:600;">INFORMASI</div>
                                </div>
                            @endif
                        </div>
                    @endif
                    {{-- Hover overlay --}}
                    <div class="card-overlay-hint">
                        <span><i class="ti ti-eye me-1"></i> Lihat Selengkapnya</span>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Badge tipe + tanggal --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        @if($item->isFoto())
                            <span class="regulasi-badge-tipe" style="background:#e0f2fe;color:#0369a1;">
                                <i class="ti ti-photo" style="font-size:0.7rem;"></i> Foto
                            </span>
                        @elseif($item->isDokumen())
                            <span class="regulasi-badge-tipe" style="background:#fef3c7;color:#92400e;">
                                <i class="ti ti-file" style="font-size:0.7rem;"></i> Dokumen
                            </span>
                        @else
                            <span class="regulasi-badge-tipe" style="background:#f0fdf4;color:#166534;">
                                <i class="ti ti-info-circle" style="font-size:0.7rem;"></i> Informasi
                            </span>
                        @endif
                        <span class="text-muted" style="font-size:0.72rem;">
                            {{ $item->created_at->locale('id')->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Judul --}}
                    <h5 class="card-title">{{ $item->judul }}</h5>

                    {{-- Deskripsi (truncated) --}}
                    <p class="card-text">{{ $item->deskripsi }}</p>

                    {{-- Hint baca selengkapnya --}}
                    <div class="read-more-hint">
                        <i class="ti ti-arrow-right" style="font-size:0.8rem;"></i>
                        Baca Selengkapnya
                    </div>
                </div>

                {{-- Footer: info uploader + hapus --}}
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-xs rounded-3"
                              style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);font-size:0.6rem;color:#fff;">
                            {{ strtoupper(substr($item->user->name ?? '?', 0, 1)) }}
                        </span>
                        <span class="text-muted" style="font-size:0.72rem;">
                            {{ $item->user->name ?? '-' }}
                        </span>
                    </div>
                    @if(Auth::user()->isPusat() || Auth::user()->isDeveloper())
                        {{-- Tombol hapus: stop propagation agar tidak buka modal --}}
                        <form method="POST" action="{{ route('regulasi.destroy', $item->id) }}"
                              class="d-inline"
                              onsubmit="return confirm('Hapus regulasi ini?')"
                              onclick="event.stopPropagation()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-ghost-danger py-0 px-2"
                                    title="Hapus" style="font-size:0.75rem;">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($regulasis->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $regulasis->links() }}
    </div>
    @endif
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- SATU MODAL DETAIL UNIVERSAL (diisi via JS saat card diklik)           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-regulasi" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <div class="d-flex align-items-start gap-3 flex-fill pe-3">
                    <div id="modalBadgeIcon"
                         style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:12px;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                        <i class="ti ti-clipboard-text text-white" style="font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="modalJudul" style="line-height:1.35;"></h5>
                        <div id="modalMeta" class="d-flex align-items-center gap-2 flex-wrap" style="font-size:0.75rem;color:rgba(255,255,255,0.65);"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body (scrollable) --}}
            <div class="modal-body">

                {{-- Area foto --}}
                <div id="modalFotoArea" style="display:none;">
                    <img id="modalFotoImg" src="" alt="" class="detail-foto">
                </div>

                {{-- Area dokumen --}}
                <div id="modalDokumenArea" class="detail-dokumen-box" style="display:none;">
                    <div style="width:48px;height:48px;background:#dbeafe;border-radius:12px;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-file-text" style="font-size:1.4rem;color:#1d4ed8;"></i>
                    </div>
                    <div class="flex-fill">
                        <div id="modalNamaFile" class="fw-bold text-dark" style="font-size:0.9rem;"></div>
                        <div id="modalUkuranFile" class="text-muted" style="font-size:0.78rem;"></div>
                    </div>
                    <a id="modalDownloadBtn" href="#" class="btn btn-primary btn-sm fw-bold px-3"
                       style="border-radius:8px;white-space:nowrap;">
                        <i class="ti ti-download me-1"></i> Unduh
                    </a>
                </div>

                {{-- Deskripsi lengkap --}}
                <div class="detail-deskripsi" id="modalDeskripsi"></div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span id="modalAvatarUploader"
                          class="avatar avatar-sm rounded-3"
                          style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);font-size:0.75rem;color:#fff;"></span>
                    <div>
                        <div id="modalUploaderName" class="fw-semibold text-dark" style="font-size:0.82rem;"></div>
                        <div class="text-muted" style="font-size:0.72rem;">Diunggah oleh</div>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    {{-- Tombol unduh foto di footer --}}
                    <a id="modalFotoDownloadBtn" href="#" class="btn btn-sm btn-outline-primary"
                       style="border-radius:8px;display:none;">
                        <i class="ti ti-download me-1"></i> Unduh Foto
                    </a>
                    {{-- Tombol hapus (hanya Pusat/Dev) --}}
                    <form id="modalHapusForm" method="POST" action="#"
                          onsubmit="return confirm('Hapus regulasi ini?')"
                          style="display:none;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger fw-bold"
                                style="border-radius:8px;">
                            <i class="ti ti-trash me-1"></i> Hapus
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-ghost-secondary"
                            data-bs-dismiss="modal" style="border-radius:8px;">
                        Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailModal');

    detailModal.addEventListener('show.bs.modal', function (e) {
        const card = e.relatedTarget;
        if (!card) return;

        const d = card.dataset;

        // ── Judul & meta ───────────────────────────────
        document.getElementById('modalJudul').textContent = d.judul;
        document.getElementById('modalDeskripsi').textContent = d.deskripsi;

        // Meta: tipe badge + tanggal
        const tipeBadge = {
            foto:      `<span style="background:#e0f2fe;color:#0369a1;padding:2px 10px;border-radius:20px;font-size:0.65rem;font-weight:700;text-transform:uppercase;"><i class="ti ti-photo"></i> Foto</span>`,
            dokumen:   `<span style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:20px;font-size:0.65rem;font-weight:700;text-transform:uppercase;"><i class="ti ti-file"></i> Dokumen</span>`,
            none:      `<span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);padding:2px 10px;border-radius:20px;font-size:0.65rem;font-weight:700;text-transform:uppercase;">Informasi</span>`,
        };
        const iconMap = {
            foto:    'ti-photo',
            dokumen: 'ti-file-text',
            none:    'ti-clipboard-text',
        };

        document.getElementById('modalMeta').innerHTML =
            (tipeBadge[d.tipe] || tipeBadge.none) +
            `<span><i class="ti ti-calendar" style="font-size:0.75rem;"></i> ${d.tanggal}</span>`;

        // Header icon
        const iconEl = document.querySelector('#modalBadgeIcon i');
        iconEl.className = 'ti ' + (iconMap[d.tipe] || 'ti-clipboard-text') + ' text-white';
        iconEl.style.fontSize = '1.2rem';

        // ── Lampiran: foto ─────────────────────────────
        const fotoArea    = document.getElementById('modalFotoArea');
        const dokumenArea = document.getElementById('modalDokumenArea');
        const fotoDownBtn = document.getElementById('modalFotoDownloadBtn');

        if (d.tipe === 'foto' && d.path) {
            fotoArea.style.display = '';
            dokumenArea.style.display = 'none';
            document.getElementById('modalFotoImg').src = d.path;
            document.getElementById('modalFotoImg').alt = d.judul;
            fotoDownBtn.href        = d.download;
            fotoDownBtn.style.display = '';
        } else if (d.tipe === 'dokumen' && d.path) {
            fotoArea.style.display = 'none';
            dokumenArea.style.display = 'flex';
            document.getElementById('modalNamaFile').textContent  = d.namafile || 'File Dokumen';
            document.getElementById('modalUkuranFile').textContent = d.ukuran  || '';
            document.getElementById('modalDownloadBtn').href        = d.download;
            fotoDownBtn.style.display = 'none';
        } else {
            fotoArea.style.display    = 'none';
            dokumenArea.style.display = 'none';
            fotoDownBtn.style.display = 'none';
        }

        // ── Uploader info ──────────────────────────────
        const uploaderName = d.uploader || '-';
        document.getElementById('modalUploaderName').textContent  = uploaderName;
        document.getElementById('modalAvatarUploader').textContent = uploaderName.charAt(0).toUpperCase();

        // ── Hapus (hanya jika candelete) ───────────────
        const hapusForm = document.getElementById('modalHapusForm');
        if (d.candelete === '1') {
            hapusForm.action = d.hapusurl;
            hapusForm.style.display = '';
        } else {
            hapusForm.style.display = 'none';
        }
    });

    // Reset modal saat ditutup
    detailModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalFotoImg').src = '';
    });
});
</script>

@endsection
