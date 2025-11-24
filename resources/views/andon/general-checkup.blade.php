@extends('layouts.andon')

@section('page-title', 'GENERAL CHECKUP MONITORING')

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

.status-finish {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
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

/* Row highlight untuk on_process */
tr[data-status="on_process"] {
    background: rgba(59, 130, 246, 0.05);
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
                <th style="width: 100px;">Line</th>
                <th style="width: 150px;">Tgl Terjadwal</th>
                <th style="width: 180px;">Mulai Perbaikan</th>
                <th style="width: 150px;">Status</th>
            </tr>
        </thead>
        <tbody class="table-dark-custom text-center" id="tableBody" style="font-size: 1.25rem;">
            @forelse($checkups as $checkup)
                <tr data-status="{{ $checkup->status }}">
                    <td style="font-weight: bold;">{{ $checkup->kode_barang }}</td>
                    <td style="font-weight: bold;">{{ $checkup->nama }}</td>
                    <td>{{ $checkup->line ?? '-' }}</td>
                    <td>{{ $checkup->tanggal_terjadwal->format('d/m/Y') }}</td>
                    <td>
                        {{ $checkup->mulai_perbaikan ? $checkup->mulai_perbaikan->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="status py-1">
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'on_process' => 'status-on_process',
                                'finish' => 'status-finish',
                            ];
                            $statusClass = $statusClasses[$checkup->status] ?? 'status-pending';
                        @endphp

                        <span class="status-badge {{ $statusClass }} px-3 py-1 inline-block rounded-lg">
                            {{ $checkup->status_display }}
                        </span>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 120px; height: 120px; margin: 0 auto 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <p style="font-size: 1.5rem; font-weight: bold;">No Checkups Available</p>
                            <p style="font-size: 1rem; color: #9ca3af; margin-top: 0.5rem;">No checkup data to display at this time</p>
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

// Debug: Log current checkups count
console.log('Total checkups displayed: {{ $checkups->count() }}');
</script>
@endpush