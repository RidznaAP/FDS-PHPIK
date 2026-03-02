@extends('layouts.app')

@section('title', 'Laboratorium')
@section('page_title', 'Modul Laboratorium')
@section('page_subtitle', 'Daftar sampel dan status pengujian laboratorium')

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
        <h3 class="card-title"><i class="ti ti-flask me-2"></i>Data Sampel Laboratorium</h3>
        <span class="badge bg-blue-lt">{{ $pelaksanaans->total() }} data</span>
    </div>
    <form id="form-bulk-delete" action="{{ route('laboratorium.bulk-delete') }}" method="POST">
        @csrf
        <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th class="w-1"><input type="checkbox" class="form-check-input" id="check-all"></th>
                    <th class="w-1">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="text-inherit">
                            No <i class="ti ti-selector ms-1"></i>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'lokasi_pengambilan_sampel', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="text-inherit">
                            Lokasi Sampling <i class="ti ti-selector ms-1"></i>
                        </a>
                    </th>
                    <th>Komoditas</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jumlah_sampel', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="text-inherit">
                            Jml Sampel <i class="ti ti-selector ms-1"></i>
                        </a>
                    </th>
                    <th>Tanggal</th>
                    <th>Parasit</th>
                    <th>Bakteri</th>
                    <th>Virus</th>
                    <th>Jamur</th>
                    <th>Prev%</th>
                    <th>Status Lab</th>
                    <th class="w-1 text-center">Aksi</th>
                </tr>
            </thead>
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
                    <td>{{ $item->jumlah_sampel }} ekor</td>
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
