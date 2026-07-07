@extends('layouts.app')

@section('title', 'Master Data — Jenis Penyakit HPIK')
@section('page_title', 'Master Data: Jenis Penyakit (HPIK)')
@section('page_subtitle', 'Kelola daftar jenis penyakit / HPIK untuk digunakan di form Perencanaan')

@section('page_actions')
<div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-success" data-bs-toggle="collapse" data-bs-target="#collapse-import">
        <i class="ti ti-upload me-1"></i>Import Excel
    </button>
    <a href="{{ route('master.jenis-penyakit.export') }}" class="btn btn-outline-info">
        <i class="ti ti-download me-1"></i>Export Excel
    </a>
    <a href="{{ route('master.jenis-penyakit.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Tambah Jenis Penyakit
    </a>
</div>
@endsection

@section('content')
<div class="row g-2 mb-3">
    <div class="col">
        <form action="{{ route('master.jenis-penyakit.index') }}" method="GET" class="input-icon">
            <span class="input-icon-addon">
                <i class="ti ti-search"></i>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari Jenis Penyakit atau Organisme Penyebab…">
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
    <div class="card border-warning border-top-wide">
        <div class="card-header bg-warning-lt">
            <h5 class="card-title text-warning"><i class="ti ti-upload me-2"></i>Import Jenis Penyakit HPIK</h5>
            <div class="card-actions">
                <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#collapse-import"></button>
            </div>
        </div>
        <form action="{{ route('master.jenis-penyakit.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">1. Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" class="form-control" required accept=".xlsx, .xls" autocomplete="off">
                </div>
                <div class="bg-blue-lt p-3 rounded-2">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="ti ti-info-circle text-blue fs-3"></i>
                        <span class="fw-bold text-blue">Petunjuk:</span>
                    </div>
                    <ul class="mb-0 small ps-3">
                        <li>Pastikan kolom Nama Penyakit dan Organisme Penyebab terisi.</li>
                        <li>Gunakan template untuk format yang akurat.</li>
                    </ul>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-end gap-2">
                <a href="{{ route('master.jenis-penyakit.template') }}" class="btn btn-link link-secondary me-auto">
                    <i class="ti ti-file-download me-1"></i>Unduh Template
                </a>
                <button type="button" class="btn btn-link link-secondary" data-bs-toggle="collapse" data-bs-target="#collapse-import">Batal</button>
                <button type="submit" class="btn btn-warning px-4 text-dark">Proses Import</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title">Daftar Jenis Penyakit / HPIK</h3>
        <span class="badge bg-red-lt">{{ $items->total() }} data</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('master.jenis-penyakit.bulk-delete') }}" method="POST">
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
                            Nama Penyakit / HPIK
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'nama' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'organisme_penyebab', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort') === 'organisme_penyebab' ? 'sort-active' : '' }}">
                            Organisme Penyebab
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'organisme_penyebab' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'golongan', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort') === 'golongan' ? 'sort-active' : '' }}">
                            Kelompok Patogen
                            <span class="sort-icon">
                                <i class="ti {{ request('sort') === 'golongan' ? (request('direction') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
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
                    <td>
                        @if($item->organisme_penyebab)
                            <span class="text-primary fst-italic">{{ $item->organisme_penyebab }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $warna = match($item->golongan) {
                                'Virus'   => 'bg-red-lt text-danger',
                                'Bakteri' => 'bg-orange-lt text-orange',
                                'Parasit' => 'bg-yellow-lt text-yellow',
                                'Jamur'   => 'bg-green-lt text-green',
                                default   => 'bg-secondary-lt text-muted',
                            };
                        @endphp
                        @if($item->golongan)
                            <span class="badge {{ $warna }}">{{ $item->golongan }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>
                        @if($item->aktif)
                            <span class="badge bg-success-lt text-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary-lt text-muted">Non-aktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('master.jenis-penyakit.edit', $item) }}"
                               class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="ti ti-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                onclick="confirmAction(
                                    '{{ route('master.jenis-penyakit.destroy', $item) }}',
                                    '&quot;{{ $item->nama }}&quot; akan dihapus dari master data.',
                                    'DELETE', 'btn-danger', '🗑️', 'Hapus Jenis Penyakit'
                                )">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                        <i class="ti ti-virus-off" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        Belum ada data jenis penyakit. Klik <strong>Tambah Jenis Penyakit</strong> untuk mulai.
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
