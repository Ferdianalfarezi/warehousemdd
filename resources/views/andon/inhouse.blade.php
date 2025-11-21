@extends('layouts.andon')

@section('page-title', 'INHOUSE REPAIR MONITORING')

@section('content')
<style>
.status-badge {
    display: inline-block;
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-on_process {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

.status-completed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}

.status-pending {
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
</style>

<div style="overflow-x: auto; margin-top:10px;">
    <table class="table-bordered theme-table table-dark-custom" style="width: 100%">
        <thead>
            <tr class="text-center" style="font-size: 1.25rem;">
                <th>No</th>
                <th>Barang</th>
                <th>Kode</th>
                <th>Mesin</th>
                <th>Problem</th>
                <th>Confirmed At</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="table-dark-custom text-center" id="tableBody" style="font-size: 1.25rem;">
            @forelse($requests as $request)
                <tr data-status="{{ $request->status }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $request->generalCheckup->nama }}</td>
                    <td>{{ $request->generalCheckup->kode_barang }}</td>
                    <td>{{ $request->mesin }}</td>
                    <td>{{ Str::limit($request->problem, 50) }}</td>
                    <td>{{ $request->confirmed_at ? $request->confirmed_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="status">
                        @php
                            $statusStyles = [
                                'on_process' => 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);',
                                'completed' => 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);',
                            ];
                            $style = $statusStyles[$request->status] ?? '';
                        @endphp
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 700; font-size: 0.875rem; text-align: center; min-width: 100px; {{ $style }}">
                            {{ strtoupper(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <button 
                                onclick="openDetailModal({{ $request->id }})"
                                style="background: #6b7280; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.25rem; border: none; cursor: pointer; font-weight: 500;"
                                onmouseover="this.style.background='#4b5563'"
                                onmouseout="this.style.background='#6b7280'"
                            >
                                Detail
                            </button>
                            @if($request->status === 'on_process')
                                <button 
                                    onclick="completeRequest({{ $request->id }})"
                                    style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.25rem; border: none; cursor: pointer; font-weight: 500;"
                                    onmouseover="this.style.background='#059669'"
                                    onmouseout="this.style.background='#10b981'"
                                >
                                    Selesai
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 120px; height: 120px; margin: 0 auto 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p style="font-size: 1.5rem; font-weight: bold;">No Data Available</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Detail Modal -->
<div id="detailModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: white; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); max-width: 48rem; width: 100%; max-height: 90vh; overflow-y: auto;">
        <!-- Header -->
        <div style="position: sticky; top: 0; background: white; border-bottom: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 1rem 1rem 0 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 1.5rem; font-weight: bold; color: #111827;">Detail Perbaikan Inhouse</h2>
                <button onclick="closeDetailModal()" style="color: #9ca3af; border: none; background: none; cursor: pointer; font-size: 1.5rem;">
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Barang</p>
                <p id="detailBarang" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Kode Barang</p>
                <p id="detailKode" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Mesin</p>
                <p id="detailMesin" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Problem</p>
                <p id="detailProblem" style="font-size: 0.875rem; color: #374151; background: #f9fafb; padding: 0.75rem; border-radius: 0.5rem;">-</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Proses yang Dilakukan</p>
                <p id="detailProses" style="font-size: 0.875rem; color: #374151; background: #f9fafb; padding: 0.75rem; border-radius: 0.5rem;">-</p>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <p style="font-size: 0.75rem; color: #6b7280;">Dikonfirmasi Oleh</p>
                    <p id="detailConfirmedBy" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; color: #6b7280;">Tanggal Konfirmasi</p>
                    <p id="detailConfirmedAt" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
                </div>
            </div>
            <div id="completedSection" style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <p style="font-size: 0.75rem; color: #6b7280;">Diselesaikan Oleh</p>
                    <p id="detailCompletedBy" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; color: #6b7280;">Tanggal Selesai</p>
                    <p id="detailCompletedAt" style="font-size: 0.875rem; font-weight: 600; color: #111827;">-</p>
                </div>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #6b7280;">Status</p>
                <span id="detailStatus" class="status-badge">-</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterStatus(status) {
    const rows = document.querySelectorAll('#tableBody tr');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Reset buttons
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Activate clicked button
    if (status === 'all') document.getElementById('filterAll').classList.add('active');
    if (status === 'on_process') document.getElementById('filterProcess').classList.add('active');
    if (status === 'completed') document.getElementById('filterCompleted').classList.add('active');
    
    // Filter rows
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return;
        
        const rowStatus = row.getAttribute('data-status');
        row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
    });
}

async function openDetailModal(requestId) {
    try {
        const response = await fetch(`/andon/inhouse/${requestId}`);
        const data = await response.json();

        if (data.success) {
            const request = data.data;
            
            document.getElementById('detailBarang').textContent = request.general_checkup.nama;
            document.getElementById('detailKode').textContent = request.general_checkup.kode_barang;
            document.getElementById('detailMesin').textContent = request.mesin;
            document.getElementById('detailProblem').textContent = request.problem;
            document.getElementById('detailProses').textContent = request.proses_dilakukan;
            document.getElementById('detailConfirmedBy').textContent = request.confirmed_by ? request.confirmed_by.name : '-';
            document.getElementById('detailConfirmedAt').textContent = request.confirmed_at ? new Date(request.confirmed_at).toLocaleString('id-ID') : '-';
            
            // Update status badge
            const statusBadge = document.getElementById('detailStatus');
            statusBadge.textContent = request.status.toUpperCase().replace('_', ' ');
            statusBadge.className = 'status-badge status-' + request.status;

            // Show completed section if completed
            if (request.status === 'completed') {
                document.getElementById('completedSection').style.display = 'grid';
                document.getElementById('detailCompletedBy').textContent = request.completed_by ? request.completed_by.name : '-';
                document.getElementById('detailCompletedAt').textContent = request.completed_at ? new Date(request.completed_at).toLocaleString('id-ID') : '-';
            } else {
                document.getElementById('completedSection').style.display = 'none';
            }

            const modal = document.getElementById('detailModal');
            modal.style.display = 'flex';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal memuat detail');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.style.display = 'none';
}

async function completeRequest(requestId) {
    if (!confirm('Tandai perbaikan sebagai selesai?')) return;

    try {
        const response = await fetch(`/andon/inhouse/${requestId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal menyelesaikan perbaikan!');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endpush