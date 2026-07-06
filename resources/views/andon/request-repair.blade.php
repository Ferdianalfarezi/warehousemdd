@extends('layouts.andon')

@section('page-title', 'REQUEST REPAIR MONITORING')

@section('content')
<style>
.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    min-width: 120px;
    text-align: center;
}

.status-open {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
}

.status-on_process {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

.status-on_trial {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
}

.status-badge {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Row highlight untuk on_process & on_trial */
tr[data-status="on_process"] {
    background: rgba(59, 130, 246, 0.05);
}

tr[data-status="on_trial"] {
    background: rgba(245, 158, 11, 0.05);
}

/* Kategori badge */
.kategori-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.8rem;
}

.kategori-dies { background: #dbeafe; color: #1e40af; }
.kategori-burry { background: #fef9c3; color: #854d0e; }
.kategori-dimensi { background: #ede9fe; color: #5b21b6; }
.kategori-human_error { background: #fee2e2; color: #991b1b; }
.kategori-accessories { background: #dcfce7; color: #166534; }

/* Image styling */
.repair-image {
    width: 64px;
    height: 64px;
    border-radius: 0.5rem;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.no-image-placeholder {
    width: 64px;
    height: 64px;
    background: #f3f4f6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
    margin: 0 auto;
}

/* Durasi live */
.durasi-live {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: #2563eb;
}
</style>

<div style="overflow-x: auto; margin-top:10px;">
    <table class="table-bordered theme-table table-dark-custom" style="width: 100%">
        <thead>
            <tr class="text-center" style="font-size: 1.15rem;">
                <th style="width: 140px;">No. Request</th>
                <th style="width: 110px;">Tgl Pengajuan</th>
                <th style="width: 100px;">Line / Mesin</th>
                <th style="width: 130px;">Part No</th>
                <th style="width: 220px;">Nama</th>
                <th style="width: 130px;">Kategori</th>
                <th style="width: 90px;">Grp / Shift</th>
                <th style="width: 90px;">Gambar</th>
                <th style="width: 150px;">Mulai Proses</th>
                <th style="width: 130px;">Durasi</th>
                <th style="width: 140px;">Status</th>
            </tr>
        </thead>
        <tbody class="table-dark-custom text-center" id="tableBody" style="font-size: 1.15rem;">
            @forelse($requestRepairs as $rr)
                <tr data-status="{{ $rr->status }}"
                    @if($rr->status === 'on_process') data-on-process-at="{{ $rr->on_process_at?->toISOString() }}" @endif
                >
                    <td style="font-weight: bold;">{{ $rr->no }}</td>
                    <td>{{ $rr->tanggal_pengajuan ? $rr->tanggal_pengajuan->format('d/m/Y') : '-' }}</td>
                    <td>{{ $rr->line_mesin ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ $rr->part_no }}</td>
                    <td style="text-align: left;">{{ $rr->nama }}</td>
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
                        <span class="kategori-badge {{ $kategoriClass }}">{{ $rr->kategori_problem }}</span>
                    </td>
                    <td>{{ $rr->group }} / {{ $rr->shift }}</td>
                    <td>
                        @if($rr->gambar_url)
                            <img src="{{ $rr->gambar_url }}" alt="Gambar" class="repair-image" style="margin: 0 auto;">
                        @else
                            <div class="no-image-placeholder">
                                <svg fill="none" stroke="#9ca3af" viewBox="0 0 24 24" style="width: 28px; height: 28px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ $rr->on_process_at ? $rr->on_process_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td>
                        @if($rr->status === 'on_process' && $rr->on_process_at)
                            <span class="durasi-live" data-durasi-start="{{ $rr->on_process_at->timestamp }}">-</span>
                        @elseif($rr->status === 'on_trial' && $rr->on_trial_at && $rr->on_process_at)
                            {{ \App\Models\RequestRepair::formatDurasi($rr->on_process_at->diffInSeconds($rr->on_trial_at)) }}
                        @else
                            -
                        @endif
                    </td>
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

// Live durasi counter untuk row yang status on_process
function formatDurasiJS(seconds) {
    const days  = Math.floor(seconds / 86400);
    const hours = Math.floor((seconds % 86400) / 3600);
    const mins  = Math.floor((seconds % 3600) / 60);
    const secs  = seconds % 60;
    const parts = [];
    if (days)  parts.push(days  + 'h ');
    if (hours) parts.push(hours + 'j ');
    parts.push(String(mins).padStart(2, '0') + 'm ' + String(secs).padStart(2, '0') + 'd');
    return parts.join('');
}

function updateDurasiLive() {
    document.querySelectorAll('.durasi-live[data-durasi-start]').forEach(function (el) {
        const start   = parseInt(el.getAttribute('data-durasi-start'), 10);
        const now     = Math.floor(Date.now() / 1000);
        const elapsed = now - start;
        el.textContent = formatDurasiJS(elapsed);
    });
}

updateDurasiLive();
setInterval(updateDurasiLive, 1000);

// Debug: Log current request repairs count
console.log('Total request repairs displayed: {{ $requestRepairs->count() }}');
</script>
@endpush