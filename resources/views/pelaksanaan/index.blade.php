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
                            Wilayah / Lokasi Sampling
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'lokasi_pengambilan_sampel' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_ikan', 'sort_order' => (request('sort_by') === 'jenis_ikan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jenis_ikan' ? 'sort-active' : '' }}">
                            Komoditas Ikan
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jenis_ikan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jumlah_sampel', 'sort_order' => (request('sort_by') === 'jumlah_sampel' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'jumlah_sampel' ? 'sort-active' : '' }}">
                            Data Sampel
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'jumlah_sampel' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_pemantauan', 'sort_order' => (request('sort_by') === 'tanggal_pemantauan' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by', 'tanggal_pemantauan') === 'tanggal_pemantauan' ? 'sort-active' : '' }}">
                            Tgl Pelaksanaan
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'tanggal_pemantauan' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="sort-th">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status_lab', 'sort_order' => (request('sort_by') === 'status_lab' && request('sort_order') === 'asc') ? 'desc' : 'asc']) }}" class="sort-btn {{ request('sort_by') === 'status_lab' ? 'sort-active' : '' }}">
                            Status Pengujian
                            <span class="sort-icon">
                                <i class="ti {{ request('sort_by') === 'status_lab' ? (request('sort_order') === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }}"></i>
                            </span>
                        </a>
                    </th>
                    <th class="w-1 text-center bg-light fw-bold small text-uppercase" style="letter-spacing: 0.1em; color: #64748b;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelaksanaans as $key => $item)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input check-item"></td>
                    <td class="text-muted">{{ $pelaksanaans->firstItem() + $key }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->perencanaan->jenis_mp ?? '-' }}</div>
                        <div class="text-muted small">{{ $item->perencanaan->kab_kota ?? '-' }}, {{ $item->perencanaan->provinsi ?? '-' }}</div>
                        <div class="small">{{ $item->lokasi_pengambilan_sampel }}</div>
                        @if($item->latitude && $item->longitude)
                            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-decoration-none">
                                <span class="badge bg-azure-lt"><i class="ti ti-map-pin me-1"></i>Map</span>
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($item->jenis_ikan)
                            <div class="fw-semibold">{{ $item->jenis_ikan }}</div>
                            @if($item->nama_latin)<div class="text-muted small fst-italic">{{ $item->nama_latin }}</div>@endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->jumlah_sampel }} pelaksanaan</div>
                        <div class="text-muted small">{{ $item->metode_pengambilan_sampel }}</div>
                        @if($item->jumlah_kematian > 0)
                            <span class="badge bg-danger-lt text-danger">Mati: {{ $item->jumlah_kematian }}</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ \Carbon\Carbon::parse($item->tanggal_pemantauan)->format('d/m/Y') }}</div>
                        <div class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        @if($item->laboratorium)
                            <span class="badge bg-success-lt text-success">{{ $item->laboratorium->hasil_uji }}</span>
                        @else
                            <span class="badge bg-warning-lt text-warning">Belum Diuji</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('pelaksanaan.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="ti ti-eye"></i></a>
                            @if(Auth::user()->isPusat())
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                    onclick="confirmAction('{{ route('pelaksanaan.destroy', $item->id) }}', 'Hapus data ini?', 'DELETE', 'btn-danger')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            @endif
                            @if($item->laboratorium)
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Lab"><i class="ti ti-flask"></i></a>
                            @else
                                <a href="{{ route('laboratorium.create', $item->id) }}" class="btn btn-sm btn-primary"><i class="ti ti-flask me-1"></i>Input Lab</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-0">
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
@endpush
@endsection