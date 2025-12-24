    @extends('layouts.app')

    @section('title', 'Parts')

    @section('content')
    <div class="space-y-6">
        
        <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Parts</h1>
                <p class="text-gray-600 mt-1">Manage warehouse parts and inventory</p>
            </div>
            <div class="flex space-x-3">
                <!-- Button Import Excel -->
                <button 
                    onclick="openImportModal()"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span>Import Excel</span>
                </button>
                
                <!-- Button Add Part -->
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add Part</span>
                </button>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Parts -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Parts</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $totalParts }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stock Aman -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Stock Aman</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $stockAman }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Hampir Habis -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Low Stock</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $hampirHabis }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Habis -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Stock Habis</p>
                        <p class="text-2xl font-bold text-red-900 mt-1">{{ $habis }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
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
                        placeholder="Search by kode, nama, supplier..."
                        onkeyup="searchTable()"
                    >
                </div>

                <!-- Per Page Selector -->
                <div class="flex-shrink-0">
                    <select 
                        id="perPageSelect" 
                        onchange="changePerPage()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                        style="line-height:1.5;"
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Part</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="partsTableBody">
                        @forelse($parts as $index => $part)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @if($part->gambar)
                                        <img src="{{ $part->image_path }}" 
                                            onclick="showImagePreview('{{ $part->image_path }}', '{{ $part->nama }}')"
                                            class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition"
                                            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23e5e7eb%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2212%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E';">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $part->kode_part }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-ms font-semibold text-gray-900">{{ $part->nama }}</p>
                                    
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold {{ $part->isBelowMinStock() ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $part->stock }} {{ $part->satuan }}
                                    </span>
                                    <p class="text-xs text-gray-500">Min: {{ $part->min_stock }} | Max: {{ $part->max_stock }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $part->getStatusBadgeClass() }}">
                                        {{ $part->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $part->supplier->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $part->address ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Existing buttons -->
                                        <button onclick="openEditModal({{ $part->id }})"
                                                class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                            Edit
                                        </button>
                                        <button onclick="deletePart({{ $part->id }})"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                        
                                        <!-- NEW: Request to Warehouse Button -->
                                        @if($part->id_pud)
                                            <button 
                                                onclick="openRequestModal({{ $part->id }})"
                                                class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 transition flex items-center"
                                                title="Request ke Warehouse"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Ajukan
                                            </button>
                                        @else
                                            <span 
                                                class="bg-gray-300 text-gray-500 px-3 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed inline-flex items-center"
                                                title="Part belum di-mapping ke warehouse (ID PUD belum diisi)"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                N/A
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="mt-4 text-gray-600 font-semibold">No parts found</p>
                                    <p class="text-gray-500 text-sm">Click "Add Part" to create one</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer: Showing Entries & Pagination -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Showing Info -->
                    <div class="text-sm text-gray-600">
                        Showing <span id="showingFrom" class="font-medium">1</span> to 
                        <span id="showingTo" class="font-medium">0</span> of 
                        <span id="totalEntries" class="font-medium">0</span> entries
                        <span id="filteredInfo" class="hidden">
                            (filtered from <span id="totalEntriesOriginal" class="font-medium">0</span> total entries)
                        </span>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationContainer" class="flex items-center space-x-2">
                        <!-- Pagination buttons akan di-generate oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Create Modal -->
    @include('parts.create')

    <!-- Include Edit Modal -->
    @include('parts.edit')

    <!-- Include Import Modal -->
    @include('parts.import')

    @include('parts.request')

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" onclick="closeImagePreview()">
        <div class="relative max-w-4xl max-h-[90vh] w-full" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button onclick="closeImagePreview()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <!-- Image Title -->
            <div class="text-white text-center mb-4">
                <h3 id="previewImageTitle" class="text-xl font-bold"></h3>
            </div>
            
            <!-- Image Container -->
            <div class="bg-white rounded-lg p-4 flex items-center justify-center">
                <img id="previewImageSrc" src="" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
            </div>
            
            <!-- Download Button -->
            <div class="text-center mt-4">
                <a id="downloadImageLink" href="" download class="inline-flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Download Image</span>
                </a>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script>
        // Store all parts data
        let allParts = [];
        let filteredParts = [];
        let currentPerPage = 20;
        let currentPage = 1;
        let totalPages = 1;

        // Initialize data on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get all parts data from table
            const rows = document.querySelectorAll('#partsTableBody tr');
            rows.forEach((row, index) => {
                if (!row.querySelector('td[colspan]')) { // Skip empty state row
                    allParts.push({
                        element: row.cloneNode(true),
                        searchText: row.textContent.toLowerCase()
                    });
                }
            });
            
            filteredParts = [...allParts];
            updateTable();
            
            // Initialize Select2 untuk supplier
            initializeSelect2();
        });

        // Initialize Select2 untuk supplier dropdown
        function initializeSelect2() {
            // Untuk create modal
            $('#createSupplierId').select2({
                placeholder: "Select Supplier",
                allowClear: false,
                width: '100%',
                dropdownParent: $('#createModal'),
                language: {
                    noResults: function() {
                        return "Supplier tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari supplier...";
                    }
                }
            });
            
            // Untuk edit modal
            $('#editSupplierId').select2({
                placeholder: "Select Supplier",
                allowClear: false,
                width: '100%',
                dropdownParent: $('#editModal'),
                language: {
                    noResults: function() {
                        return "Supplier tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari supplier...";
                    }
                }
            });
        }

        // Search function
        function searchTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            
            if (searchInput === '') {
                filteredParts = [...allParts];
                document.getElementById('filteredInfo').classList.add('hidden');
            } else {
                filteredParts = allParts.filter(part => part.searchText.includes(searchInput));
                document.getElementById('filteredInfo').classList.remove('hidden');
                document.getElementById('totalEntriesOriginal').textContent = allParts.length;
            }
            
            currentPage = 1; // Reset ke halaman 1 saat search
            updateTable();
        }

        // Change per page
        function changePerPage() {
            const perPage = document.getElementById('perPageSelect').value;
            currentPerPage = perPage === 'all' ? filteredParts.length : parseInt(perPage);
            currentPage = 1; // Reset ke halaman 1 saat ganti per page
            updateTable();
        }

        // Go to page
        function goToPage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            updateTable();
        }

        // Render pagination
        function renderPagination() {
            const container = document.getElementById('paginationContainer');
            
            // Hide pagination jika cuma 1 halaman atau show all
            if (totalPages <= 1 || currentPerPage >= filteredParts.length) {
                container.innerHTML = '';
                return;
            }

            let paginationHTML = '';

            // Previous Button
            paginationHTML += `
                <button 
                    onclick="goToPage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''}
                    class="px-2 py-1.5 rounded-lg border text-xs ${currentPage === 1 ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'} transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            `;

            // Page Numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Adjust start page jika endPage mentok
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // First page button
            if (startPage > 1) {
                paginationHTML += `
                    <button 
                        onclick="goToPage(1)" 
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs"
                    >
                        1
                    </button>
                `;
                if (startPage > 2) {
                    paginationHTML += `<span class="px-1 text-gray-500 text-xs">...</span>`;
                }
            }

            // Middle page buttons
            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <button 
                        onclick="goToPage(${i})" 
                        class="px-3 py-1.5 rounded-lg border transition text-xs ${i === currentPage ? 'bg-black text-white border-black font-semibold' : 'border-gray-300 text-gray-700 hover:bg-gray-50'}"
                    >
                        ${i}
                    </button>
                `;
            }

            // Last page button
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHTML += `<span class="px-1 text-gray-500 text-xs">...</span>`;
                }
                paginationHTML += `
                    <button 
                        onclick="goToPage(${totalPages})" 
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs"
                    >
                        ${totalPages}
                    </button>
                `;
            }

            // Next Button
            paginationHTML += `
                <button 
                    onclick="goToPage(${currentPage + 1})" 
                    ${currentPage === totalPages ? 'disabled' : ''}
                    class="px-2 py-1.5 rounded-lg border text-xs ${currentPage === totalPages ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'} transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            `;

            container.innerHTML = paginationHTML;
        }

        // Update table display
        function updateTable() {
            const tbody = document.getElementById('partsTableBody');
            const totalEntries = filteredParts.length;
            
            // Calculate pagination
            if (currentPerPage >= totalEntries) {
                totalPages = 1;
                currentPage = 1;
            } else {
                totalPages = Math.ceil(totalEntries / currentPerPage);
                // Pastikan currentPage tidak melebihi totalPages
                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }
            }

            const startIndex = (currentPage - 1) * currentPerPage;
            const endIndex = Math.min(startIndex + currentPerPage, totalEntries);
            
            // Update info
            document.getElementById('showingFrom').textContent = totalEntries > 0 ? startIndex + 1 : '0';
            document.getElementById('showingTo').textContent = endIndex;
            document.getElementById('totalEntries').textContent = totalEntries;
            
            tbody.innerHTML = '';
            
            if (totalEntries === 0) {
                // Show empty state
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <p class="text-gray-600 mt-4 font-semibold">No results found</p>
                            <p class="text-gray-500 text-sm mt-1">Try adjusting your search</p>
                        </td>
                    </tr>
                `;
                renderPagination();
                return;
            }
            
            // Display parts untuk halaman saat ini
            filteredParts.slice(startIndex, endIndex).forEach((part, index) => {
                const row = part.element.cloneNode(true);
                // Update row number
                const firstCell = row.querySelector('td:first-child');
                if (firstCell) {
                    firstCell.textContent = startIndex + index + 1;
                }
                tbody.appendChild(row);
            });

            // Render pagination
            renderPagination();
        }

        // Show Image Preview Modal with fade animation
        function showImagePreview(imageSrc, imageTitle) {
            const modal = document.getElementById('imagePreviewModal');
            document.getElementById('previewImageSrc').src = imageSrc;
            document.getElementById('previewImageTitle').textContent = imageTitle;
            document.getElementById('downloadImageLink').href = imageSrc;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger fade in animation
            setTimeout(() => {
                modal.classList.add('modal-fade-in');
            }, 10);
        }

        // Close Image Preview Modal with fade animation
        function closeImagePreview() {
            const modal = document.getElementById('imagePreviewModal');
            modal.classList.remove('modal-fade-in');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Preview for CREATE modal
        function previewImage(event, previewId) {
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewId + 'Container');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // Preview for EDIT modal (separate function)
        function previewEditImage(event) {
            const preview = document.getElementById('editPreview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('noImageText').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        function openCreateModal() {
            const modal = document.getElementById('createModal');
            document.getElementById('createForm').reset();
            document.getElementById('createPreviewContainer').classList.add('hidden');
            clearErrors();
            
            // Reset Select2
            $('#createSupplierId').val('').trigger('change');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger fade in animation
            setTimeout(() => {
                modal.classList.add('modal-fade-in');
            }, 10);
        }

       async function openEditModal(id) {
        try {
            const response = await fetch(`/parts/${id}`);
            const res = await response.json();

            if (!res.success) throw new Error('API error');

            const part = res.data;

            document.getElementById('editPartId').value = part.id;
            document.getElementById('editKodePart').value = part.kode_part;
            document.getElementById('editNama').value = part.nama;
            document.getElementById('editStock').value = part.stock;
            document.getElementById('editMinStock').value = part.min_stock;
            document.getElementById('editMaxStock').value = part.max_stock;
            document.getElementById('editSatuan').value = part.satuan;
            document.getElementById('editAddress').value = part.address ?? '';

            const editLine = document.getElementById('editLine');
            if (editLine) editLine.value = part.line ?? '';

            const editIdPud = document.getElementById('editIdPud');
            if (editIdPud) editIdPud.value = part.id_pud ?? '';

            if (part.supplier_id) {
                $('#editSupplierId').val(part.supplier_id).trigger('change');
            } else {
                $('#editSupplierId').val(null).trigger('change');
            }

            const previewImg = document.getElementById('editPreview');
            const noImageText = document.getElementById('noImageText');

            if (part.gambar) {
                previewImg.src = part.image_path ?? `/storage/parts/${part.gambar}`;
                previewImg.style.display = 'block';
                noImageText.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                noImageText.style.display = 'block';
                noImageText.textContent = 'No image uploaded';
            }

            clearErrors();

            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                modal.classList.add('modal-fade-in');
            }, 10);

        } catch (error) {
            console.error(error);
            Swal.fire('Error!', 'Failed to load part data', 'error');
        }
    }


        function closeCreateModal() {
            const modal = document.getElementById('createModal');
            modal.classList.remove('modal-fade-in');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('createForm').reset();
                document.getElementById('createPreviewContainer').classList.add('hidden');
                clearErrors();
                
                // Reset Select2
                $('#createSupplierId').val('').trigger('change');
            }, 300);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.remove('modal-fade-in');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('editForm').reset();
                clearErrors();
                
                // Reset Select2
                $('#editSupplierId').val('').trigger('change');
            }, 300);
        }

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        }

        // Submit Create Form
        document.getElementById('createForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const formData = new FormData(this);

            try {
                const response = await fetch('/parts', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`error-create-${key}`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong!', 'error');
            }
        });

        // Submit Edit Form
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const formData = new FormData(this);
            const id = document.getElementById('editPartId').value;
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/parts/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`error-edit-${key}`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong!', 'error');
            }
        });

        async function deletePart(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "This part will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/parts/${id}`, {
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
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete part!', 'error');
                }
            }
        }

        async function openRequestModal(partId) {
        try {
            // Fetch part data
            const response = await fetch(`/parts/${partId}`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error('Failed to fetch part data');
            }
            
            const part = data.data;
            
            // Validate id_pud mapping
            if (!part.id_pud) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Request',
                    text: 'Part ini belum di-mapping ke warehouse system (ID PUD belum diisi). Hubungi administrator.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            // Populate modal with part data
            document.getElementById('requestPartId').value = part.id;
            document.getElementById('requestPartCode').textContent = part.kode_part;
            document.getElementById('requestPartName').textContent = part.nama;
            document.getElementById('requestPartStock').textContent = part.stock;
            document.getElementById('requestPartUnit').textContent = part.satuan;
            document.getElementById('requestPartIdPud').textContent = part.id_pud || 'N/A';
            document.getElementById('requestUnitDisplay').textContent = part.satuan;
            
            // Set image
            const imgElement = document.getElementById('requestPartImage');
            if (part.image_path) {
                imgElement.src = part.image_path;
            } else {
                imgElement.src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22%3E%3Crect width=%2280%22 height=%2280%22 fill=%22%23dbeafe%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2214%22 fill=%22%233b82f6%22%3ENo Image%3C/text%3E%3C/svg%3E';
            }
            
            // Reset form
            document.getElementById('requestForm').reset();
            document.getElementById('requestPartId').value = part.id; // Restore after reset
            document.getElementById('keteranganCount').textContent = '0';
            clearRequestErrors();
            
            // Show modal
            const modal = document.getElementById('requestModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('modal-fade-in');
            }, 10);
            
            // Focus on quantity input
            setTimeout(() => {
                document.getElementById('requestQuantity').focus();
            }, 300);
            
        } catch (error) {
            console.error('Error opening request modal:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data part',
                confirmButtonColor: '#3b82f6'
            });
        }
    }
    
    /**
     * Close request modal
     */
    function closeRequestModal() {
        const modal = document.getElementById('requestModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('requestForm').reset();
            clearRequestErrors();
        }, 300);
    }
    
    /**
     * Clear request form errors
     */
    function clearRequestErrors() {
        document.querySelectorAll('#requestForm .error-message').forEach(el => el.textContent = '');
    }
    
    /**
     * Character counter for keterangan
     */
    document.getElementById('requestKeterangan')?.addEventListener('input', function() {
        document.getElementById('keteranganCount').textContent = this.value.length;
    });
    
    /**
     * Submit Request Form
     */
    document.getElementById('requestForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearRequestErrors();
        
        const submitBtn = document.getElementById('submitRequestBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const partId = document.getElementById('requestPartId').value;
        
        // Disable button & show loading
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Mengirim...';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(`/parts/${partId}/request-warehouse`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                closeRequestModal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK'
                });
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-request-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                } else {
                    // Show general error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengirim request',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonColor: '#3b82f6'
            });
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Ajukan Request';
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
                closeImagePreview();
                closeImportModal();
                closeRequestModal(); 
            }
        });
    </script>
    @endpush