@extends('layouts.app')

@section('title', 'Pelaksanaan')
@section('page_title', 'Pelaksanaan Lapangan')
@section('page_subtitle', 'Data realisasi pengambilan sampel dan hasil uji laboratorium')

@section('content')
{{-- ═══ TOOLBAR ═══ --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <form method="GET" action="{{ route('pelaksanaan.index') }}" class="input-icon flex-grow-1" style="max-width:380px;">
        <span class="input-icon-addon"><i class="ti ti-search text-muted"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari lokasi, jenis ikan, provinsi…" value="{{ request('search') }}">
        @if(request('tahun'))<input type="hidden" name="tahun" value="{{ request('tahun') }}">@endif
        @if(request('lab'))<input type="hidden" name="lab" value="{{ request('lab') }}">@endif
    </form>

    <form id="filter-form-pelaksanaan" method="GET" action="{{ route('pelaksanaan.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <select name="tahun" class="form-select form-select-sm" style="width:135px;" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <select name="lab" class="form-select form-select-sm" style="width:175px;" onchange="this.form.submit()">
            <option value="">Semua Status Lab</option>
            <option value="done"    {{ request('lab') == 'done'    ? 'selected' : '' }}>Sudah Diuji</option>
            <option value="pending" {{ request('lab') == 'pending' ? 'selected' : '' }}>Belum Diuji</option>
        </select>
        @if(request('search') || request('tahun') || request('lab'))
            <a href="{{ route('pelaksanaan.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-x me-1"></i>Reset
            </a>
        @endif
    </form>

    <div class="ms-auto d-flex gap-2">
        <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-green text-white rounded-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                <i class="ti ti-map-pin" style="font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Data Pelaksanaan Lapangan</div>
                <div class="text-muted small">{{ $pelaksanaans->total() }} data ditemukan</div>
            </div>
        </div>
        <span class="badge bg-green px-3 py-2" style="font-size:.72rem;color:#fff;">{{ $pelaksanaans->total() }} total</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('pelaksanaan.bulk-delete') }}" method="POST">
        @csrf
         <div class="table-responsive">
        <table class="table table-vcenter table-mobile-cards card-table table-hover align-middle">
            <thead>
                <tr>
                    <th class="w-1 px-3"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1">No</th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'lokasi_pengambilan_sampel', 'sort_order' => (request('sort_by') === 'lokasi_pengambilan_sampel' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by', 'lokasi_pengambilan_sampel') === 'lokasi_pengambilan_sampel' ? 'sort-active' : '' }}">
                            Objek & Lokasi Sampling
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'lokasi_pengambilan_sampel' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_ikan', 'sort_order' => (request('sort_by') === 'jenis_ikan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jenis_ikan' ? 'sort-active' : '' }}">
                            Media Pembawa
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jenis_ikan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="text-uppercase py-2 small fw-bold" style="letter-spacing: 0.05em; color: #64748b;">Instansi / Petugas</th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_pemantauan', 'sort_order' => (request('sort_by') === 'tanggal_pemantauan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by', 'tanggal_pemantauan') === 'tanggal_pemantauan' ? 'sort-active' : '' }}">
                            Sampel & Waktu
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'tanggal_pemantauan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status_lab', 'sort_order' => (request('sort_by') === 'status_lab' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'status_lab' ? 'sort-active' : '' }}">
                            Status Lab
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'status_lab' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center py-2 small fw-bold text-uppercase aksi-sticky-th" style="letter-spacing: 0.05em; color: #64748b;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input check-item"></td>
                    <td class="text-muted small">{{ $pelaksanaans->firstItem() + $key }}</td>
                    <td data-label="Wilayah / Lokasi">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-blue-lt text-uppercase fw-bold" style="font-size: 0.65rem;">{{ $item->perencanaan->jenis_mp ?? 'MP' }}</span>
                            @if($item->latitude && $item->longitude)
                                <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-azure" title="Lihat di Peta">
                                    <i class="ti ti-map-2 fs-3"></i>
                                </a>
                            @endif
                        </div>
                        <div class="fw-bold text-dark mb-0">{{ Str::limit($item->lokasi_pengambilan_sampel, 40) }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                    </td>
                    <td data-label="Media Pembawa">
                        @if($item->jenis_ikan)
                            <div class="fw-bold text-azure">{{ $item->jenis_ikan }}</div>
                            @if($item->nama_latin)<div class="text-muted small fst-italic" style="font-size: 0.75rem;">{{ $item->nama_latin }}</div>@endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td data-label="Instansi">
                        <div class="fw-bold text-dark small mb-0">{{ $item->perencanaan->user->upt_asal ?? $item->perencanaan->user->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;"><i class="ti ti-user-edit me-1"></i>{{ $item->perencanaan->user->name }}</div>
                    </td>
                    <td data-label="Sampel & Waktu">
                        <div class="fw-bold text-dark mb-1">
                            <i class="ti ti-test-pipe text-muted me-1"></i>{{ $item->jumlah_sampel }} <span class="small fw-normal text-muted">sampel</span>
                        </div>
                        <div class="small text-muted"><i class="ti ti-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d M Y') }}</div>
                        @if($item->jumlah_kematian > 0)
                            <div class="mt-1"><span class="badge bg-danger-lt text-danger" style="font-size: 0.6rem;">KEMATIAN: {{ $item->jumlah_kematian }}</span></div>
                        @endif
                    </td>
                    <td data-label="Status Lab">
                        @if($item->laboratorium)
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-success-lt text-success fw-bold text-start border border-success-subtle py-1">
                                    <i class="ti ti-check me-1"></i>{{ $item->laboratorium->hasil_uji }}
                                </span>
                                <div class="text-muted" style="font-size: 0.7rem;">Selesai: {{ $item->laboratorium->created_at->format('d/m/y') }}</div>
                            </div>
                        @else
                            <span class="badge bg-warning-lt text-warning fw-bold border border-warning-subtle py-1">
                                <i class="ti ti-clock me-1"></i>MENUNGGU UJI
                            </span>
                        @endif
                    </td>
                    <td class="aksi-sticky-td">
                        <div class="d-flex gap-1 justify-content-end">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-icon btn-white border-0 text-primary" title="Detail"><i class="ti ti-eye fs-2"></i></a>
                                @if(Auth::user()->isPusat() || Auth::user()->isDeveloper() || optional($item->perencanaan)->user_id == Auth::id())
                                    <a href="{{ route('pelaksanaan.edit', $item->id) }}" class="btn btn-icon btn-white border-0 text-warning" title="Edit"><i class="ti ti-pencil fs-2"></i></a>
                                    <button type="button" class="btn btn-icon btn-white border-0 text-danger" title="Hapus"
                                        onclick="confirmAction('{{ route('pelaksanaan.destroy', $item->id) }}', 'Hapus data ini?', 'DELETE', 'btn-danger')">
                                        <i class="ti ti-trash fs-2"></i>
                                    </button>
                                @endif
                            </div>
                            @if(!$item->laboratorium)
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-primary btn-sm px-3 shadow-sm" title="Input Hasil Lab">
                                    <i class="ti ti-flask me-1"></i>Lab
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="p-0">
                        <div class="empty-state">
                            <div class="empty-state-icon">🗺️</div>
                            <h4>Belum Ada Data Pelaksanaan</h4>
                            <p>Data realisasi lapangan akan muncul di sini setelah perencanaan disetujui dan diinput.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </form>
    @if($pelaksanaans->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $pelaksanaans->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    const checkAll = document.getElementById('check-all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const checkItems = document.querySelectorAll('.check-item');
            checkItems.forEach(item => item.checked = checkAll.checked);
            updateBulkDeleteButton();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('check-item')) {
            updateBulkDeleteButton();
        }
    });

    function updateBulkDeleteButton() {
        const btnBulkDelete = document.getElementById('btn-bulk-delete');
        const countSelected = document.getElementById('count-selected');
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        
        if (btnBulkDelete) {
            btnBulkDelete.classList.toggle('d-none', checkedCount === 0);
            if (countSelected) countSelected.textContent = checkedCount;
        }
    }

    function submitBulkDelete() {
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        if (checkedCount === 0) return;

        // Use the global confirmAction modal (no Swal dependency needed)
        const btn = document.getElementById('confirmBtn');
        const methodInput = document.getElementById('confirmMethod');

        document.getElementById('confirmMessage').textContent = `Anda akan menghapus ${checkedCount} data pelaksanaan. Tindakan ini tidak dapat dibatalkan!`;
        document.getElementById('confirmTitle').textContent = 'Hapus Banyak Data?';
        document.getElementById('confirmEmoji').textContent = '🗑️';
        document.getElementById('confirmForm').action = '#';
        methodInput.disabled = true;

        btn.className = 'btn flex-fill btn-danger';
        btn.textContent = 'Ya, Hapus Semua!';
        btn.onclick = function() {
            document.getElementById('form-bulk-delete').submit();
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
        };

        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    }
</script>

<style>
/* ── Premium Table Styling ── */
.card-table thead th {
    background: #f8fafc !important;
    text-transform: uppercase;
    font-size: 0.65rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.05em;
    border-top: none;
    padding: 1rem 0.75rem !important;
    color: #475569 !important;
}
.card-table tbody td {
    padding: 1.25rem 0.75rem !important;
    border-bottom: 1px solid #f1f5f9;
}
.card-table tr:hover { background: #fdfdfd !important; }

/* Sticky Aksi column */
.aksi-sticky-th,
.aksi-sticky-td {
    position: sticky !important;
    right: 0 !important;
    z-index: 10 !important;
    background-color: #ffffff !important;
    box-shadow: -15px 0 15px -10px rgba(0,0,0,0.05) !important;
    white-space: nowrap !important;
    padding-right: 1.5rem !important;
}
.aksi-sticky-th {
    background-color: #f8fafc !important;
    z-index: 11 !important;
}
tbody tr:hover .aksi-sticky-td {
    background-color: #fdfdfd !important;
}

/* Custom badges */
.badge {
    font-weight: 700;
    letter-spacing: 0.02em;
}

/* Button groups */
.btn-group .btn-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-group .btn-icon:hover { background: #f1f5f9 !important; }
</style>
@endpush
@endsection