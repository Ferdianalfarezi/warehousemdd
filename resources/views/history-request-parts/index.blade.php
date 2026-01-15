{{-- resources/views/history-request-parts/index.blade.php --}}

@extends('layouts.app')

@section('title', 'History Request Parts')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">History Request Parts</h1>
            <p class="text-gray-600 mt-1">Riwayat request part yang telah selesai</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('request-parts.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Active Requests
            </a>
            <a href="{{ route('parts.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase">Total Verified</p>
                    <p class="text-2xl font-bold text-green-900 mt-1">{{ $histories->where('status', 'verified')->count() }}</p>
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
                    <p class="text-xs font-semibold text-red-600 uppercase">Total Rejected</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ $histories->where('status', 'rejected')->count() }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase">Total Requests</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $histories->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase">Total Items</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $histories->sum(fn($h) => $h->items->count()) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0">
            <!-- Search Box -->
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchHistory"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Search by request number, requester..."
                    onkeyup="searchHistory()"
                >
            </div>

            <!-- Status Filter -->
            <div class="flex-shrink-0">
                <select 
                    id="statusFilter" 
                    onchange="filterByStatus()"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                >
                    <option value="">All Status</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Request</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Requester</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Selesai</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    @forelse($histories as $history)
                        <tr class="hover:bg-gray-50 transition" data-search-text="{{ strtolower($history->request_number . ' ' . $history->requester_name) }}" data-status="{{ $history->status }}">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">{{ $history->request_number }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $history->tanggal_request->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $history->requester_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $history->items->count() }} items
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $history->status === 'verified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($history->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $history->tanggal_verified ? $history->tanggal_verified->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <button 
                                        onclick="showHistoryDetail({{ $history->id }})"
                                        class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition"
                                    >
                                        Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada history</p>
                                <p class="text-gray-500 text-sm">History request akan muncul disini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
@include('history-request-parts.detail-modal')

@endsection

@push('scripts')
<script>
    function searchHistory() {
        const searchTerm = document.getElementById('searchHistory').value.toLowerCase();
        const rows = document.querySelectorAll('#historyTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) return; // Skip empty state row
            
            const searchText = row.dataset.searchText || '';
            const match = searchText.includes(searchTerm);
            
            row.style.display = match ? '' : 'none';
        });
    }
    
    function filterByStatus() {
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('#historyTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) return;
            
            const status = row.dataset.status || '';
            const match = statusFilter === '' || status === statusFilter;
            
            row.style.display = match ? '' : 'none';
        });
    }
    
    async function showHistoryDetail(id) {
        try {
            const response = await fetch(`/history-request-parts/${id}`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error('Failed to fetch data');
            }
            
            const history = data.data;
            
            // Populate modal
            document.getElementById('historyDetailRequestNumber').textContent = history.request_number;
            document.getElementById('historyDetailRequester').textContent = history.requester_name;
            document.getElementById('historyDetailDepartment').textContent = history.department?.name ?? 'N/A';
            document.getElementById('historyDetailTanggalRequest').textContent = new Date(history.tanggal_request).toLocaleString('id-ID');
            document.getElementById('historyDetailTanggalCompleted').textContent = history.tanggal_completed ? new Date(history.tanggal_completed).toLocaleString('id-ID') : '-';
            document.getElementById('historyDetailTanggalVerified').textContent = history.tanggal_verified ? new Date(history.tanggal_verified).toLocaleString('id-ID') : '-';
            document.getElementById('historyDetailStatus').innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${history.status === 'verified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${history.status}</span>`;
            document.getElementById('historyDetailCatatan').textContent = history.catatan ?? '-';
            document.getElementById('historyDetailVerifiedBy').textContent = history.verified_by_user?.name ?? '-';
            
            // Populate items table
            const tbody = document.getElementById('historyDetailItemsBody');
            tbody.innerHTML = '';
            
            history.items.forEach((item, index) => {
                const row = `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">${item.part_code}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${item.part_name}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${item.quantity}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${item.quantity_approved ?? item.quantity}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${item.keterangan ?? '-'}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
            
            // Show modal
            const modal = document.getElementById('historyDetailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('modal-fade-in');
            }, 10);
            
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat detail history', 'error');
        }
    }
    
    function closeHistoryDetailModal() {
        const modal = document.getElementById('historyDetailModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeHistoryDetailModal();
        }
    });
</script>
@endpush