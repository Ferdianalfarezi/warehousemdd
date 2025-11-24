@extends('layouts.app')

@section('title', 'General Checkups')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">General Checkups</h1>
            <p class="text-gray-600 mt-1">Manage maintenance checkups for scheduled items</p>
        </div>
        <button 
            onclick="autoPopulate()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Auto Populate</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Checkups</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- On Process -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On Process</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['on_process'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['today'] }}</p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">

            <!-- Search Box -->
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
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

            <!-- Status Filter -->
            <div class="flex-shrink-0">
                <select 
                    id="statusFilter" 
                    onchange="filterTable()"
                    class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="on_process">On Process</option>
                </select>
            </div>

            <!-- Line Filter -->
            <div class="flex-shrink-0">
                <select 
                    id="lineFilter" 
                    onchange="filterTable()"
                    class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="all">All Lines</option>
                    @foreach($lines as $line)
                        <option value="{{ $line }}">Line {{ $line }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page Selector -->
            <div class="flex-shrink-0">
                <select 
                    id="perPageSelect" 
                    onchange="changePerPage()"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Line</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Terjadwal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mulai Perbaikan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="checkupsTableBody">
                    @forelse($checkups as $checkup)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $checkup->status }}" data-line="{{ $checkup->line }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                @if($checkup->gambar)
                                    <img src="{{ asset('storage/barangs/'.$checkup->gambar) }}" 
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $checkup->kode_barang }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $checkup->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $checkup->line ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $checkup->tanggal_terjadwal->format('d/m/Y') }}
                            </td>
                            
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $checkup->mulai_perbaikan ? $checkup->mulai_perbaikan->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $checkup->status_badge_class }}">
                                    {{ $checkup->status_display }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModal({{ $checkup->id }})"
                                            class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-600 transition">
                                        Detail
                                    </button>
                                    
                                    @if($checkup->status === 'pending')
                                        <button onclick="startRepair({{ $checkup->id }})"
                                                class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition">
                                            Process
                                        </button>
                                    @elseif($checkup->status === 'on_process')
                                        <a href="{{ route('general-checkups.process', $checkup->id) }}"
                                                class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition inline-block">
                                            Lanjutkan
                                        </a>
                                    @endif
                                    
                                    <button onclick="deleteCheckup({{ $checkup->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No checkups found</p>
                                <p class="text-gray-500 text-sm">Click "Auto Populate" to load checkups from schedules</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Showing Entries -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="showingFrom" class="font-medium">1</span> to 
                    <span id="showingTo" class="font-medium">0</span> of 
                    <span id="totalEntries" class="font-medium">0</span> entries
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Detail Modal -->
@include('general-checkups.detail-modal')

@endsection

@push('scripts')
<script>
let allCheckups = [];
let filteredCheckups = [];
let currentPerPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize table data
    const rows = document.querySelectorAll('#checkupsTableBody tr');
    rows.forEach((row, index) => {
        if (!row.querySelector('td[colspan]')) {
            allCheckups.push({
                element: row.cloneNode(true),
                searchText: row.textContent.toLowerCase(),
                status: row.getAttribute('data-status'),
                line: row.getAttribute('data-line')
            });
        }
    });
    
    filteredCheckups = [...allCheckups];
    updateTable();
});

function searchTable() {
    filterTable();
}

function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const lineFilter = document.getElementById('lineFilter').value;
    
    filteredCheckups = allCheckups.filter(checkup => {
        const matchesSearch = search === '' || checkup.searchText.includes(search);
        const matchesStatus = statusFilter === 'all' || checkup.status === statusFilter;
        const matchesLine = lineFilter === 'all' || checkup.line === lineFilter;
        
        return matchesSearch && matchesStatus && matchesLine;
    });
    
    updateTable();
}

function changePerPage() {
    const val = document.getElementById('perPageSelect').value;
    currentPerPage = val === 'all' ? filteredCheckups.length : parseInt(val);
    updateTable();
}

function updateTable() {
    const tbody = document.getElementById('checkupsTableBody');
    tbody.innerHTML = '';
    
    const total = filteredCheckups.length;
    const display = currentPerPage > total ? total : currentPerPage;
    
    document.getElementById('showingFrom').textContent = total > 0 ? '1' : '0';
    document.getElementById('showingTo').textContent = display;
    document.getElementById('totalEntries').textContent = total;
    
    if (total === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                    No checkups match your filters
                </td>
            </tr>
        `;
        return;
    }
    
    filteredCheckups.slice(0, display).forEach((checkup, i) => {
        const row = checkup.element.cloneNode(true);
        row.querySelector('td:first-child').textContent = i + 1;
        tbody.appendChild(row);
    });
}

// Auto Populate
async function autoPopulate() {
    const result = await Swal.fire({
        title: 'Auto Populate Checkups?',
        text: "Sistem akan mengambil data dari schedule yang statusnya 'Segera' atau 'Hari Ini'",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Populate!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('/general-checkups/auto-populate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal melakukan auto populate!', 'error');
        }
    }
}

// Start Repair
async function startRepair(checkupId) {
    const result = await Swal.fire({
        title: 'Mulai Perbaikan?',
        text: "Waktu perbaikan akan dimulai saat ini",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Mulai!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/general-checkups/${checkupId}/start-repair`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Perbaikan Dimulai!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1000
                });
                window.location.href = data.redirect;
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal memulai perbaikan!', 'error');
        }
    }
}

// Open Detail Modal
async function openDetailModal(checkupId) {
    try {
        const response = await fetch(`/general-checkups/${checkupId}`);
        const data = await response.json();

        if (data.success) {
            const checkup = data.data;
            
            // Populate modal with data
            document.getElementById('detailImage').src = checkup.gambar 
                ? `/storage/barangs/${checkup.gambar}` 
                : '/images/no-image.png';
            document.getElementById('detailKode').textContent = checkup.kode_barang;
            document.getElementById('detailNama').textContent = checkup.nama;
            document.getElementById('detailLine').textContent = checkup.line || '-';
            document.getElementById('detailTerjadwal').textContent = new Date(checkup.tanggal_terjadwal).toLocaleDateString('id-ID');
            document.getElementById('detailCheckup').textContent = checkup.tanggal_checkup 
                ? new Date(checkup.tanggal_checkup).toLocaleDateString('id-ID') 
                : '-';
            document.getElementById('detailMulai').textContent = checkup.mulai_perbaikan 
                ? new Date(checkup.mulai_perbaikan).toLocaleString('id-ID') 
                : '-';
            document.getElementById('detailSelesai').textContent = checkup.waktu_selesai 
                ? new Date(checkup.waktu_selesai).toLocaleString('id-ID') 
                : '-';
            document.getElementById('detailStatus').textContent = checkup.status_display;
            document.getElementById('detailCatatan').textContent = checkup.catatan_umum || '-';

            // Show modal
            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            Swal.fire('Error!', 'Gagal memuat detail checkup', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail checkup', 'error');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Delete Checkup
async function deleteCheckup(checkupId) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "Checkup akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/general-checkups/${checkupId}`, {
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
            Swal.fire('Error!', 'Gagal menghapus checkup!', 'error');
        }
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endpush