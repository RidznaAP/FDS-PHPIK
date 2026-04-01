@extends('layouts.app')

@section('title', 'Perencanaan')
@section('page_title', 'Perencanaan Pemantauan')
@section('page_subtitle', 'Daftar rencana pemantauan HPIK per UPT')

@section('page_actions')
    <div class="btn-list">
        <a href="{{ route('perencanaan.export') }}" class="btn btn-outline-success">
            <i class="ti ti-download me-1"></i> Ekspor Excel
        </a>
        @if(Auth::user()->isPusat())
        <a href="{{ route('perencanaan.template') }}" class="btn btn-outline-info">
            <i class="ti ti-file-download me-1"></i> Unduh Template
        </a>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-import">
            <i class="ti ti-upload me-1"></i> Impor Excel
        </button>
        @endif
        @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit() || Auth::user()->isPusat())
            <a href="{{ route('perencanaan.create') }}" class="btn btn-primary d-none d-sm-inline-flex">
                <i class="ti ti-plus me-1"></i> Perencanaan Baru
            </a>
        @endif
    </div>
@endsection

@section('content')
{{-- ═══ TOOLBAR ═══ --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    {{-- Search --}}
    <form method="GET" action="{{ route('perencanaan.index') }}" class="input-icon flex-grow-1" style="max-width:380px;">
        <span class="input-icon-addon"><i class="ti ti-search text-muted"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari provinsi, komoditas, HPIK…" value="{{ request('search') }}">
        @if(request('tahun'))<input type="hidden" name="tahun" value="{{ request('tahun') }}">@endif
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    </form>

    {{-- Filters --}}
    <form id="filter-form" method="GET" action="{{ route('perencanaan.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <select name="tahun" class="form-select form-select-sm" style="width:135px;" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm" style="width:175px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="draft"    {{ request('status') == 'draft'    ? 'selected' : '' }}>Dalam Penyusunan</option>
            <option value="waiting"  {{ request('status') == 'waiting'  ? 'selected' : '' }}>Menunggu Persetujuan</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui &amp; Aktif</option>
        </select>
        @if(request('search') || request('tahun') || request('status'))
            <a href="{{ route('perencanaan.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-x me-1"></i>Reset
            </a>
        @endif
    </form>

    <div class="ms-auto d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="btn btn-sm btn-outline-primary {{ request('view', 'list') !== 'board' ? 'active' : '' }}" title="Tampilan Daftar"><i class="ti ti-list"></i></a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'board']) }}" class="btn btn-sm btn-outline-primary {{ request('view') === 'board' ? 'active' : '' }}" title="Tampilan Board Kanban"><i class="ti ti-layout-kanban"></i></a>
        </div>
        <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-blue text-white rounded-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                <i class="ti ti-clipboard-list" style="font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Daftar Perencanaan HPIK</div>
                <div class="text-muted small">{{ $perencanaans->total() }} data ditemukan</div>
            </div>
        </div>
        <span class="badge bg-blue px-3 py-2" style="font-size:.72rem;color:#fff;">{{ $perencanaans->total() }} total</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('perencanaan.bulk-delete') }}" method="POST">
        @csrf
        @if(request('view') === 'board')
            @php
                $colDraft = []; $colLap = []; $colLab = []; $colEval = [];
                foreach($perencanaans as $p) {
                    if (in_array($p->status, ['draft', 'waiting'])) { $colDraft[] = $p; continue; }
                    if ($p->evaluasi) { $colEval[] = $p; continue; }
                    $hasLab = false; 
                    foreach($p->pelaksanaans as $pel) { if ($pel->laboratorium) $hasLab = true; }
                    if ($hasLab) { $colLab[] = $p; } else { $colLap[] = $p; }
                }
            @endphp
            <div class="kanban-board-container p-4" style="overflow-x: auto; white-space: nowrap; min-height: 60vh; background: var(--body-bg);">
                <!-- 1. Draft & Menunggu -->
                <div class="kanban-col d-inline-flex flex-column align-items-center" style="width: 300px; vertical-align: top; margin-right: 1.5rem; white-space: normal;">
                    <div class="kanban-header badge bg-secondary text-white w-100 py-2 fs-6 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                        <span class="text-uppercase tracking-wider"><i class="ti ti-clipboard me-1"></i> DRAFT / PERSIAPAN</span>
                        <span class="badge bg-white text-dark ms-2" style="font-size:0.75rem;">{{ count($colDraft) }}</span>
                    </div>
                    <div class="w-100" style="min-height: 200px;">
                        @foreach($colDraft as $p) @include('perencanaan.partials.kanban_card', ['p' => $p]) @endforeach
                        @if(count($colDraft) == 0) <div class="text-muted text-center small py-3 border border-dashed rounded bg-transparent">Kosong</div> @endif
                    </div>
                </div>

                <!-- 2. Proses Lapangan -->
                <div class="kanban-col d-inline-flex flex-column align-items-center" style="width: 300px; vertical-align: top; margin-right: 1.5rem; white-space: normal;">
                    <div class="kanban-header badge bg-blue text-white w-100 py-2 fs-6 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                        <span class="text-uppercase tracking-wider"><i class="ti ti-map-pin me-1"></i> PROSES LAPANGAN</span>
                        <span class="badge bg-white text-blue ms-2" style="font-size:0.75rem;">{{ count($colLap) }}</span>
                    </div>
                    <div class="w-100" style="min-height: 200px;">
                        @foreach($colLap as $p) @include('perencanaan.partials.kanban_card', ['p' => $p]) @endforeach
                        @if(count($colLap) == 0) <div class="text-muted text-center small py-3 border border-dashed rounded bg-transparent">Kosong</div> @endif
                    </div>
                </div>

                <!-- 3. Proses Uji Lab -->
                <div class="kanban-col d-inline-flex flex-column align-items-center" style="width: 300px; vertical-align: top; margin-right: 1.5rem; white-space: normal;">
                    <div class="kanban-header badge bg-purple text-white w-100 py-2 fs-6 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                        <span class="text-uppercase tracking-wider"><i class="ti ti-microscope me-1"></i> PROSES LAB</span>
                        <span class="badge bg-white text-purple ms-2" style="font-size:0.75rem;">{{ count($colLab) }}</span>
                    </div>
                    <div class="w-100" style="min-height: 200px;">
                        @foreach($colLab as $p) @include('perencanaan.partials.kanban_card', ['p' => $p]) @endforeach
                        @if(count($colLab) == 0) <div class="text-muted text-center small py-3 border border-dashed rounded bg-transparent">Kosong</div> @endif
                    </div>
                </div>

                <!-- 4. Evaluasi Selesai -->
                <div class="kanban-col d-inline-flex flex-column align-items-center" style="width: 300px; vertical-align: top; white-space: normal;">
                    <div class="kanban-header badge bg-green text-white w-100 py-2 fs-6 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                        <span class="text-uppercase tracking-wider"><i class="ti ti-check me-1"></i> EVALUASI AKHIR</span>
                        <span class="badge bg-white text-green ms-2" style="font-size:0.75rem;">{{ count($colEval) }}</span>
                    </div>
                    <div class="w-100" style="min-height: 200px;">
                        @foreach($colEval as $p) @include('perencanaan.partials.kanban_card', ['p' => $p]) @endforeach
                        @if(count($colEval) == 0) <div class="text-muted text-center small py-3 border border-dashed rounded bg-transparent">Kosong</div> @endif
                    </div>
                </div>
            </div>
        @else
        <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th class="w-1 px-3"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1 sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ (request('sort_by', 'id') === 'id') ? 'sort-active' : '' }}">
                            No
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'id' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'provinsi', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'provinsi' ? 'sort-active' : '' }}">
                            Wilayah / Provinsi
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'provinsi' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_mp', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jenis_mp' ? 'sort-active' : '' }}">
                            Jenis Komoditas & HPIK
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jenis_mp' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'target_uji', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'target_uji' ? 'sort-active' : '' }}">
                            Total Target
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'target_uji' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'status' ? 'sort-active' : '' }}">
                            Status Progress
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'status' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center fw-bold small text-uppercase aksi-sticky-th" style="letter-spacing: 0.1em; color: #64748b; background: #f6f8fb;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perencanaans as $key => $p)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $p->id }}" class="form-check-input check-item"></td>
                    <td class="text-muted">{{ $perencanaans->firstItem() + $key }}</td>
                    <td>
                        <div class="fw-semibold">{{ $p->kab_kota }}</div>
                        <div class="text-muted small">{{ $p->provinsi }}</div>
                    </td>
                    <td>
                        <div>{{ $p->jenis_mp }}</div>
                        <div class="text-muted small">{{ $p->jenis_hpik }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $p->target_uji }}</div>
                        <div class="text-muted small">TW: {{ $p->tw1 }}/{{ $p->tw2 }}/{{ $p->tw3 }}/{{ $p->tw4 }}</div>
                    </td>
                    <td>
                        @php
                            $statusMap = [
                                'draft'    => ['label'=>'Draft',              'class'=>'bg-secondary-lt text-secondary'],
                                'waiting'  => ['label'=>'Menunggu Validasi',  'class'=>'bg-warning-lt text-warning'],
                                'approved' => ['label'=>'Disetujui',          'class'=>'bg-success-lt text-success'],
                            ];
                            $s = $statusMap[$p->status] ?? $statusMap['draft'];
                        @endphp
                        <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                    </td>
                    <td class="aksi-sticky-td">
                        <div class="d-flex gap-1">
                            <a href="{{ route('perencanaan.show', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if(Auth::user()->isPusat())
                                <a href="{{ route('perencanaan.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                    onclick="confirmAction('{{ route('perencanaan.destroy', $p->id) }}', 'Hapus data ini?', 'DELETE', 'btn-danger')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            @endif
                            @if((Auth::user()->isBkhit() || Auth::user()->isBbkhit()) && $p->user_id === Auth::id())
                                @if($p->status === 'draft')
                                    <a href="{{ route('perencanaan.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="ti ti-pencil"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="confirmAction('{{ route('perencanaan.destroy', $p->id) }}', 'Hapus data?', 'DELETE', 'btn-danger')"><i class="ti ti-trash"></i></button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('{{ route('perencanaan.submit', $p->id) }}', 'Ajukan validasi?', 'POST', 'btn-warning')"><i class="ti ti-send me-1"></i>Ajukan</button>
                                @endif
                                @if($p->status === 'approved')
                                    <a href="{{ route('pelaksanaan.create', $p->id) }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-1"></i>Input Lapangan</a>
                                @endif
                            @endif
                            @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                                @if($p->status === 'waiting')
                                    <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('{{ route('perencanaan.approve', $p->id) }}', 'Setujui perencanaan ini?', 'POST', 'btn-success')"><i class="ti ti-check me-1"></i>Setujui</button>
                                @elseif($p->evaluasi)
                                    <span class="badge bg-green-lt text-green fw-bold"><i class="ti ti-circle-check me-1"></i>Selesai</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-0">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <h4>Belum Ada Data Perencanaan</h4>
                            <p>Belum ada rencana pemantauan HPIK yang tersimpan sesuai filter yang dipilih.</p>
                            @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit())
                                <a href="{{ route('perencanaan.create') }}" class="btn btn-primary btn-pill px-4">
                                    <i class="ti ti-plus me-2"></i>Buat Perencanaan Baru
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
    </form>
    @if($perencanaans->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $perencanaans->links() }}
    </div>
    @endif
</div>

@if(Auth::user()->isBkhit() || Auth::user()->isBbkhit() || Auth::user()->isPusat())
<div class="modal modal-blur fade" id="modal-import" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Impor Data Perencanaan dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('perencanaan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-required">File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <div class="form-hint small mt-2">Gunakan tombol <strong>"Unduh Template"</strong> untuk mendapatkan format yang benar.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-upload me-1"></i> Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
/* Sticky Aksi column */
.aksi-sticky-th,
.aksi-sticky-td {
    position: sticky;
    right: 0;
    z-index: 2;
    background: #ffffff;
    box-shadow: -3px 0 8px -2px rgba(0,0,0,0.08);
}
.aksi-sticky-th {
    background: #f6f8fb !important;
    z-index: 3;
}
tbody tr:hover .aksi-sticky-td {
    background: #f8fafc;
}
</style>
@endpush

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

        document.getElementById('confirmMessage').textContent = `Anda akan menghapus ${checkedCount} data perencanaan. Tindakan ini tidak dapat dibatalkan!`;
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

        var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }
</script>
@endpush
@endsection
