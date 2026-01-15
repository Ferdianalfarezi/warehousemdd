{{-- resources/views/request-parts/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Request Parts')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Request Parts</h1>
            <p class="text-gray-600 mt-1">Kelola request part ke warehouse system</p>
        </div>
        <div class="flex items-center space-x-3">
            
            
            <a href="{{ route('parts.index') }}" class="inline-flex items-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900 mt-1" id="stat-pending">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase">Approved Kadiv</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1" id="stat-approved-kadiv">{{ $requests->where('status', 'approved_kadiv')->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase">Approved Kagud</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1" id="stat-approved-kagud">{{ $requests->where('status', 'approved_kagud')->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase">Ready/Completed</p>
                    <p class="text-2xl font-bold text-green-900 mt-1" id="stat-completed">{{ $requests->whereIn('status', ['ready', 'completed'])->count() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-red-600 uppercase">Rejected</p>
                    <p class="text-2xl font-bold text-red-900 mt-1" id="stat-rejected">{{ $requests->where('status', 'rejected')->count() }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No Request</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Requester</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 transition" 
                            data-id="{{ $request->id }}" 
                            data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">{{ $request->request_number }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $request->tanggal_request->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $request->requester_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $request->items->count() }} items
                                </span>
                            </td>
                            <td class="px-6 py-4 status-cell">
                                <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ getStatusBadgeClass($request->status) }}">
                                    {{ getStatusLabel($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate keterangan-cell">
                                {{ $request->keterangan ?? getKeteranganByStatus($request->status) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 action-buttons">
                                    <button 
                                        onclick="showRequestDetail({{ $request->id }})"
                                        class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition"
                                    >
                                        Detail
                                    </button>
                                    
                                    @if($request->status === 'completed')
                                        <button 
                                            onclick="verifyRequest({{ $request->id }})"
                                            class="verify-btn bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition"
                                        >
                                            Verify
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada request</p>
                                <p class="text-gray-500 text-sm">Request part akan muncul disini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
@include('request-parts.detail-modal')

@endsection

@push('scripts')
<script>
// ========================================
// HELPER FUNCTIONS (untuk JS)
// ========================================
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved_kadiv': 'bg-blue-100 text-blue-800',
        'approved_kagud': 'bg-purple-100 text-purple-800',
        'ready': 'bg-emerald-100 text-emerald-800',
        'completed': 'bg-green-100 text-green-800',
        'verified': 'bg-gray-100 text-gray-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'Pending',
        'approved_kadiv': 'Approved Kadiv',
        'approved_kagud': 'Approved Kagud',
        'ready': 'Ready',
        'completed': 'Completed',
        'verified': 'Verified',
        'rejected': 'Rejected',
    };
    return labels[status] || status;
}

// ========================================
// MODAL FUNCTIONS
// ========================================
async function showRequestDetail(id) {
    try {
        const response = await fetch(`/request-parts/${id}`);
        const data = await response.json();
        
        if (!data.success) throw new Error('Failed to fetch data');
        
        const request = data.data;
        
        document.getElementById('detailRequestNumber').textContent = request.request_number;
        document.getElementById('detailRequester').textContent = request.requester_name;
        document.getElementById('detailDepartment').textContent = request.department?.name ?? 'N/A';
        document.getElementById('detailTanggal').textContent = new Date(request.tanggal_request).toLocaleString('id-ID');
        document.getElementById('detailStatus').innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(request.status)}">${getStatusLabel(request.status)}</span>`;
        document.getElementById('detailKeterangan').textContent = request.keterangan ?? '-';
        document.getElementById('detailCatatan').textContent = request.catatan ?? '-';
        
        const tbody = document.getElementById('detailItemsBody');
        tbody.innerHTML = '';
        
        request.items.forEach((item, index) => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${item.part.kode_part}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${item.part.nama}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${item.quantity} ${item.part.satuan}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${item.keterangan ?? '-'}</td>
                </tr>
            `);
        });
        
        const modal = document.getElementById('requestDetailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modal.classList.add('modal-fade-in'), 10);
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal memuat detail request', 'error');
    }
}

function closeRequestDetailModal() {
    const modal = document.getElementById('requestDetailModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function verifyRequest(id) {
    const result = await Swal.fire({
        title: 'Verifikasi Penerimaan',
        text: 'Apakah barang sudah diterima dengan benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Verify',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/request-parts/${id}/verify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#3b82f6'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        }
    }
}

// ESC to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRequestDetailModal();
});
</script>
@endpush

@php
function getStatusBadgeClass($status) {
    return match($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved_kadiv' => 'bg-blue-100 text-blue-800',
        'approved_kagud' => 'bg-purple-100 text-purple-800',
        'ready' => 'bg-emerald-100 text-emerald-800',
        'completed' => 'bg-green-100 text-green-800',
        'verified' => 'bg-gray-100 text-gray-800',
        'rejected' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
}

function getStatusLabel($status) {
    return match($status) {
        'pending' => 'Pending',
        'approved_kadiv' => 'Approved Kadiv',
        'approved_kagud' => 'Approved Kagud',
        'ready' => 'Ready',
        'completed' => 'Completed',
        'verified' => 'Verified',
        'rejected' => 'Rejected',
        default => ucfirst($status),
    };
}

function getKeteranganByStatus($status) {
    return match($status) {
        'pending' => 'Menunggu approval Kepala Dept.',
        'approved_kadiv' => 'Menunggu approval PUD',
        'approved_kagud' => 'Barang sedang disiapkan',
        'ready' => 'Barang siap diambil',
        'completed' => 'Menunggu verifikasi penerimaan',
        'verified' => 'Request selesai - Barang diterima',
        'rejected' => 'Request ditolak',
        default => '-',
    };
}
@endphp