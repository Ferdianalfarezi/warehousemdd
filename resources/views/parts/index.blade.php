{{-- resources/views/parts/index.blade.php - Server-Side Pagination Version --}}

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
                <!-- Button Request Part -->
                <button 
                    onclick="openRequestPartModal()"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Request Part</span>
                </button>

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
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Total Parts -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Parts</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1" id="statTotal">{{ $totalParts }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stock Aman -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition" data-filter-condition="normal">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Stock Aman</p>
                        <p class="text-2xl font-bold text-green-900 mt-1" id="statAman">{{ $stockAman }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Hampir Habis -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition" data-filter-condition="low">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Low Stock</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1" id="statLow">{{ $hampirHabis }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Habis -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition" data-filter-condition="out">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Stock Habis</p>
                        <p class="text-2xl font-bold text-red-900 mt-1" id="statHabis">{{ $habis }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- On Request -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition" data-filter-condition="on_request">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">On Request</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1" id="statOnRequest">{{ $onRequest ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Actions Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between md:space-x-4 space-y-3 md:space-y-0">

            <!-- Left Side: Search & Per Page -->
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0 flex-1">
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
                    >
                </div>

                <!-- Per Page Selector -->
                <div class="flex-shrink-0">
                    <select 
                        id="perPageSelect" 
                        class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                        style="line-height:1.5;"
                    >
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>

                <!-- Filter Stock Condition -->
                <div class="flex-shrink-0">
                    <select 
                        id="stockConditionFilter" 
                        class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="">Semua Kondisi &nbsp;&nbsp;</option>
                        <option value="normal">Stock Normal</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Stock Habis</option>
                        <option value="on_request">On Request</option>
                    </select>
                </div>
            </div>

            <!-- Right Side: Bulk Request Button (Hidden by default) -->
            <div id="bulkRequestContainer" class="hidden">
                <button 
                    onclick="openBulkRequestModal()"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span id="bulkRequestBtnText">Ajukan Request (<span id="selectedCount">0</span>)</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-8 w-8 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600 font-medium">Loading...</span>
            </div>
        </div>

        <div class="overflow-x-auto relative">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input 
                                type="checkbox" 
                                id="selectAll"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Part</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th style="width:150px" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="partsTableBody">
                    <!-- Data akan di-load via AJAX -->
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-4 text-gray-600">Loading data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer: Showing Entries & Pagination -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Showing Info -->
                <div class="text-sm text-gray-600">
                    Showing <span id="showingFrom" class="font-medium">0</span> to 
                    <span id="showingTo" class="font-medium">0</span> of 
                    <span id="totalEntries" class="font-medium">0</span> entries
                </div>

                <!-- Pagination -->
                <div id="paginationContainer" class="flex items-center space-x-2">
                    <!-- Pagination buttons akan di-generate oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('parts.create')
@include('parts.edit')
@include('parts.import')
@include('parts.bulk-request')
@include('parts.request-part')

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" onclick="closeImagePreview()">
    <div class="relative max-w-4xl max-h-[90vh] w-full" onclick="event.stopPropagation()">
        <button onclick="closeImagePreview()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <div class="text-white text-center mb-4">
            <h3 id="previewImageTitle" class="text-xl font-bold"></h3>
        </div>
        
        <div class="bg-white rounded-lg p-4 flex items-center justify-center">
            <img id="previewImageSrc" src="" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
        </div>
        
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
    // ============================================
    // State Management
    // ============================================
    let currentPage = 1;
    let perPage = 20;
    let searchQuery = '';
    let stockConditionFilter = '';
    let totalPages = 1;
    let isLoading = false;
    let searchTimeout = null;
    let selectedParts = new Map();

    // ============================================
    // Initialize
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initializeSelect2();
        loadParts();
        bindEvents();
    });

    // ============================================
    // Event Bindings
    // ============================================
    function bindEvents() {
        // Search dengan debounce
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchQuery = this.value;
                currentPage = 1;
                loadParts();
            }, 300);
        });

        // Per page change
        document.getElementById('perPageSelect').addEventListener('change', function() {
            perPage = this.value === 'all' ? 'all' : parseInt(this.value);
            currentPage = 1;
            loadParts();
        });

        // Stock condition filter
        document.getElementById('stockConditionFilter').addEventListener('change', function() {
            stockConditionFilter = this.value;
            currentPage = 1;
            loadParts();
        });

        // Clickable stats badges
        document.querySelectorAll('[data-filter-condition]').forEach(badge => {
            badge.addEventListener('click', function() {
                const condition = this.dataset.filterCondition;
                
                // Toggle: jika sudah aktif, reset filter
                if (stockConditionFilter === condition) {
                    stockConditionFilter = '';
                    document.getElementById('stockConditionFilter').value = '';
                } else {
                    stockConditionFilter = condition;
                    document.getElementById('stockConditionFilter').value = condition;
                }
                
                // Update active state visual
                updateActiveBadge();
                
                currentPage = 1;
                loadParts();
            });
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', toggleSelectAll);
    }

    // ============================================
    // Update Active Badge Visual
    // ============================================
    function updateActiveBadge() {
        document.querySelectorAll('[data-filter-condition]').forEach(badge => {
            const condition = badge.dataset.filterCondition;
            if (condition === stockConditionFilter) {
                badge.classList.add('ring-2', 'ring-offset-2', 'ring-gray-900');
            } else {
                badge.classList.remove('ring-2', 'ring-offset-2', 'ring-gray-900');
            }
        });
    }

    // ============================================
    // Load Parts Data (AJAX)
    // ============================================
    async function loadParts() {
        if (isLoading) return;
        
        isLoading = true;
        showLoading(true);

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: perPage,
                search: searchQuery,
                stock_condition: stockConditionFilter
            });

            const response = await fetch(`/parts/data?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                renderTable(result.data);
                renderPagination(result.pagination);
                updateShowingInfo(result.pagination);
                updateActiveBadge();
            } else {
                showEmptyState('Failed to load data');
            }
        } catch (error) {
            console.error('Error loading parts:', error);
            showEmptyState('Error loading data. Please try again.');
        } finally {
            isLoading = false;
            showLoading(false);
        }
    }

    // ============================================
    // Render Table
    // ============================================
    function renderTable(parts) {
        const tbody = document.getElementById('partsTableBody');
        
        if (!parts || parts.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-4 text-gray-600 font-semibold">No parts found</p>
                        <p class="text-gray-500 text-sm">${searchQuery || stockConditionFilter ? 'Try different filter or search terms' : 'Click "Add Part" to create one'}</p>
                        ${stockConditionFilter ? `<button onclick="clearFilters()" class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">Clear Filters</button>` : ''}
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = parts.map(part => `
            <tr class="hover:bg-gray-50 transition" data-part-id="${part.id}">
                <td class="px-6 py-4">
                    ${part.id_pud && part.status !== 'on_request' ? `
                        <input 
                            type="checkbox" 
                            class="part-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            data-part-id="${part.id}"
                            data-part-code="${escapeHtml(part.kode_part)}"
                            data-part-name="${escapeHtml(part.nama)}"
                            data-part-unit="${escapeHtml(part.satuan)}"
                            ${selectedParts.has(String(part.id)) ? 'checked' : ''}
                            onchange="updateBulkRequestButton()"
                        >
                    ` : '<span class="text-gray-300">—</span>'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${part.row_number}</td>
                <td class="px-6 py-4">
                    ${part.gambar ? `
                        <img src="${part.image_path}" 
                            onclick="showImagePreview('${part.image_path}', '${escapeHtml(part.nama)}')"
                            class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition"
                            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23e5e7eb%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2212%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E';">
                    ` : `
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    `}
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">${escapeHtml(part.kode_part)}</td>
                <td class="px-6 py-4">
                    <p class="text-ms font-semibold text-gray-900">${escapeHtml(part.nama)}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm font-bold ${part.is_below_min ? 'text-red-600' : 'text-green-600'}">
                        ${part.stock} ${escapeHtml(part.satuan)}
                    </span>
                    <p class="text-xs text-gray-500">Min: ${part.min_stock} | Max: ${part.max_stock}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${part.status_badge_class}">
                        ${part.status_label}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(part.supplier_nama)}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(part.address)}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center space-x-2">
                        <button onclick="openEditModal(${part.id})"
                                class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                            Edit
                        </button>
                        <button onclick="deletePart(${part.id})"
                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // ============================================
    // Clear Filters
    // ============================================
    function clearFilters() {
        searchQuery = '';
        stockConditionFilter = '';
        document.getElementById('searchInput').value = '';
        document.getElementById('stockConditionFilter').value = '';
        currentPage = 1;
        updateActiveBadge();
        loadParts();
    }

    // ============================================
    // Pagination
    // ============================================
    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        totalPages = pagination.total_pages;

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous Button
        html += `
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

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            html += `<button onclick="goToPage(1)" class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs">1</button>`;
            if (startPage > 2) {
                html += `<span class="px-1 text-gray-500 text-xs">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <button 
                    onclick="goToPage(${i})" 
                    class="px-3 py-1.5 rounded-lg border transition text-xs ${i === currentPage ? 'bg-black text-white border-black font-semibold' : 'border-gray-300 text-gray-700 hover:bg-gray-50'}"
                >
                    ${i}
                </button>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span class="px-1 text-gray-500 text-xs">...</span>`;
            }
            html += `<button onclick="goToPage(${totalPages})" class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs">${totalPages}</button>`;
        }

        // Next Button
        html += `
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

        container.innerHTML = html;
    }

    function goToPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) return;
        currentPage = page;
        loadParts();
    }

    function updateShowingInfo(pagination) {
        document.getElementById('showingFrom').textContent = pagination.from;
        document.getElementById('showingTo').textContent = pagination.to;
        document.getElementById('totalEntries').textContent = pagination.total;
    }

    // ============================================
    // Helper Functions
    // ============================================
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
        }
    }

    function showEmptyState(message) {
        const tbody = document.getElementById('partsTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="px-6 py-16 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-4 text-gray-600 font-semibold">${message}</p>
                </td>
            </tr>
        `;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ============================================
    // Select2 Initialization
    // ============================================
    function initializeSelect2() {
        $('#createSupplierId').select2({
            placeholder: "Select Supplier",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#createModal'),
            language: {
                noResults: () => "Supplier tidak ditemukan",
                searching: () => "Mencari supplier..."
            }
        });
        
        $('#editSupplierId').select2({
            placeholder: "Select Supplier",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#editModal'),
            language: {
                noResults: () => "Supplier tidak ditemukan",
                searching: () => "Mencari supplier..."
            }
        });
    }

    // ============================================
    // Bulk Request Functions
    // ============================================
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.part-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
            
            if (selectAll.checked) {
                selectedParts.set(checkbox.dataset.partId, {
                    id: checkbox.dataset.partId,
                    code: checkbox.dataset.partCode,
                    name: checkbox.dataset.partName,
                    unit: checkbox.dataset.partUnit
                });
            }
        });
        
        if (!selectAll.checked) {
            selectedParts.clear();
        }
        
        updateBulkRequestButton();
    }

    function updateBulkRequestButton() {
        const container = document.getElementById('bulkRequestContainer');
        const countSpan = document.getElementById('selectedCount');
        const checkboxes = document.querySelectorAll('.part-checkbox:checked');
        
        selectedParts.clear();
        checkboxes.forEach(checkbox => {
            selectedParts.set(checkbox.dataset.partId, {
                id: checkbox.dataset.partId,
                code: checkbox.dataset.partCode,
                name: checkbox.dataset.partName,
                unit: checkbox.dataset.partUnit
            });
        });
        
        const count = selectedParts.size;
        
        if (count > 0) {
            container.classList.remove('hidden');
            countSpan.textContent = count;
        } else {
            container.classList.add('hidden');
            document.getElementById('selectAll').checked = false;
        }
    }

    function openBulkRequestModal() {
        if (selectedParts.size === 0) {
            Swal.fire('Info', 'Pilih minimal 1 part untuk request', 'info');
            return;
        }

        const modal = document.getElementById('bulkRequestModal');
        const tbody = document.getElementById('bulkRequestItemsBody');
        
        tbody.innerHTML = '';
        
        let index = 1;
        selectedParts.forEach(part => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">${index++}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${escapeHtml(part.code)}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(part.name)}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <input 
                                type="number" 
                                class="quantity-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                data-part-id="${part.id}"
                                min="1"
                                value="1"
                                required
                            >
                            <span class="text-sm text-gray-500">${escapeHtml(part.unit)}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button 
                            type="button"
                            onclick="removeFromBulkRequest('${part.id}')"
                            class="text-red-600 hover:text-red-800 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
        
        document.getElementById('bulkCatatan').value = '';
        document.getElementById('catatanCount').textContent = '0';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.add('modal-fade-in');
        }, 10);
    }

    function removeFromBulkRequest(partId) {
        selectedParts.delete(partId);
        
        const checkbox = document.querySelector(`.part-checkbox[data-part-id="${partId}"]`);
        if (checkbox) {
            checkbox.checked = false;
        }
        
        const row = document.querySelector(`#bulkRequestItemsBody input[data-part-id="${partId}"]`)?.closest('tr');
        if (row) {
            row.remove();
        }
        
        updateBulkRequestButton();
        
        if (selectedParts.size === 0) {
            closeBulkRequestModal();
        }
    }

    function closeBulkRequestModal() {
        const modal = document.getElementById('bulkRequestModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Character counter for catatan
    document.getElementById('bulkCatatan')?.addEventListener('input', function() {
        document.getElementById('catatanCount').textContent = this.value.length;
    });

    // Submit bulk request
    document.getElementById('bulkRequestForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBulkRequestBtn');
        const submitBtnText = document.getElementById('submitBulkBtnText');
        
        const partIds = [];
        const quantities = [];
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            const partId = input.dataset.partId;
            const quantity = parseInt(input.value);
            
            if (quantity > 0) {
                partIds.push(partId);
                quantities.push(quantity);
            }
        });
        
        if (partIds.length === 0) {
            Swal.fire('Error', 'Masukkan quantity untuk setiap item', 'error');
            return;
        }
        
        const catatan = document.getElementById('bulkCatatan').value;
        
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Mengirim...';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        try {
            const response = await fetch('/parts/bulk-request', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    part_ids: partIds,
                    quantities: quantities,
                    catatan: catatan
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                closeBulkRequestModal();
                
                selectedParts.clear();
                document.querySelectorAll('.part-checkbox').forEach(cb => cb.checked = false);
                document.getElementById('selectAll').checked = false;
                updateBulkRequestButton();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: `${data.message}<br><small class="text-gray-600">Request Number: ${data.data.request_number}</small>`,
                    confirmButtonColor: '#3b82f6',
                }).then(() => {
                    loadParts();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#3b82f6'
                });
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
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Kirim Request';
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });

    // ============================================
    // Image Preview
    // ============================================
    function showImagePreview(imageSrc, imageTitle) {
        const modal = document.getElementById('imagePreviewModal');
        document.getElementById('previewImageSrc').src = imageSrc;
        document.getElementById('previewImageTitle').textContent = imageTitle;
        document.getElementById('downloadImageLink').href = imageSrc;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.add('modal-fade-in');
        }, 10);
    }

    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // ============================================
    // Image Preview for Forms
    // ============================================
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

    // ============================================
    // Modal Functions
    // ============================================
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        document.getElementById('createForm').reset();
        document.getElementById('createPreviewContainer').classList.add('hidden');
        clearErrors();
        
        $('#createSupplierId').val('').trigger('change');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
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
            
            $('#editSupplierId').val('').trigger('change');
        }, 300);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // ============================================
    // Form Submissions
    // ============================================
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
                    closeCreateModal();
                    loadParts();
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
                    closeEditModal();
                    loadParts();
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
                        loadParts();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete part!', 'error');
            }
        }
    }

    // ============================================
    // Keyboard Shortcuts
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeImagePreview();
            closeImportModal();
            closeBulkRequestModal();
            closeRequestPartModal();
        }
    });

    // ============================================
    // Request Part Modal (Manual Add)
    // ============================================
    let requestPartItems = new Map();
    let partSearchTimeout = null;

    function openRequestPartModal() {
        const modal = document.getElementById('requestPartModal');
        requestPartItems.clear();
        renderRequestPartItems();
        document.getElementById('requestPartCatatan').value = '';
        document.getElementById('requestCatatanCount').textContent = '0';
        document.getElementById('partSearchInput').value = '';
        document.getElementById('partSearchResults').innerHTML = '';
        document.getElementById('partSearchResults').classList.add('hidden');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.add('modal-fade-in');
            document.getElementById('partSearchInput').focus();
        }, 10);
    }

    function closeRequestPartModal() {
        const modal = document.getElementById('requestPartModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Search parts for adding
    document.getElementById('partSearchInput')?.addEventListener('input', function() {
        clearTimeout(partSearchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            document.getElementById('partSearchResults').classList.add('hidden');
            return;
        }
        
        partSearchTimeout = setTimeout(() => searchPartsForRequest(query), 300);
    });

    async function searchPartsForRequest(query) {
        const resultsContainer = document.getElementById('partSearchResults');
        
        try {
            resultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Mencari...</div>';
            resultsContainer.classList.remove('hidden');
            
            const response = await fetch(`/parts/data?search=${encodeURIComponent(query)}&per_page=10`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                const availableParts = result.data.filter(part => 
                    part.id_pud && !requestPartItems.has(String(part.id))
                );
                
                if (availableParts.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada part yang tersedia untuk di-request</div>';
                    return;
                }
                
                resultsContainer.innerHTML = availableParts.map(part => `
                    <div 
                        class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition"
                        onclick="addPartToRequest(${part.id}, '${escapeHtml(part.kode_part)}', '${escapeHtml(part.nama)}', '${escapeHtml(part.satuan)}', ${part.stock})"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-gray-900">${escapeHtml(part.kode_part)}</span>
                                <span class="text-gray-500 mx-2">-</span>
                                <span class="text-gray-700">${escapeHtml(part.nama)}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm ${part.is_below_min ? 'text-red-600' : 'text-green-600'} font-medium">
                                    Stock: ${part.stock} ${escapeHtml(part.satuan)}
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                resultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Part tidak ditemukan</div>';
            }
        } catch (error) {
            console.error('Error searching parts:', error);
            resultsContainer.innerHTML = '<div class="p-4 text-center text-red-500">Error mencari part</div>';
        }
    }

    function addPartToRequest(id, code, name, unit, stock) {
        if (requestPartItems.has(String(id))) {
            Swal.fire('Info', 'Part sudah ada dalam daftar', 'info');
            return;
        }
        
        requestPartItems.set(String(id), {
            id: id,
            code: code,
            name: name,
            unit: unit,
            stock: stock,
            quantity: 1
        });
        
        renderRequestPartItems();
        
        document.getElementById('partSearchInput').value = '';
        document.getElementById('partSearchResults').classList.add('hidden');
        document.getElementById('partSearchInput').focus();
    }

    function removePartFromRequest(id) {
        requestPartItems.delete(String(id));
        renderRequestPartItems();
    }

    function updateRequestQuantity(id, quantity) {
        const item = requestPartItems.get(String(id));
        if (item) {
            item.quantity = Math.max(1, parseInt(quantity) || 1);
            requestPartItems.set(String(id), item);
        }
    }

    function renderRequestPartItems() {
        const tbody = document.getElementById('requestPartItemsBody');
        const emptyState = document.getElementById('requestPartEmptyState');
        const submitBtn = document.getElementById('submitRequestPartBtn');
        
        if (requestPartItems.size === 0) {
            tbody.innerHTML = '';
            emptyState.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }
        
        emptyState.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        
        let index = 1;
        let html = '';
        
        requestPartItems.forEach(part => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">${index++}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${escapeHtml(part.code)}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(part.name)}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${part.stock} ${escapeHtml(part.unit)}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <input 
                                type="number" 
                                class="request-quantity-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                data-part-id="${part.id}"
                                min="1"
                                value="${part.quantity}"
                                onchange="updateRequestQuantity('${part.id}', this.value)"
                            >
                            <span class="text-sm text-gray-500">${escapeHtml(part.unit)}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button 
                            type="button"
                            onclick="removePartFromRequest('${part.id}')"
                            class="text-red-600 hover:text-red-800 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    // Character counter for request catatan
    document.getElementById('requestPartCatatan')?.addEventListener('input', function() {
        document.getElementById('requestCatatanCount').textContent = this.value.length;
    });

    // Submit request part form
    document.getElementById('requestPartForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (requestPartItems.size === 0) {
            Swal.fire('Error', 'Tambahkan minimal 1 part untuk request', 'error');
            return;
        }
        
        const submitBtn = document.getElementById('submitRequestPartBtn');
        const submitBtnText = document.getElementById('submitRequestBtnText');
        
        const partIds = [];
        const quantities = [];
        
        requestPartItems.forEach(item => {
            partIds.push(item.id);
            quantities.push(item.quantity);
        });
        
        const catatan = document.getElementById('requestPartCatatan').value;
        
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Mengirim...';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        try {
            const response = await fetch('/parts/bulk-request', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    part_ids: partIds,
                    quantities: quantities,
                    catatan: catatan
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                closeRequestPartModal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: `${data.message}<br><small class="text-gray-600">Request Number: ${data.data.request_number}</small>`,
                    confirmButtonColor: '#3b82f6',
                }).then(() => {
                    loadParts();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#3b82f6'
                });
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
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Kirim Request';
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        const searchContainer = document.getElementById('partSearchContainer');
        if (searchContainer && !searchContainer.contains(e.target)) {
            document.getElementById('partSearchResults')?.classList.add('hidden');
        }
    });
</script>
@endpush