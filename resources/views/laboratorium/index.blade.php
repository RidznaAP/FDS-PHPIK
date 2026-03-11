@extends('layouts.app')

@section('title', 'Laboratorium')
@section('page_title', 'Modul Laboratorium')
@section('page_subtitle', 'Daftar sampel dan status pengujian laboratorium')

@section('content')
<div class="row g-2 mb-3">
    <div class="col">
        <form method="GET" action="{{ route('laboratorium.index') }}" class="input-icon" style="max-width:400px;">
            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari lokasi sampling, komoditas…" value="{{ request('search') }}">
        </form>
    </div>
    <div class="col-auto d-flex gap-2">
        <select name="tahun" form="filter-form-lab" class="form-select" style="width:auto;" onchange="document.getElementById('filter-form-lab').submit()">
            <option value="">Semua Tahun</option>
            @foreach($years ?? [] as $y)
                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <form id="filter-form-lab" method="GET" action="{{ route('laboratorium.index') }}" class="d-none">
            <input type="hidden" name="search" value="{{ request('search') }}">
        </form>
        <button type="button" id="btn-bulk-delete" class="btn btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus Terpilih (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title"><i class="ti ti-flask me-2"></i>Data Sampel Laboratorium</h3>
        <span class="badge bg-blue-lt">{{ $pelaksanaans->total() }} data</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('laboratorium.bulk-delete') }}" method="POST">
        @csrf
        <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th class="w-1 px-3"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1 sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => (request('sort_by') === 'id' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by', 'id') === 'id' ? 'sort-active' : '' }}">
                            No
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'id' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'lokasi_pengambilan_sampel', 'sort_order' => (request('sort_by') === 'lokasi_pengambilan_sampel' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'lokasi_pengambilan_sampel' ? 'sort-active' : '' }}">
                            Wilayah Sampling
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'lokasi_pengambilan_sampel' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_ikan', 'sort_order' => (request('sort_by') === 'jenis_ikan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jenis_ikan' ? 'sort-active' : '' }}">
                            Ikan / MP
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jenis_ikan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jumlah_sampel', 'sort_order' => (request('sort_by') === 'jumlah_sampel' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jumlah_sampel' ? 'sort-active' : '' }}">
                            Jml
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jumlah_sampel' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_pemantauan', 'sort_order' => (request('sort_by') === 'tanggal_pemantauan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by', 'tanggal_pemantauan') === 'tanggal_pemantauan' ? 'sort-active' : '' }}">
                            Tgl
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'tanggal_pemantauan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'hasil_parasit', 'sort_order' => (request('sort_by') === 'hasil_parasit' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'hasil_parasit' ? 'sort-active' : '' }}">
                            Par
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'hasil_parasit' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'hasil_bakteri', 'sort_order' => (request('sort_by') === 'hasil_bakteri' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'hasil_bakteri' ? 'sort-active' : '' }}">
                            Bak
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'hasil_bakteri' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'hasil_virus', 'sort_order' => (request('sort_by') === 'hasil_virus' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'hasil_virus' ? 'sort-active' : '' }}">
                            Vir
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'hasil_virus' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'hasil_jamur', 'sort_order' => (request('sort_by') === 'hasil_jamur' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'hasil_jamur' ? 'sort-active' : '' }}">
                            Jam
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'hasil_jamur' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'prevalensi', 'sort_order' => (request('sort_by') === 'prevalensi' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'prevalensi' ? 'sort-active' : '' }}">
                            %
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'prevalensi' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status_lab', 'sort_order' => (request('sort_by') === 'status_lab' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'status_lab' ? 'sort-active' : '' }}">
                            Lab
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'status_lab' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center bg-light fw-bold small text-uppercase" style="letter-spacing: 0.1em; color: #64748b;">Aksi</th>
                </tr>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td>
                        @if($item->laboratorium)
                            <input type="checkbox" name="ids[]" value="{{ $item->laboratorium->id }}" class="form-check-input check-item">
                        @endif
                    </td>
                    <td class="text-muted">{{ $pelaksanaans->firstItem() + $key }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->lokasi_pengambilan_sampel }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                    </td>
                    <td>{{ $item->perencanaan->jenis_mp ?? '-' }}</td>
                    <td>{{ $item->jumlah_sampel }} pelaksanaan</td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
                    @php
                        $patogenBadge = [
                            '+' => 'bg-danger-lt text-danger',
                            '-' => 'bg-success-lt text-success',
                            'NT' => 'bg-secondary-lt text-secondary',
                        ];
                    @endphp
                    @foreach(['hasil_parasit','hasil_bakteri','hasil_virus','hasil_jamur'] as $f)
                    <td>
                        @if($item->laboratorium)
                            @php $val = $item->laboratorium->$f ?? 'NT'; @endphp
                            <span class="badge {{ $patogenBadge[$val] ?? 'bg-secondary-lt' }}">{{ $val }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-muted small">
                        @if($item->laboratorium && $item->laboratorium->prevalensi !== null)
                            {{ $item->laboratorium->prevalensi }}%
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($item->laboratorium)
                            <span class="badge bg-success-lt text-success"><i class="ti ti-check me-1"></i>{{ $item->laboratorium->hasil_uji }}</span>
                        @else
                            <span class="badge bg-warning-lt text-warning"><i class="ti ti-clock me-1"></i>Menunggu</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($item->laboratorium)
                                <a href="{{ route('laboratorium.show', $item->laboratorium->id) }}" class="btn btn-sm btn-outline-primary" title="Detail Lab"><i class="ti ti-eye"></i></a>
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="ti ti-pencil"></i></a>
                                @if(Auth::user()->isPusat())
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmAction('{{ route('laboratorium.destroy', $item->laboratorium->id) }}', 'Hapus hasil lab ini?', 'DELETE', 'btn-danger')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-primary"><i class="ti ti-flask me-1"></i>Input</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center py-4 text-muted">
                        <i class="ti ti-flask" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        Belum ada data sampel masuk.
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
        Swal.fire({
            title: 'Hapus Banyak Hasil Lab?',
            text: `Anda akan menghapus ${checkedCount} hasil laboratorium. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-bulk-delete').submit();
        });
    }
</script>
@endpush
@endsection
