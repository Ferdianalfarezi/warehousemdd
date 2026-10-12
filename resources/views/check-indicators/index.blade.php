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
        <div class="flex items-center space-x-3">
            <button 
                onclick="openImportModal()"
                class="bg-white border-2 border-black text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span>Import Excel</span>
            </button>

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
                >
            </div>

            <!-- Per Page Selector -->
            <div class="flex-shrink-0">
                <select 
                    id="perPageSelect" 
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
        <div class="overflow-x-auto relative">
            <div id="loadingOverlay" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                <div class="flex items-center space-x-3">
                    <svg class="animate-spin h-8 w-8 text-black" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-600 font-medium">Loading...</span>
                </div>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Bagian</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Standards</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="checkIndicatorsTableBody">
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-4 text-gray-500">Loading data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="showingFrom" class="font-medium">0</span> to
                    <span id="showingTo" class="font-medium">0</span> of
                    <span id="totalEntries" class="font-medium">0</span> entries
                </div>
                <div id="paginationContainer" class="flex items-center space-x-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('check-indicators.create')
@include('check-indicators.edit')
@include('check-indicators.detail')
@include('check-indicators.import')

@endsection

@push('scripts')
<script>
// ============================================
// STATE
// ============================================
let currentPage   = 1;
let perPage       = 20;
let searchQuery   = '';
let totalPages    = 1;
let isLoading     = false;
let searchTimeout = null;

const BTN_PAGE          = 'px-3 py-1.5 rounded-lg border text-xs transition';
const BTN_PAGE_ACTIVE   = 'bg-black text-white border-black font-semibold';
const BTN_PAGE_NORMAL   = 'border-gray-300 text-gray-700 hover:bg-gray-50';
const BTN_PAGE_DISABLED = 'border-gray-200 text-gray-400 cursor-not-allowed';

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    loadIndicators();

    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = this.value;
            currentPage = 1;
            loadIndicators();
        }, 300);
    });

    document.getElementById('perPageSelect').addEventListener('change', function () {
        perPage     = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        loadIndicators();
    });
});

// ============================================
// LOAD DATA
// ============================================
async function loadIndicators() {
    if (isLoading) return;
    isLoading = true;
    showLoading(true);
    try {
        const params = new URLSearchParams({ page: currentPage, per_page: perPage, search: searchQuery });
        const res    = await fetch(`/check-indicators/data?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const result = await res.json();
        if (result.success) {
            renderTable(result.data);
            renderPagination(result.pagination);
            updateShowingInfo(result.pagination);
        } else {
            showEmpty('Gagal memuat data');
        }
    } catch (err) {
        showEmpty('Error memuat data. Coba refresh halaman.');
    } finally {
        isLoading = false;
        showLoading(false);
    }
}

// ============================================
// RENDER TABLE
// ============================================
function renderTable(rows) {
    const tbody = document.getElementById('checkIndicatorsTableBody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-16 text-center">
            <p class="mt-4 text-gray-600 font-semibold">${searchQuery ? 'No results found' : 'No check indicators found'}</p>
            <p class="text-sm text-gray-500">${searchQuery ? 'Coba kata kunci lain' : 'Click "Add Check Indicator" or "Import Excel" to create one'}</p>
        </td></tr>`;
        return;
    }
    tbody.innerHTML = rows.map(r => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-900">${r.row_number}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    ${imageCell(r)}
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${esc(r.nama_barang) || '-'}</p>
                        <p class="text-xs text-gray-500">${esc(r.kode_barang)}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${esc(r.nama_bagian)}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ${r.standards_count} Standards
                </span>
            </td>
            <td class="px-6 py-4">${actionButtons(r.id)}</td>
        </tr>
    `).join('');
}

function imageCell(r) {
    if (!r.gambar_url) {
        return `<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg></div>`;
    }
    return `<img src="${r.gambar_url}" alt="${esc(r.nama_barang)}" loading="lazy" class="w-12 h-12 rounded-lg object-cover border border-gray-200">`;
}

// Tombol ikon, style inline (sama seperti Request Repair / General Checkup)
function makeBtn(onclick, title, bgColor, bgHoverColor, svgPath) {
    return '<button type="button" onclick="' + onclick + '" title="' + title + '"'
         + ' style="background-color:' + bgColor + ';width:32px;height:32px;border-radius:8px;transition:background-color .15s;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:none;cursor:pointer;"'
         + ' onmouseover="this.style.backgroundColor=\'' + bgHoverColor + '\'"'
         + ' onmouseout="this.style.backgroundColor=\'' + bgColor + '\'">'
         + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">'
         + svgPath
         + '</svg></button>';
}

function actionButtons(id) {
    return '<div style="display:flex;align-items:center;justify-content:center;gap:4px;">'
        + makeBtn('viewDetails(' + id + ')', 'Detail', '#eab308', '#ca8a04',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>')
        + makeBtn('openEditModal(' + id + ')', 'Edit', '#f97316', '#ea580c',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>')
        + makeBtn('deleteCheckIndicator(' + id + ')', 'Hapus', '#ef4444', '#dc2626',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>')
        + '</div>';
}

// ============================================
// PAGINATION
// ============================================
function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    totalPages = pagination.total_pages;
    if (totalPages <= 1) { container.innerHTML = ''; return; }

    const pageBtn = (label, page, extraClass, disabled = false) =>
        `<button onclick="goToPage(${page})" ${disabled ? 'disabled' : ''} class="${BTN_PAGE} ${extraClass}">${label}</button>`;

    const max = 5;
    let start = Math.max(1, currentPage - Math.floor(max / 2));
    let end   = Math.min(totalPages, start + max - 1);
    if (end - start < max - 1) start = Math.max(1, end - max + 1);

    let html = pageBtn('&laquo;', currentPage - 1, currentPage === 1 ? BTN_PAGE_DISABLED : BTN_PAGE_NORMAL, currentPage === 1);
    if (start > 1) {
        html += pageBtn(1, 1, BTN_PAGE_NORMAL);
        if (start > 2) html += `<span class="px-1 text-gray-500 text-xs">...</span>`;
    }
    for (let i = start; i <= end; i++) {
        html += pageBtn(i, i, i === currentPage ? BTN_PAGE_ACTIVE : BTN_PAGE_NORMAL);
    }
    if (end < totalPages) {
        if (end < totalPages - 1) html += `<span class="px-1 text-gray-500 text-xs">...</span>`;
        html += pageBtn(totalPages, totalPages, BTN_PAGE_NORMAL);
    }
    html += pageBtn('&raquo;', currentPage + 1, currentPage === totalPages ? BTN_PAGE_DISABLED : BTN_PAGE_NORMAL, currentPage === totalPages);

    container.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    loadIndicators();
}

function updateShowingInfo(p) {
    document.getElementById('showingFrom').textContent  = p.from;
    document.getElementById('showingTo').textContent    = p.to;
    document.getElementById('totalEntries').textContent = p.total;
}

// ============================================
// HELPERS
// ============================================
function esc(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
function showLoading(show) { document.getElementById('loadingOverlay').classList.toggle('hidden', !show); }
function showEmpty(msg) {
    document.getElementById('checkIndicatorsTableBody').innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">${msg}</td></tr>`;
}

// ============================================
// BARANG OPTIONS (dropdown Create/Edit) — di-load sekali saat modal pertama dibuka,
// tidak lagi dirender di HTML (1700+ barang x 2 dropdown bikin halaman berat)
// ============================================
let barangOptionsPromise = null;

function ensureBarangOptions() {
    if (!barangOptionsPromise) {
        barangOptionsPromise = (async () => {
            const selects = ['createBarangId', 'editBarangId'].map(id => document.getElementById(id));
            selects.forEach(s => { s.disabled = true; s.options[0].text = 'Loading barang...'; });
            try {
                const res    = await fetch('/check-indicators/barang-options', { headers: { 'Accept': 'application/json' } });
                const result = await res.json();
                const html   = result.data.map(b => `<option value="${b.id}">${esc(b.kode_barang)} - ${esc(b.nama)}</option>`).join('');
                selects.forEach(s => s.insertAdjacentHTML('beforeend', html));
            } catch (e) {
                barangOptionsPromise = null; // biar bisa dicoba lagi saat modal dibuka ulang
                Swal.fire('Error!', 'Gagal memuat daftar barang', 'error');
            } finally {
                selects.forEach(s => { s.disabled = false; s.options[0].text = '-- Select Barang --'; });
            }
        })();
    }
    return barangOptionsPromise;
}

// ============================================
// DELETE
// ============================================
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

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/check-indicators/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await response.json();

        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadIndicators();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Failed to delete check indicator!', 'error');
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeViewModal();
        closeCreateModal();
        closeEditModal();
        closeImportModal();
    }
});
</script>
@endpush
