@extends('layouts.andon')

@section('page-title', 'REQUEST REPAIR MONITORING')

@section('content')
<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    min-width: 120px;
    height: 38px;
    line-height: 1;
    white-space: nowrap;
    text-align: center;
    color: white;
}

.status-open {
    background: #16a34a;
}

.status-on_process {
    background: #2563eb;
}

.status-on_trial {
    background: #ea8202;
}

/* Row highlight untuk on_process & on_trial (default, dioverride oleh stock level kalau ada) */
tr[data-status="on_process"] {
    background: rgba(59, 130, 246, 0.05);
}

tr[data-status="on_trial"] {
    background: rgba(234, 179, 8, 0.05);
}

/* Row highlight berdasarkan sisa Stock FG (dihitung dari tanggal_pengajuan) */
tr[data-stock-level="critical"] {
    background: rgba(220, 38, 38, 0.25) !important;
}

tr[data-stock-level="warning"] {
    background: rgba(234, 179, 8, 0.20) !important;
}

/* Kategori — teks bold aja, tanpa badge */
.kategori-text {
    font-weight: 700;
}

/* Durasi live */
.durasi-live,
.durasi-trial-live {
    font-weight: 700;
    color: #ffffff;
}

/* Summary cards */
.summary-cards {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 12px;
}

.summary-card {
    border-radius: 0.6rem;
    padding: 0.4rem 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
}

.summary-card .summary-icon {
    width: 26px;
    height: 26px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-card .summary-icon svg {
    width: 15px;
    height: 15px;
}

.summary-card .summary-text {
    line-height: 1.1;
}

.summary-card .summary-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    opacity: 0.9;
}

.summary-card .summary-value {
    font-size: 1.15rem;
    font-weight: 800;
}

.summary-open {
    background: #16a34a;
}

.summary-on_process {
    background: #2563eb;
}

.summary-on_trial {
    background: #ea8202;
}
</style>

@php
    $openCount = $requestRepairs->where('status', 'open')->count();
    $onProcessCount = $requestRepairs->where('status', 'on_process')->count();
    $onTrialCount = $requestRepairs->where('status', 'on_trial')->count();
@endphp

<div class="summary-cards mt-2 mb-2 mr-2">
    <div class="summary-card summary-open">
        <div class="summary-icon">
            <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="summary-text ">
            <div class="summary-label">Open</div>
            <div class="summary-value">{{ $openCount }}</div>
        </div>
    </div>
    <div class="summary-card summary-on_process">
        <div class="summary-icon">
            <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        </div>
        <div class="summary-text">
            <div class="summary-label">On Process</div>
            <div class="summary-value">{{ $onProcessCount }}</div>
        </div>
    </div>
    <div class="summary-card summary-on_trial">
        <div class="summary-icon">
            <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="summary-text">
            <div class="summary-label">On Trial</div>
            <div class="summary-value">{{ $onTrialCount }}</div>
        </div>
    </div>
</div>

<div style="overflow-x: auto; margin-top:10px;">
    <table class="table-bordered theme-table table-dark-custom" style="width: 100%">
        <thead>
            <tr class="text-center" style="font-size: 1.15rem;">
                <th style="width: 140px; padding: 10px 8px;" rowspan="2">No. Request</th>
                <th style="width: 150px; padding: 10px 8px;" rowspan="2">Tgl Pengajuan</th>
                <th style="width: 100px; padding: 10px 8px;" rowspan="2">Line</th>
                <th style="width: 130px; padding: 10px 8px;" rowspan="2">Part No</th>
                <th style="width: 130px; padding: 10px 8px;" rowspan="2">No Proses</th>
                <th style="width: 100px; padding: 10px 8px;" rowspan="2">Kategori</th>
                <th style="width: 90px; padding: 10px 8px;" rowspan="2">Shift</th>
                <th style="width: 90px; padding: 10px 8px;" rowspan="2">Stock FG</th>
                <th style="padding: 1px 1px; font-size: 0.9rem;" colspan="2">Durasi</th>
                <th style="width: 130px; padding: 10px 8px;" rowspan="2">PIC</th>
                <th style="width: 140px; padding: 10px 8px;" rowspan="2">Status</th>
            </tr>
            <tr class="text-center">
                <th style="width: 90px; padding: 1px 1px; font-size: 1rem;">Repair</th>
                <th style="width: 90px; padding: 1px 1px; font-size: 1rem;">On Trial</th>
            </tr>
        </thead>
        <tbody class="table-dark-custom text-center" id="tableBody" style="font-size: 1.15rem;">
            @forelse($requestRepairs as $rr)
                @php
                    // Sisa stock FG dihitung mundur dari tanggal_pengajuan
                    $stockLevel = null;
                    if ($rr->tanggal_pengajuan && !is_null($rr->kekuatan_stock_fg)) {
                        $daysElapsed = (int) $rr->tanggal_pengajuan->startOfDay()->diffInDays(now()->startOfDay());
                        $sisaStockFg = $rr->kekuatan_stock_fg - $daysElapsed;

                        if ($sisaStockFg <= 1) {
                            $stockLevel = 'critical';
                        } elseif ($sisaStockFg <= 2) {
                            $stockLevel = 'warning';
                        }
                    }
                @endphp
                <tr data-status="{{ $rr->status }}"
                    @if($stockLevel) data-stock-level="{{ $stockLevel }}" @endif
                    @if($rr->status === 'on_process') data-on-process-at="{{ $rr->on_process_at?->toISOString() }}" @endif
                >
                    <td style="font-weight: bold;">{{ $rr->no }}</td>
                    <td>{{ $rr->tanggal_pengajuan ? $rr->tanggal_pengajuan->format('d/m/Y') : '-' }}</td>
                    <td>{{ $rr->line_mesin ? substr($rr->line_mesin, 0, 7) : '-' }}</td>
                    <td style="font-weight: bold;">{{ $rr->part_no }}</td>
                    <td style="font-weight: bold;">{{ $rr->process_no }}</td>
                    <td>
                        @php
                            $kategoriClasses = [
                                'Dies'         => 'kategori-dies',
                                'Burry'        => 'kategori-burry',
                                'Dimensi'      => 'kategori-dimensi',
                                'Human Error'  => 'kategori-human_error',
                                'Accessories'  => 'kategori-accessories',
                            ];
                            $kategoriClass = $kategoriClasses[$rr->kategori_problem] ?? 'kategori-dies';
                        @endphp
                        <span class="kategori-text {{ $kategoriClass }}">{{ $rr->kategori_problem }}</span>
                    </td>
                    <td>{{ $rr->group }} / {{ $rr->shift }}</td>
                    <td style="font-weight: bold;">{{ $rr->kekuatan_stock_fg }} Hari</td>

                    <td>
                        @if($rr->status === 'on_process' && $rr->on_process_at)
                            <span class="durasi-live" data-durasi-start="{{ $rr->on_process_at->timestamp }}">-</span>
                        @elseif($rr->status === 'on_trial' && $rr->on_trial_at && $rr->on_process_at)
                            {{ \App\Models\RequestRepair::formatDurasi($rr->on_process_at->diffInSeconds($rr->on_trial_at)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rr->status === 'on_trial' && $rr->on_trial_at)
                            <span class="durasi-trial-live" data-durasi-trial-start="{{ $rr->on_trial_at->timestamp }}">-</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $rr->picNamesString() }}</td>
                    <td class="status py-1">
                        @php
                            $statusClasses = [
                                'open'       => 'status-open',
                                'on_process' => 'status-on_process',
                                'on_trial'   => 'status-on_trial',
                            ];
                            $statusLabels = [
                                'open'       => 'Open',
                                'on_process' => 'On Process',
                                'on_trial'   => 'On Trial',
                            ];
                            $statusClass = $statusClasses[$rr->status] ?? 'status-open';
                            $statusLabel = $statusLabels[$rr->status] ?? $rr->status;
                        @endphp

                        <span class="status-badge {{ $statusClass }} px-3 py-1 inline-block rounded-lg">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 120px; height: 120px; margin: 0 auto 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <p style="font-size: 1.5rem; font-weight: bold;">No Active Request Repairs</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Auto refresh setiap 30 detik untuk monitoring real-time
setInterval(function() {
    location.reload();
}, 30000);

// Format durasi: HH.MM.SS, kalau lebih dari 1 hari jadi XH.HH.MM.SS
function formatDurasiJS(seconds) {
    const days  = Math.floor(seconds / 86400);
    const hours = Math.floor((seconds % 86400) / 3600);
    const mins  = Math.floor((seconds % 3600) / 60);
    const secs  = seconds % 60;

    const hoursStr = String(hours).padStart(2, '0');
    const minsStr  = String(mins).padStart(2, '0');
    const secsStr  = String(secs).padStart(2, '0');

    if (days > 0) {
        return days + 'H.' + hoursStr + '.' + minsStr + '.' + secsStr;
    }
    return hoursStr + '.' + minsStr + '.' + secsStr;
}

// Live durasi counter untuk row status on_process
function updateDurasiLive() {
    document.querySelectorAll('.durasi-live[data-durasi-start]').forEach(function (el) {
        const start   = parseInt(el.getAttribute('data-durasi-start'), 10);
        const now     = Math.floor(Date.now() / 1000);
        const elapsed = now - start;
        el.textContent = formatDurasiJS(elapsed);
    });
}

// Live durasi counter untuk row status on_trial (mulai dari on_trial_at)
function updateDurasiTrialLive() {
    document.querySelectorAll('.durasi-trial-live[data-durasi-trial-start]').forEach(function (el) {
        const start   = parseInt(el.getAttribute('data-durasi-trial-start'), 10);
        const now     = Math.floor(Date.now() / 1000);
        const elapsed = now - start;
        el.textContent = formatDurasiJS(elapsed);
    });
}

updateDurasiLive();
updateDurasiTrialLive();
setInterval(function () {
    updateDurasiLive();
    updateDurasiTrialLive();
}, 1000);

// Debug: Log current request repairs count
console.log('Total request repairs displayed: {{ $requestRepairs->count() }}');
</script>
@endpush