@extends('layouts.app')

@section('title', 'Perencanaan')
@section('page_title', 'Modul Perencanaan')
@section('page_subtitle', 'Daftar rencana pemantauan HPIK')

@section('page_actions')
    <div class="btn-list">
        <a href="{{ route('perencanaan.export') }}" class="btn btn-outline-success">
            <i class="ti ti-download me-1"></i> Ekspor Excel
        </a>
        <a href="{{ route('perencanaan.template') }}" class="btn btn-outline-info">
            <i class="ti ti-file-download me-1"></i> Unduh Template
        </a>
        @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit() || Auth::user()->isPusat())
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-import">
                <i class="ti ti-upload me-1"></i> Impor Excel
            </button>
            <a href="{{ route('perencanaan.create') }}" class="btn btn-primary d-none d-sm-inline-flex">
                <i class="ti ti-plus me-1"></i> Perencanaan Baru
            </a>
        @endif
    </div>
@endsection

@section('content')
<div class="row g-2 mb-3">
    <div class="col">
        <form method="GET" action="{{ route('perencanaan.index') }}" class="input-icon" style="max-width:400px;">
            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari provinsi, kota, jenis MP…" value="{{ request('search') }}">
        </form>
    </div>
    <div class="col-auto d-flex gap-2">
        <select name="tahun" form="filter-form" class="form-select" style="width:auto;" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Tahun</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <select name="status" form="filter-form" class="form-select" style="width:auto;" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
        </select>
        <form id="filter-form" method="GET" action="{{ route('perencanaan.index') }}" class="d-none">
            <input type="hidden" name="search" value="{{ request('search') }}">
        </form>
        <button type="button" id="btn-bulk-delete" class="btn btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus Terpilih (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title"><i class="ti ti-clipboard-list me-2"></i>Daftar Perencanaan</h3>
        <span class="badge bg-blue-lt">{{ $perencanaans->total() }} data</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('perencanaan.bulk-delete') }}" method="POST">
        @csrf
        <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th class="w-1"><input type="checkbox" class="form-check-input" id="check-all"></th>
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
                            Wilayah
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'provinsi' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_mp', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jenis_mp' ? 'sort-active' : '' }}">
                            Jenis MP / HPIK
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jenis_mp' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'target_uji', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'target_uji' ? 'sort-active' : '' }}">
                            Target Uji
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'target_uji' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'status' ? 'sort-active' : '' }}">
                            Status
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'status' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center">Aksi</th>
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
                    <td>
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
                                    <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('{{ route('perencanaan.approve', $p->id) }}', 'Setujui?', 'POST', 'btn-success')"><i class="ti ti-check me-1"></i>Setujui</button>
                                @elseif($p->status === 'approved' && !$p->evaluasi)
                                    <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-orange"><i class="ti ti-chart-bar me-1"></i>Evaluasi</a>
                                @elseif($p->evaluasi)
                                    <span class="badge bg-green-lt">✅ Selesai</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="ti ti-clipboard-list" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        Belum ada data perencanaan.
                        @if(Auth::user()->isBkhit() || Auth::user()->isBbkhit())
                            <br><a href="{{ route('perencanaan.create') }}" class="btn btn-primary btn-sm mt-2"><i class="ti ti-plus me-1"></i>Buat Perencanaan</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
        Swal.fire({
            title: 'Hapus Banyak Data?',
            text: `Anda akan menghapus ${checkedCount} data perencanaan. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-bulk-delete').submit();
        });
    }
</script>
@endpush
@endsection
