@extends('layouts.app')

@section('title', 'History Checkups')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">History Checkups</h1>
            <p class="text-gray-600 mt-1">Completed maintenance checkup records</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Total History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total History</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['this_month'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Avg Duration -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg Duration</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['avg_duration'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">minutes</p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Parts Used -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Parts Used</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['total_parts_used'] }}</p>
                </div>
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Success Rate</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['success_rate'] }}%</p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('history-checkups.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">

            <!-- Search Box -->
            <div class="w-full md:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Search by kode, nama barang..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Date Range -->
            <div class="flex-shrink-0">
                <input 
                    type="date"
                    name="start_date"
                    value="{{ request('start_date') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
            </div>
            <span class="text-gray-500">to</span>
            <div class="flex-shrink-0">
                <input 
                    type="date"
                    name="end_date"
                    value="{{ request('end_date') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
            </div>

            <!-- Line Filter -->
            <div class="flex-shrink-0">
                <select 
                    name="line"
                    class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">All Lines</option>
                    @foreach($lines as $line)
                        <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>Line {{ $line }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <button 
                type="submit"
                class="px-6 py-2.5 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition"
            >
                Filter
            </button>

            @if(request()->hasAny(['start_date', 'end_date', 'line', 'barang_id']))
                <a 
                    href="{{ route('history-checkups.index') }}"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
                >
                    Reset
                </a>
            @endif

        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Line</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Checkup</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mulai</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Selesai</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">OK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NG</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Parts</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    @forelse($histories as $history)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                @if($history->gambar)
                                    <img src="{{ asset('storage/barangs/'.$history->gambar) }}" 
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $history->kode_barang }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $history->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $history->line ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $history->tanggal_checkup->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $history->mulai_perbaikan->format('H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $history->waktu_selesai->format('H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $history->durasi_display }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $history->total_ok }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $history->total_ng }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $history->total_part_used }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailHistoryModal({{ $history->id }})"
                                            class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-600 transition">
                                        Detail
                                    </button>
                                    <button onclick="deleteHistory({{ $history->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No history found</p>
                                <p class="text-gray-500 text-sm">Complete some checkups to see history</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Detail Modal -->
@include('history-checkups.detail-modal')

@endsection

@push('scripts')
<script>
function searchTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#historyTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
}

async function openDetailHistoryModal(historyId) {
    try {
        const response = await fetch(`/history-checkups/${historyId}`);
        const data = await response.json();

        if (data.success) {
            const history = data.data;
                
            // Basic Info
            document.getElementById('historyDetailImage').src = history.gambar 
                ? `/storage/barangs/${history.gambar}` 
                : '/images/no-image.png';
            document.getElementById('historyDetailKode').textContent = history.kode_barang;
            document.getElementById('historyDetailNama').textContent = history.nama;
            document.getElementById('historyDetailLine').textContent = history.line || '-';
            document.getElementById('historyDetailTerjadwal').textContent = 
                new Date(history.tanggal_terjadwal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            
            // Timeline Info
            document.getElementById('historyDetailCheckup').textContent = 
                new Date(history.tanggal_checkup).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            document.getElementById('historyDetailMulai').textContent = 
                new Date(history.mulai_perbaikan).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            document.getElementById('historyDetailSelesai').textContent = 
                new Date(history.waktu_selesai).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            
            // Calculate Duration (Fix untuk durasi kosong)
            const mulai = new Date(history.mulai_perbaikan);
            const selesai = new Date(history.waktu_selesai);
            const diffMs = selesai - mulai;
            const diffMins = Math.floor(diffMs / 60000);
            const hours = Math.floor(diffMins / 60);
            const mins = diffMins % 60;
            
            let durasiText = '';
            if (hours > 0) {
                durasiText = `${hours} jam ${mins} menit`;
            } else {
                durasiText = `${mins} menit`;
            }
            document.getElementById('historyDetailDurasi').textContent = durasiText;
            
            // Stats
            document.getElementById('historyDetailOK').textContent = history.total_ok;
            document.getElementById('historyDetailNG').textContent = history.total_ng;
            document.getElementById('historyDetailParts').textContent = history.total_part_used;
            document.getElementById('historyDetailCatatan').textContent = history.catatan_umum || 'Tidak ada catatan';

            // Checkup Details
            const detailsContainer = document.getElementById('historyCheckupDetails');
            detailsContainer.innerHTML = '';
            
            if (history.details && history.details.length > 0) {
                history.details.forEach((detail, index) => {
                    const statusBadge = detail.status === 'ok' 
                        ? '<span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">✓ OK</span>'
                        : '<span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold">✗ NG</span>';
                    
                    const bgColor = detail.status === 'ok' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                    
                    // NG Action Badge
                    let actionBadge = '';
                    if (detail.status === 'ng' && detail.ng_action_type) {
                        if (detail.ng_action_type === 'part') {
                            actionBadge = '<span class="ml-2 px-2 py-1 bg-orange-500 text-white rounded text-xs font-semibold">🔧 Ganti Part</span>';
                        } else if (detail.ng_action_type === 'inhouse') {
                            actionBadge = '<span class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-xs font-semibold">🏭 Inhouse</span>';
                        } else if (detail.ng_action_type === 'outhouse') {
                            actionBadge = '<span class="ml-2 px-2 py-1 bg-purple-500 text-white rounded text-xs font-semibold">🏢 Outhouse</span>';
                        }
                    }
                    
                    // Part Replacements for this detail
                    let partReplacementsHTML = '';
                    if (detail.part_replacements && detail.part_replacements.length > 0) {
                        partReplacementsHTML = `
                            <div class="mt-3 space-y-2 bg-white rounded-lg p-3 border border-orange-300 animate-fadeIn">
                                <p class="text-xs font-bold text-orange-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Part yang Digunakan:
                                </p>
                                ${detail.part_replacements.map(part => `
                                    <div class="bg-orange-50 rounded p-2 border border-orange-200">
                                        <p class="text-xs font-semibold text-gray-900">${part.nama_part}</p>
                                        <p class="text-xs text-gray-600">${part.kode_part} - Qty: ${part.quantity_used}</p>
                                        ${part.catatan ? `<p class="text-xs text-gray-500 italic mt-1">${part.catatan}</p>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    }

                    // Inhouse/Outhouse Request Info
                    let requestInfoHTML = '';
                    if (detail.inhouse_request) {
                        const req = detail.inhouse_request;
                        requestInfoHTML = `
                            <div class="mt-3 space-y-2 bg-white rounded-lg p-3 border border-blue-300 animate-fadeIn">
                                <p class="text-xs font-bold text-blue-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Inhouse Request:
                                </p>
                                <div class="bg-blue-50 rounded p-2 border border-blue-200 space-y-1">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-600">Problem:</span>
                                            <p class="font-semibold text-gray-900">${req.problem || '-'}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Mesin:</span>
                                            <p class="font-semibold text-gray-900">${req.mesin || '-'}</p>
                                        </div>
                                    </div>
                                    ${req.proses_dilakukan ? `
                                        <div class="text-xs">
                                            <span class="text-gray-600">Proses:</span>
                                            <p class="font-semibold text-gray-900">${req.proses_dilakukan}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    } else if (detail.outhouse_request) {
                        const req = detail.outhouse_request;
                        requestInfoHTML = `
                            <div class="mt-3 space-y-2 bg-white rounded-lg p-3 border border-purple-300 animate-fadeIn">
                                <p class="text-xs font-bold text-purple-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Outhouse Request:
                                </p>
                                <div class="bg-purple-50 rounded p-2 border border-purple-200 space-y-1">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-600">Problem:</span>
                                            <p class="font-semibold text-gray-900">${req.problem || '-'}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Mesin:</span>
                                            <p class="font-semibold text-gray-900">${req.mesin || '-'}</p>
                                        </div>
                                    </div>
                                    ${req.supplier ? `
                                        <div class="text-xs">
                                            <span class="text-gray-600">Supplier:</span>
                                            <p class="font-semibold text-gray-900">${req.supplier}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    }
                    
                    detailsContainer.innerHTML += `
                        <div class="border ${bgColor} rounded-lg p-4 hover:shadow-md transition-all duration-200 animate-slideIn" style="animation-delay: ${index * 50}ms">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-gray-700 text-white px-3 py-1 rounded-full text-xs font-bold">
                                        ${detail.poin}
                                    </span>
                                    <p class="text-sm font-bold text-gray-900">${detail.nama_bagian}</p>
                                </div>
                                <div class="flex items-center">
                                    ${statusBadge}
                                    ${actionBadge}
                                </div>
                            </div>
                            ${detail.catatan ? `
                                <div class="mt-2 pl-4 border-l-2 ${detail.status === 'ok' ? 'border-green-400' : 'border-red-400'}">
                                    <p class="text-xs text-gray-600 italic">Catatan: ${detail.catatan}</p>
                                </div>
                            ` : ''}
                            ${partReplacementsHTML}
                            ${requestInfoHTML}
                        </div>
                    `;
                });
            } else {
                detailsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Tidak ada detail checkup</p>';
            }

            // Part Replacements (standalone)
            const partsContainer = document.getElementById('historyPartReplacements');
            const partsSection = document.getElementById('historyPartReplacementsSection');
            partsContainer.innerHTML = '';
            
            const standaloneReplacements = history.part_replacements 
                ? history.part_replacements.filter(part => !part.history_checkup_detail_id)
                : [];
            
            if (standaloneReplacements.length > 0) {
                partsSection.classList.remove('hidden');
                standaloneReplacements.forEach(part => {
                    partsContainer.innerHTML += `
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-orange-200 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">${part.nama_part}</p>
                                    <p class="text-xs text-gray-600 mt-1">${part.kode_part}</p>
                                    <div class="flex items-center mt-2">
                                        <span class="px-2 py-1 bg-orange-200 text-orange-800 rounded text-xs font-semibold">
                                            Qty: ${part.quantity_used}
                                        </span>
                                    </div>
                                    ${part.catatan ? `
                                        <p class="text-xs text-gray-500 italic mt-2 border-l-2 border-orange-400 pl-2">${part.catatan}</p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                partsSection.classList.add('hidden');
            }

            // Show modal with animation
            const modal = document.getElementById('historyDetailModal');
            const modalContent = document.getElementById('historyModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');

                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);

        } else {
            Swal.fire('Error!', 'Gagal memuat detail history', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail history', 'error');
    }
}

function closeHistoryDetailModal() {
    const modal = document.getElementById('historyDetailModal');
    const modalContent = document.getElementById('historyModalContent');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeHistoryDetailModal();
});


// Keyboard shortcut
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHistoryDetailModal();
    }
});

async function deleteHistory(historyId) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "History akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/history-checkups/${historyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Gagal menghapus history!', 'error');
        }
    }
}
</script>
@endpush