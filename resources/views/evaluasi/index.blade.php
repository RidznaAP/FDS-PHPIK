@extends('layouts.app')

@section('title', 'Evaluasi')
@section('page_title', 'Modul Evaluasi')
@section('page_subtitle', 'Penetapan status akhir hasil pemantauan HPIK')

@section('content')
<div class="row g-2 mb-3">
    <div class="col">
        {{-- placeholder --}}
    </div>
    <div class="col-auto">
        <button type="button" id="btn-bulk-delete" class="btn btn-danger d-none" onclick="submitBulkDelete()">
            <i class="ti ti-trash me-1"></i>Hapus Terpilih (<span id="count-selected">0</span>)
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Data Evaluasi HPIK</h3>
        <span class="badge bg-blue-lt">{{ $perencanaans->total() }} data</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('evaluasi.bulk-delete') }}" method="POST">
        @csrf
        <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th class="w-1"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => (request('sort_by') === 'id' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="btn-sort {{ request('sort_by', 'id') === 'id' ? 'active' : '' }}">
                            ID
                            <span class="sort-indicator">
                                <i class="ti {{ request('sort_by') === 'id' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'provinsi', 'sort_order' => (request('sort_by') === 'provinsi' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="btn-sort {{ request('sort_by') === 'provinsi' ? 'active' : '' }}">
                            Wilayah / Komoditas
                            <span class="sort-indicator">
                                <i class="ti {{ request('sort_by') === 'provinsi' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'lab_selesai', 'sort_order' => (request('sort_by') === 'lab_selesai' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="btn-sort {{ request('sort_by') === 'lab_selesai' ? 'active' : '' }}">
                            Lab Selesai
                            <span class="sort-indicator">
                                <i class="ti {{ request('sort_by') === 'lab_selesai' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status_evaluasi', 'sort_order' => (request('sort_by') === 'status_evaluasi' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="btn-sort {{ request('sort_by') === 'status_evaluasi' ? 'active' : '' }}">
                            Hasil Evaluasi
                            <span class="sort-indicator">
                                <i class="ti {{ request('sort_by') === 'status_evaluasi' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perencanaans as $key => $p)
                <tr>
                    <td>
                        @if($p->evaluasi)
                            <input type="checkbox" name="ids[]" value="{{ $p->evaluasi->id }}" class="form-check-input check-item">
                        @endif
                    </td>
                    <td class="text-muted">{{ $perencanaans->firstItem() + $key }}</td>
                    <td>
                        <div class="fw-semibold">{{ $p->kab_kota }}, {{ $p->provinsi }}</div>
                        <div class="text-muted small">{{ $p->jenis_mp }} — {{ $p->jenis_hpik }}</div>
                    </td>
                    <td>
                        @php
                            $selesai = $p->pelaksanaans->filter(fn($pl) => $pl->laboratorium !== null)->count();
                            $total = $p->pelaksanaans->count();
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-fill" style="max-width: 80px;">
                                <div class="progress progress-sm">
                                    <div class="progress-bar {{ $selesai == $total && $total > 0 ? 'bg-success' : 'bg-yellow' }}"
                                         style="width: {{ $total > 0 ? ($selesai/$total*100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted small">{{ $selesai }}/{{ $total }}</div>
                        </div>
                    </td>
                    <td>
                        @if($p->evaluasi)
                            @php $w = $p->evaluasi->warna; @endphp
                            <span class="badge bg-{{ $w }}-lt text-{{ $w }}">{{ $p->evaluasi->kesimpulan }}</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">Belum Dievaluasi</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($p->evaluasi)
                                <a href="{{ route('evaluasi.show', $p->evaluasi->id) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="ti ti-chart-bar"></i></a>
                                @if(Auth::user()->isPusat())
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmAction('{{ route('evaluasi.destroy', $p->evaluasi->id) }}', 'Hapus hasil evaluasi?', 'DELETE', 'btn-danger')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('perencanaan.show', $p->id) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="ti ti-eye"></i></a>
                            @endif
                            @if(!$p->evaluasi && $p->status === 'approved' && (Auth::user()->isBbkhit() || Auth::user()->isPusat()))
                                <a href="{{ route('evaluasi.create', $p->id) }}" class="btn btn-sm btn-warning"><i class="ti ti-plus me-1"></i>Evaluasi</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="ti ti-chart-bar" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        Belum ada data untuk dievaluasi.
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
            title: 'Hapus Banyak Hasil Evaluasi?',
            text: `Anda akan menghapus ${checkedCount} hasil evaluasi. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-bulk-delete').submit();
        });
    }
</script>
@endpush
@endsection
