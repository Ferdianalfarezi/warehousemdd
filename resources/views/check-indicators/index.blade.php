@extends('layouts.app')

@section('title', 'Check Indicators')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Check Indicators</h1>
            <p class="text-gray-600 mt-1">Manage quality check standards for products</p>
        </div>
        <button 
            onclick="openCreateModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add Check Indicator</span>
        </button>
    </div>

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
                    placeholder="Search by barang, bagian..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Per Page Selector -->
            <div class="flex-shrink-0">
                <select 
                    id="perPageSelect" 
                    onchange="changePerPage()"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="20">20</option>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Parts</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Bagian</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Standards</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200" id="checkIndicatorsTableBody">
                    @forelse ($checkIndicators as $indicator)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- No -->
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Barang + Gambar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($indicator->barang->gambar)
                                        <img src="{{ asset('storage/barangs/' . $indicator->barang->gambar) }}"
                                             alt="{{ $indicator->barang->nama }}"
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $indicator->barang->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $indicator->barang->kode_barang }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Jumlah Parts -->
                            <td class="px-6 py-4">
                                @if ($indicator->barang->parts->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $indicator->barang->parts->count() }} Parts
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        No Parts
                                    </span>
                                @endif
                            </td>

                            <!-- Nama Bagian -->
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $indicator->nama_bagian }}
                            </td>

                            <!-- Total Standards -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $indicator->standards->count() }} Standards
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="viewDetails({{ $indicator->id }})"
                                            class="bg-yellow-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        Detail
                                    </button>

                                    <button onclick="openEditModal({{ $indicator->id }})"
                                            class="bg-orange-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        Edit
                                    </button>

                                    <button onclick="deleteCheckIndicator({{ $indicator->id }})"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <p class="mt-4 text-gray-600 font-semibold">No check indicators found</p>
                                    <p class="text-sm text-gray-500">Click "Add Check Indicator" to create one</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination Info -->
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

<!-- Include Modals -->
@include('check-indicators.create')
@include('check-indicators.edit')
@include('check-indicators.detail')

@endsection

@push('scripts')
<script>
    let allIndicators = [];
    let filteredIndicators = [];
    let currentPerPage = 20;

    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#checkIndicatorsTableBody tr');
        rows.forEach((row, index) => {
            if (!row.querySelector('td[colspan]')) {
                allIndicators.push({
                    element: row.cloneNode(true),
                    searchText: row.textContent.toLowerCase()
                });
            }
        });
        
        filteredIndicators = [...allIndicators];
        updateTable();
    });

    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        
        if (searchInput === '') {
            filteredIndicators = [...allIndicators];
        } else {
            filteredIndicators = allIndicators.filter(indicator => indicator.searchText.includes(searchInput));
        }
        
        updateTable();
    }

    function changePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        currentPerPage = perPage === 'all' ? filteredIndicators.length : parseInt(perPage);
        updateTable();
    }

    function updateTable() {
        const tbody = document.getElementById('checkIndicatorsTableBody');
        tbody.innerHTML = '';
        
        const totalEntries = filteredIndicators.length;
        const displayCount = currentPerPage > totalEntries ? totalEntries : currentPerPage;
        
        document.getElementById('showingFrom').textContent = totalEntries > 0 ? '1' : '0';
        document.getElementById('showingTo').textContent = displayCount;
        document.getElementById('totalEntries').textContent = totalEntries;
        
        if (totalEntries === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-600 mt-4 font-semibold">No results found</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        filteredIndicators.slice(0, displayCount).forEach((indicator, index) => {
            const row = indicator.element.cloneNode(true);
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                firstCell.textContent = index + 1;
            }
            tbody.appendChild(row);
        });
    }

    async function deleteCheckIndicator(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This check indicator will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/check-indicators/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete check indicator!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush