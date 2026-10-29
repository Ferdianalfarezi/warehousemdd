@extends('layouts.andon')

@section('page-title', 'PREVENTIVE MONITORING')

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
    min-width: 100px;
    text-align: center;
}

.status-pending {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
}

.status-on_process {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

.status-upcoming {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
}

.status-finish {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}

/* Row highlight */
tr[data-status="on_process"] {
    background: rgba(59, 130, 246, 0.05);
}

tr[data-status="upcoming"] {
    background: rgba(245, 158, 11, 0.05);
}

/* Merah: mulai perbaikan -> sekarang > 3 jam */
tr.row-overdue,
tr.row-overdue td {
    background: rgba(239, 68, 68, 0.15) !important;
}

/* Counter */
.duration-counter {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

/* Image styling */
.checkup-image {
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
}
</style>

<div style="overflow-x: auto; margin-top:10px;">
    <table class="table-bordered theme-table table-dark-custom" style="width: 100%">
        <thead>
            <tr class="text-center" style="font-size: 1.25rem;">
                <th style="width: 150px;">Kode Barang</th>
                <th style="width: 250px;">Nama Barang</th>
                <th style="width: 180px;">PIC</th>
                <th style="width: 150px;">Tgl Terjadwal</th>
                <th style="width: 180px;">Mulai Perbaikan</th>
                <th style="width: 200px;">Durasi</th>
                <th style="width: 150px;">Status</th>
            </tr>
        </thead>
        <tbody data-live="table" class="table-dark-custom text-center" id="tableBody" style="font-size: 1.25rem;">
            @if($checkups->isEmpty() && $upcomings->isEmpty())
                <tr>
                    <td colspan="7">
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 120px; height: 120px; margin: 0 auto 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <p style="font-size: 1.5rem; font-weight: bold;">No Checkups On Process or Upcoming</p>
                        </div>
                    </td>
                </tr>
            @else
                {{-- ON PROCESS (GeneralCheckup) --}}
                @foreach($checkups as $checkup)
                    @php
                        // Merah kalau durasi pengerjaan sudah lewat 3 jam
                        $isOverdue = $checkup->mulai_perbaikan
                            && $checkup->mulai_perbaikan->diffInMinutes(now(), false) > 4 * 60;
                    @endphp
                    <tr data-status="on_process" class="{{ $isOverdue ? 'row-overdue' : '' }}">
                        <td style="font-weight: bold;">{{ $checkup->kode_barang }}</td>
                        <td style="font-weight: bold;">{{ $checkup->nama }}</td>
                        <td>{{ $checkup->starter->nama ?? '-' }}</td>
                        <td>{{ $checkup->tanggal_terjadwal->format('d/m/Y') }}</td>
                        <td>
                            {{ $checkup->mulai_perbaikan ? $checkup->mulai_perbaikan->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            @if($checkup->mulai_perbaikan)
                                <span class="duration-counter" data-start="{{ $checkup->mulai_perbaikan->timestamp * 1000 }}">
                                    --:--:--
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="status py-1">
                            <span class="status-badge status-on_process px-3 py-1 inline-block rounded-lg">
                                {{ $checkup->status_display }}
                            </span>
                        </td>
                    </tr>
                @endforeach

                {{-- UPCOMING (Schedule, 10 terdekat) --}}
                @foreach($upcomings as $schedule)
                    <tr data-status="upcoming">
                        <td style="font-weight: bold;">{{ $schedule->kode_barang }}</td>
                        <td style="font-weight: bold;">{{ $schedule->nama }}</td>
                        <td>-</td>
                        <td>{{ $schedule->service_berikutnya ? $schedule->service_berikutnya->format('d/m/Y') : '-' }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="status py-1">
                            <span class="status-badge status-upcoming px-3 py-1 inline-block rounded-lg">
                                UPCOMING
                            </span>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Batas durasi pengerjaan (4 jam)
const OVERDUE_LIMIT_MS = 4 * 60 * 60 * 1000;

function pad(n) {
    return String(n).padStart(2, '0');
}

function updateCounters() {
    const now = Date.now();

    document.querySelectorAll('.duration-counter').forEach(function (el) {
        const start = parseInt(el.dataset.start, 10);
        const diff = Math.max(0, now - start);

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        const time = pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
        el.textContent = days > 0 ? days + ' hari ' + time : time;

        // Live switch ke merah kalau sudah lewat 3 jam
        if (diff > OVERDUE_LIMIT_MS) {
            el.closest('tr').classList.add('row-overdue');
        }
    });
}

updateCounters();
setInterval(updateCounters, 1000);

// Data tabel di-update otomatis oleh layout andon (tanpa reload)
document.addEventListener('andon:updated', updateCounters);

console.log('On process: {{ $checkups->count() }}, Upcoming: {{ $upcomings->count() }}');
</script>
@endpush