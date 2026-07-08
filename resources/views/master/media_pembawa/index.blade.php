@extends('layouts.app')

@section('title', 'Master Data — Media Pembawa')
@section('page_title', 'Master Data: Media Pembawa')
@section('page_subtitle', 'Kelola daftar media pembawa / jenis ikan untuk digunakan di form Perencanaan')

@section('page_actions')
<div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-success" data-bs-toggle="collapse" data-bs-target="#collapse-import">
        <i class="ti ti-upload me-1"></i>Import Excel
    </button>
    <a href="{{ route('master.media-pembawa.export') }}" class="btn btn-outline-info" data-turbo="false">
        <i class="ti ti-download me-1"></i>Export Excel
    </a>
    <a href="{{ route('master.media-pembawa.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Tambah Media Pembawa
    </a>
</div>
@endsection

@section('content')
<div class="row g-2 mb-3">
    <div class="col">
        <form action="{{ route('master.media-pembawa.index') }}" method="GET" class="input-icon">
            <span class="input-icon-addon">
                <i class="ti ti-search"></i>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari Nama Umum…">
        </form>
    </div>
    <div class="col-auto">
        <button type="button" id="btn-bulk-delete" class="btn btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus Terpilih (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

{{-- ═══ INLINE IMPORT SECTION ═══ --}}
<div class="collapse mb-3" id="collapse-import">
    <div class="card border-success border-top-wide">
        <div class="card-header bg-success-lt">
            <h5 class="card-title text-success"><i class="ti ti-upload me-2"></i>Import Media Pembawa</h5>
            <div class="card-actions">
                <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#collapse-import"></button>
            </div>
        </div>
        <form action="{{ route('master.media-pembawa.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">1. Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" class="form-control" required accept=".xlsx, .xls, .csv" autocomplete="off">
                </div>
                <div class="bg-blue-lt p-3 rounded-2">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="ti ti-info-circle text-blue fs-3"></i>
                        <span class="fw-bold text-blue">Petunjuk:</span>
                    </div>
                    <ul class="mb-0 small ps-3">
                        <li>Kolom: **Nama Umum**, **Nama Inggris**, **Nama Ilmiah**.</li>
                        <li>Gunakan template untuk hasil terbaik.</li>
                    </ul>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-end gap-2">
                <a href="{{ route('master.media-pembawa.template') }}" class="btn btn-link link-secondary me-auto">
                    <i class="ti ti-file-download me-1"></i>Unduh Template
                </a>
                <button type="button" class="btn btn-link link-secondary" data-bs-toggle="collapse" data-bs-target="#collapse-import">Batal</button>
                <button type="submit" class="btn btn-success px-4">Proses Import</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title">Daftar Media Pembawa</h3>
        <span class="badge bg-blue-lt">{{ $items->total() }} data</span>
    </div>
    
    <form id="form-bulk-delete" action="{{ route('master.media-pembawa.bulk-delete') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                <tr>
                    <th class="w-1 px-3"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1 sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort', 'id') === 'id' ? 'sort-active' : '' }}">
                            No
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'id' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort', 'nama') === 'nama' ? 'sort-active' : '' }}">
                            Nama Umum
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'nama' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_inggris', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort', 'nama_inggris') === 'nama_inggris' ? 'sort-active' : '' }}">
                            Nama Inggris
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'nama_inggris' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'keterangan', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort', 'keterangan') === 'keterangan' ? 'sort-active' : '' }}">
                            Nama Ilmiah
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'keterangan' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'aktif', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort', 'aktif') === 'aktif' ? 'sort-active' : '' }}">
                            Status
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'aktif' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center bg-light fw-bold small text-uppercase" style="letter-spacing: 0.1em; color: #64748b;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input check-item"></td>
                            <td class="text-muted">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td class="text-muted">{{ $item->nama_inggris ?? '-' }}</td>
                            <td class="text-muted small italic">{{ $item->keterangan ?? '-' }}</td>
                            <td>
                                @if($item->aktif)
                                    <span class="badge bg-success-lt text-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-lt text-muted">Non-aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('master.media-pembawa.edit', $item) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmAction(
                                            '{{ route('master.media-pembawa.destroy', $item) }}',
                                            '&quot;{{ $item->nama }}&quot; akan dihapus dari master data.',
                                            'DELETE', 'btn-danger', '🗑️', 'Hapus Media Pembawa'
                                        )">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ti ti-database-off" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                                Belum ada data media pembawa. Klik <strong>Tambah Media Pembawa</strong> untuk mulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    @if($items->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    const checkAll = document.getElementById('check-all');
    const checkItems = document.querySelectorAll('.check-item');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const countSelected = document.getElementById('count-selected');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(item => {
                item.checked = this.checked;
            });
            updateBulkDeleteButton();
        });

        checkItems.forEach(item => {
            item.addEventListener('change', updateBulkDeleteButton);
        });
    }

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('d-none');
            countSelected.textContent = checkedCount;
        } else {
            btnBulkDelete.classList.add('d-none');
        }
    }

    function submitBulkDelete() {
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        confirmAction(
            null, 
            checkedCount + ' data yang dipilih akan dihapus permanen. Lanjutkan?', 
            'POST', 
            'btn-danger', 
            '⚠️', 
            'Hapus Banyak Data'
        );
        
        // Override the confirm button click
        document.getElementById('confirmBtn').onclick = function() {
            document.getElementById('form-bulk-delete').submit();
        };
    }

    // Auto-submit search on typing (debounced)
    let searchTimer;
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                this.form.submit();
            }, 700);
        });
        
        // Move cursor to end of text on focus
        searchInput.addEventListener('focus', function() {
            const val = this.value;
            this.value = '';
            this.value = val;
        });
        
        // Auto-focus if there is a search query
        if (searchInput.value) {
            searchInput.focus();
        }
    }
</script>
@endsection
