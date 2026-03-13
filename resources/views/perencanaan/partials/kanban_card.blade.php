<div class="card mb-3 shadow-sm border-0 position-relative kanban-card animation-fade-in transition-all" style="cursor: pointer;" onclick="location.href='{{ route('perencanaan.show', $p->id) }}'">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-blue-lt text-uppercase fst-italic" style="font-size: 0.6rem;">{{ Str::limit($p->jenis_mp, 15) }}</span>
            @if($p->status == 'draft') <span class="badge bg-secondary-lt">Draft</span>
            @elseif($p->status == 'waiting') <span class="badge bg-warning-lt">Waiting</span>
            @elseif($p->status == 'approved') <span class="badge bg-success-lt">Approved</span>
            @endif
        </div>
        <h5 class="mb-1 text-truncate" title="{{ $p->provinsi }} - {{ $p->kab_kota }}">
            {{ $p->provinsi }}
        </h5>
        <div class="text-muted small mb-2 text-truncate" title="{{ $p->kab_kota }}">
            <i class="ti ti-map-pin me-1"></i> {{ $p->kab_kota }}
        </div>
        <div class="text-muted small mb-2 text-truncate" title="{{ $p->jenis_hpik }}">
            <i class="ti ti-virus text-danger me-1"></i> {{ $p->jenis_hpik }}
        </div>
        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
            <div class="text-secondary small fw-bold"><i class="ti ti-box"></i> {{ $p->target_uji }} Uji</div>
            <div class="avatar avatar-sm shadow-none" style="background:#e2e8f0; color:#475569; font-size:10px;" title="{{ optional($p->user)->name }}">
                {{ strtoupper(substr(optional($p->user)->name, 0, 2)) }}
            </div>
        </div>
    </div>
</div>
