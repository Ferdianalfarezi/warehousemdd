@extends('layouts.app')

@section('title', 'Barangs')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dies</h1>
            <p class="text-gray-600 mt-1">Manage All Dies Data</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openImportModal()"
                class="bg-green-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>Import Excel</span>
            </button>
            <button onclick="openCreateModal()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Barang</span>
            </button>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0">
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Search kode, nama, supplier, cust, line, mesin...">
            </div>
            <div class="flex-shrink-0">
                <select id="perPageSelect"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">Image</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cust</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Model</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dies</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Line</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="barangsTableBody">
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
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

{{-- IMPORT MODAL --}}
<div id="importModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>Import dari Excel</span>
            </h2>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-700 space-y-1">
                <p class="font-semibold text-blue-800 mb-2">Format kolom Excel yang didukung:</p>
                <p>• <strong>DELIVERY PART CODE</strong> → kode_barang</p>
                <p>• <strong>CHILD PART CODE</strong> → child_part_code (detail)</p>
                <p>• <strong>PART NAME</strong> → nama / part_name</p>
                <p>• <strong>CUSTOMER</strong> → cust</p>
                <p>• <strong>MODEL</strong> → model</p>
                <p>• <strong>PROSES NAME</strong> → process_name</p>
                <p>• <strong>PROSES NO</strong> → process_no</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-xs text-yellow-800">
                ⚠️ Kode yang sudah ada akan di-update details-nya. Kode baru akan dibuat otomatis (supplier & line perlu diisi manual).
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File Excel (.xlsx, .xls)</label>
                <div id="dropZone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-green-400 hover:bg-green-50 transition">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-gray-600" id="dropZoneText">Klik atau drag & drop file Excel di sini</p>
                    <input type="file" id="excelFileInput" accept=".xlsx,.xls" class="hidden" onchange="handleFileSelect(event)">
                </div>
                <div id="selectedFileInfo" class="mt-2 hidden">
                    <div class="flex items-center space-x-2 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-green-700 font-medium" id="selectedFileName"></span>
                        <button type="button" onclick="clearFileSelection()" class="ml-auto text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="importProgress" class="hidden">
                <div class="flex items-center space-x-3">
                    <svg class="animate-spin h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Sedang mengimport data...</span>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end space-x-3">
            <button type="button" onclick="closeImportModal()"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="submitImport()" id="importSubmitBtn"
                class="px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                Import Sekarang
            </button>
        </div>
    </div>
</div>

{{-- Image Preview Modal --}}
<div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-75" onclick="closeImagePreview()">
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
            <a id="downloadImageLink" href="" download
                class="inline-flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Image</span>
            </a>
        </div>
    </div>
</div>

@include('barangs.create')
@include('barangs.edit')
@include('barangs.detail')

@endsection

@push('scripts')
<script>
// ============================================
// SHARED CONSTANTS (icons & repeated class strings)
// ============================================
const ICON = {
    trash: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
};
const BTN_PAGE          = 'px-3 py-1.5 rounded-lg border text-xs transition';
const BTN_PAGE_ACTIVE   = 'bg-black text-white border-black font-semibold';
const BTN_PAGE_NORMAL   = 'border-gray-300 text-gray-700 hover:bg-gray-50';
const BTN_PAGE_DISABLED = 'border-gray-200 text-gray-400 cursor-not-allowed';
const INPUT_XS = 'w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-black';

function waitForJQuery(callback, maxWait = 5000) {
    const start = Date.now();
    const check = () => {
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) callback();
        else if (Date.now() - start < maxWait) setTimeout(check, 50);
        else callback();
    };
    check();
}

let currentPage   = 1;
let perPage       = 20;
let searchQuery   = '';
let totalPages    = 1;
let isLoading     = false;
let searchTimeout = null;
let partCounter   = 0;
let partsData = {!! json_encode($parts->map(fn($p) => [
    'id'   => $p->id,
    'nama' => $p->nama,
    'kode' => $p->kode_part,
])->values()) !!};

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    loadBarangs();
    bindEvents();

    const dz = document.getElementById('dropZone');
    dz.addEventListener('click', () => document.getElementById('excelFileInput').click());
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-green-400', 'bg-green-50'); });
    dz.addEventListener('dragleave', () => dz.classList.remove('border-green-400', 'bg-green-50'));
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('border-green-400', 'bg-green-50');
        if (e.dataTransfer.files[0]) handleFileDrop(e.dataTransfer.files[0]);
    });
});

function bindEvents() {
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = this.value;
            currentPage = 1;
            loadBarangs();
        }, 300);
    });
    document.getElementById('perPageSelect').addEventListener('change', function () {
        perPage     = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        loadBarangs();
    });
}

// ============================================
// LOAD DATA
// ============================================
async function loadBarangs() {
    if (isLoading) return;
    isLoading = true;
    showLoading(true);
    try {
        const params = new URLSearchParams({ page: currentPage, per_page: perPage, search: searchQuery });
        const res    = await fetch(`/barangs/data?${params}`, {
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
function renderTable(barangs) {
    const tbody = document.getElementById('barangsTableBody');
    if (!barangs || barangs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="px-6 py-16 text-center">
            <p class="mt-4 text-gray-600 font-semibold">Tidak ada barang ditemukan</p>
            <p class="text-gray-500 text-sm">${searchQuery ? 'Coba kata kunci lain' : 'Klik "Add Barang" atau Import Excel untuk memulai'}</p>
        </td></tr>`;
        return;
    }
    tbody.innerHTML = barangs.map(b => `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-4 text-sm text-gray-900">${b.row_number}</td>
            <td class="px-4 py-4">${imageCell(b)}</td>
            <td class="px-4 py-4 text-sm font-semibold text-gray-900 font-mono">${esc(b.kode_barang)}</td>
            <td class="px-4 py-4"><p class="text-sm font-semibold text-gray-900">${esc(b.nama)}</p></td>
            <td class="px-4 py-4 text-sm text-gray-600">${esc(b.cust) || '-'}</td>
            <td class="px-4 py-4 text-sm text-gray-600">${esc(b.model) || '-'}</td>
            <td class="px-4 py-4">${diesBadge(b.dies_count)}</td>
            <td class="px-4 py-4 text-sm text-gray-600">${esc(b.line) || '-'}</td>
            <td class="px-4 py-4">${actionButtons(b.id)}</td>
        </tr>
    `).join('');
}

function imageCell(b) {
    if (!b.gambar_url) {
        return `<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg></div>`;
    }
    return `<img src="${b.gambar_url}" onclick="showImagePreview('${b.gambar_url}','${esc(b.nama)}')"
        class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition">`;
}

function diesBadge(count) {
    return count > 0
        ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${count} proses</span>`
        : '<span class="text-xs text-gray-400">-</span>';
}

function actionButtons(id) {
    return `<div class="flex items-center justify-center space-x-2">
        <button onclick="openDetailModal(${id})" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition">Detail</button>
        <button onclick="openEditModal(${id})" class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">Edit</button>
        <button onclick="deleteBarang(${id})" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">Delete</button>
    </div>`;
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

    let html = pageBtn(
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>',
        currentPage - 1, currentPage === 1 ? BTN_PAGE_DISABLED : BTN_PAGE_NORMAL, currentPage === 1
    );

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
    html += pageBtn(
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
        currentPage + 1, currentPage === totalPages ? BTN_PAGE_DISABLED : BTN_PAGE_NORMAL, currentPage === totalPages
    );

    container.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    loadBarangs();
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
    document.getElementById('barangsTableBody').innerHTML = `<tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">${msg}</td></tr>`;
}
function modalShow(id) {
    const el = document.getElementById(id);
    el.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('modal-fade-in')));
}
function modalHide(id) {
    const el = document.getElementById(id);
    el.classList.remove('modal-fade-in');
    setTimeout(() => { el.style.display = 'none'; }, 300);
}

// ============================================
// IMAGE PREVIEW
// ============================================
function showImagePreview(src, title) {
    document.getElementById('previewImageSrc').src           = src;
    document.getElementById('previewImageTitle').textContent = title;
    document.getElementById('downloadImageLink').href        = src;
    modalShow('imagePreviewModal');
}
function closeImagePreview() { modalHide('imagePreviewModal'); }

function readAsImage(file, onLoaded) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => onLoaded(ev.target.result);
    reader.readAsDataURL(file);
}
function previewImage(e, id) {
    readAsImage(e.target.files[0], src => {
        document.getElementById(id).src = src;
        document.getElementById(id + 'Container').classList.remove('hidden');
    });
}
function previewEditImage(e) {
    readAsImage(e.target.files[0], src => {
        document.getElementById('editPreview').src           = src;
        document.getElementById('editPreview').style.display = 'block';
        document.getElementById('noImageText').style.display = 'none';
    });
}

// ============================================
// IMPORT MODAL
// ============================================
function openImportModal()  { modalShow('importModal'); }
function closeImportModal() { modalHide('importModal'); clearFileSelection(); }

function handleFileSelect(e) { if (e.target.files[0]) showSelectedFile(e.target.files[0]); }
function handleFileDrop(file) {
    if (!file.name.match(/\.(xlsx|xls)$/i)) { Swal.fire('Format Salah', 'Hanya file .xlsx atau .xls', 'error'); return; }
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('excelFileInput').files = dt.files;
    showSelectedFile(file);
}
function showSelectedFile(file) {
    document.getElementById('selectedFileName').textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
    document.getElementById('selectedFileInfo').classList.remove('hidden');
    document.getElementById('dropZoneText').textContent = 'File dipilih';
}
function clearFileSelection() {
    document.getElementById('excelFileInput').value = '';
    document.getElementById('selectedFileInfo').classList.add('hidden');
    document.getElementById('dropZoneText').textContent = 'Klik atau drag & drop file Excel di sini';
}
async function submitImport() {
    const fi = document.getElementById('excelFileInput');
    if (!fi.files.length) { Swal.fire('Pilih File', 'Silakan pilih file Excel terlebih dahulu', 'warning'); return; }
    const btn  = document.getElementById('importSubmitBtn');
    const prog = document.getElementById('importProgress');
    btn.disabled = true;
    prog.classList.remove('hidden');
    const fd = new FormData();
    fd.append('excel_file', fi.files[0]);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    try {
        const res  = await fetch('/barangs/import-excel', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            closeImportModal();
            await Swal.fire({
                icon: 'success', title: 'Import Berhasil!',
                html: `<div class="text-left text-sm space-y-1">
                    <p>✅ <strong>${data.stats.imported}</strong> barang baru</p>
                    <p>🔄 <strong>${data.stats.updated}</strong> barang diupdate</p>
                    <p>📦 <strong>${data.stats.total}</strong> total delivery part code</p></div>`,
                confirmButtonColor: '#000'
            });
            loadBarangs();
        } else { Swal.fire('Error!', data.message, 'error'); }
    } catch (e) { Swal.fire('Error!', 'Gagal mengirim file', 'error'); }
    finally { btn.disabled = false; prog.classList.add('hidden'); }
}

// ============================================
// DIES DETAIL ROWS
// ============================================
const DIES_FIELDS = ['child_part_code', 'part_name', 'cust', 'model', 'process_name', 'process_no'];
const DIES_PLACEHOLDERS = { child_part_code: 'Child Code', part_name: 'Part Name', cust: 'Cust', model: 'Model', process_name: 'Proses Name', process_no: 'No' };
const DIES_COLSPAN = { child_part_code: 2, part_name: 3, cust: 1, model: 2, process_name: 2, process_no: 1 };

function addDiesDetailRow(containerId, data = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const row = document.createElement('div');
    row.className = 'dies-detail-row grid grid-cols-12 gap-1.5 items-center p-1.5 border border-gray-200 rounded-lg bg-gray-50';
    row.innerHTML = DIES_FIELDS.map(f => `
        <div class="col-span-${DIES_COLSPAN[f]}">
            <input type="text" data-field="${f}" value="${esc(data[f] || '')}" placeholder="${DIES_PLACEHOLDERS[f]}" class="${INPUT_XS}">
        </div>`).join('') + `
        <div class="col-span-1 flex justify-center">
            <button type="button" onclick="this.closest('.dies-detail-row').remove()" class="bg-red-500 text-white p-1 rounded hover:bg-red-600">${ICON.trash}</button>
        </div>`;
    container.appendChild(row);
}

function appendDiesDetailsToFormData(formData, containerId) {
    document.querySelectorAll(`#${containerId} .dies-detail-row`).forEach((row, idx) => {
        DIES_FIELDS.forEach(f => {
            const inp = row.querySelector(`[data-field="${f}"]`);
            if (inp) formData.append(`dies_details[${idx}][${f}]`, inp.value);
        });
    });
}

// ============================================
// PART ROWS
// ============================================
function buildPartRow(idx, partId, qty, selectClass, modalId) {
    const opts = ['<option value="">Select Part</option>']
        .concat(partsData.map(p => `<option value="${p.id}" ${partId == p.id ? 'selected' : ''}>${p.nama} (${p.kode})</option>`))
        .join('');
    const row = document.createElement('div');
    row.className = 'part-row flex items-center space-x-3 p-3 border border-gray-200 rounded-lg';
    row.innerHTML = `
        <div class="flex-1">
            <select name="parts[${idx}][part_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black ${selectClass}">${opts}</select>
        </div>
        <div class="w-32">
            <input type="number" name="parts[${idx}][quantity]" value="${qty}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
        </div>
        <button type="button" onclick="removePartRowFrom(this,'${modalId}')" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600">${ICON.trash}</button>`;
    return row;
}

function appendPartRow(containerId, selectClass, modalId, partId = '', qty = 1) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const idx = partCounter++;
    const row = buildPartRow(idx, partId, qty, selectClass, modalId);
    container.appendChild(row);
    waitForJQuery(() => {
        $(row.querySelector('.' + selectClass)).select2({ placeholder: "Select Part", allowClear: false, width: '100%', dropdownParent: $(`#${modalId}`) });
    });
}

function removePartRowFrom(btn, modalId) {
    const row = btn.closest('.part-row');
    waitForJQuery(() => { try { $(row.querySelector('select')).select2('destroy'); } catch (e) {} });
    row.remove();
}

function addPartRow()     { appendPartRow('partsContainer',     'part-select-create', 'createModal'); }
function addEditPartRow() { appendPartRow('editPartsContainer', 'part-select-edit',   'editModal'); }

// ============================================
// CREATE MODAL
// ============================================
function openCreateModal() {
    modalShow('createModal');

    document.getElementById('createForm').reset();
    document.getElementById('createPreviewContainer').classList.add('hidden');
    document.getElementById('partsContainer').innerHTML       = '';
    document.getElementById('diesDetailsContainer').innerHTML = '';
    partCounter = 0;
    clearErrors();

    waitForJQuery(() => {
        try { $('#createSupplierId').select2('destroy'); } catch (e) {}
        try { $('#createLineId').select2('destroy'); } catch (e) {}
        $('#createSupplierId').select2({ placeholder: "Select Supplier", allowClear: false, width: '100%', dropdownParent: $('#createModal') });
        $('#createLineId').select2({ placeholder: "Select Line", allowClear: true, width: '100%', dropdownParent: $('#createModal') });
    });

    document.getElementById('createForm').onsubmit = async function (e) {
        e.preventDefault();
        clearErrors();
        const fd = new FormData(this);
        appendDiesDetailsToFormData(fd, 'diesDetailsContainer');
        try {
            const res  = await fetch('/barangs', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
                closeCreateModal();
                loadBarangs();
            } else if (data.errors) {
                displayErrors(data.errors, 'create');
            } else {
                Swal.fire('Error!', data.message || 'Unknown error', 'error');
            }
        } catch (err) { Swal.fire('Error!', 'Terjadi kesalahan', 'error'); }
    };
}

function closeCreateModal() {
    waitForJQuery(() => {
        try { $('#createSupplierId').select2('destroy'); } catch (e) {}
        try { $('#createLineId').select2('destroy'); } catch (e) {}
        document.querySelectorAll('#createModal .part-select-create').forEach(s => { try { $(s).select2('destroy'); } catch (e) {} });
    });
    modalHide('createModal');
}

// ============================================
// EDIT MODAL
// ============================================
async function openEditModal(id) {
    modalShow('editModal');

    try {
        const res    = await fetch(`/barangs/${id}`);
        const result = await res.json();
        if (!result.success) throw new Error('Gagal fetch');
        const b = result.data;

        document.getElementById('editBarangId').value   = b.id;
        document.getElementById('editKodeBarang').value = b.kode_barang;
        document.getElementById('editNama').value       = b.nama;
        document.getElementById('editAddress').value    = b.address || '';
        document.getElementById('editCust').value       = b.cust    || '';
        document.getElementById('editModel').value      = b.model   || '';

        const prev     = document.getElementById('editPreview');
        const noImgTxt = document.getElementById('noImageText');
        if (b.gambar) {
            prev.src = `/storage/barangs/${b.gambar}`;
            prev.style.display = 'block'; noImgTxt.style.display = 'none';
        } else {
            prev.style.display = 'none'; noImgTxt.style.display = 'block';
        }

        document.getElementById('editPartsContainer').innerHTML = '';
        partCounter = 0;
        b.parts?.forEach(p => appendPartRow('editPartsContainer', 'part-select-edit', 'editModal', p.id, p.pivot.quantity));

        document.getElementById('editDiesDetailsContainer').innerHTML = '';
        b.dies_details?.forEach(d => addDiesDetailRow('editDiesDetailsContainer', d));

        waitForJQuery(() => {
            try { $('#editSupplierId').select2('destroy'); } catch (e) {}
            try { $('#editLineId').select2('destroy'); } catch (e) {}
            $('#editSupplierId').select2({ placeholder: "Select Supplier", allowClear: false, width: '100%', dropdownParent: $('#editModal') })
                .val(b.supplier_id).trigger('change');
            $('#editLineId').select2({ placeholder: "Select Line", allowClear: true, width: '100%', dropdownParent: $('#editModal') })
                .val(b.line_id).trigger('change');
        });

        clearErrors();

        document.getElementById('editForm').onsubmit = async function (e) {
            e.preventDefault();
            clearErrors();
            const fd = new FormData(this);
            fd.append('_method', 'PUT');
            appendDiesDetailsToFormData(fd, 'editDiesDetailsContainer');
            try {
                const res  = await fetch(`/barangs/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
                    closeEditModal();
                    loadBarangs();
                } else if (data.errors) {
                    displayErrors(data.errors, 'edit');
                }
            } catch (err) { Swal.fire('Error!', 'Terjadi kesalahan!', 'error'); }
        };
    } catch (e) {
        modalHide('editModal');
        Swal.fire('Error!', 'Gagal memuat data', 'error');
    }
}

function closeEditModal() {
    waitForJQuery(() => {
        try { $('#editSupplierId').select2('destroy'); } catch (e) {}
        try { $('#editLineId').select2('destroy'); } catch (e) {}
        document.querySelectorAll('#editModal .part-select-edit').forEach(s => { try { $(s).select2('destroy'); } catch (e) {} });
    });
    modalHide('editModal');
}

// ============================================
// DETAIL MODAL
// ============================================
async function openDetailModal(id) {
    const modal   = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');

    modal.style.display = 'flex';
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    try {
        const res    = await fetch(`/barangs/${id}`);
        const result = await res.json();
        if (!result.success) throw new Error();
        const b = result.data;

        document.getElementById('detailKodeBarang').textContent = b.kode_barang;
        document.getElementById('detailNama').textContent       = b.nama;
        document.getElementById('detailAddress').textContent    = b.address ?? '-';
        document.getElementById('detailCust').textContent       = b.cust    ?? '-';
        document.getElementById('detailModel').textContent      = b.model   ?? '-';
        document.getElementById('detailSupplier').textContent   = b.supplier?.nama ?? '-';

        const partsTbody = document.getElementById('detailPartsTableBody');
        partsTbody.innerHTML = b.parts?.length
            ? b.parts.map((p, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">${i + 1}</td>
                    <td class="px-4 py-3 text-sm font-medium">${esc(p.kode_part)}</td>
                    <td class="px-4 py-3 text-sm">${esc(p.nama)}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">${p.pivot.quantity} ${esc(p.satuan)}</span>
                    </td>
                </tr>`).join('')
            : `<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No parts found</td></tr>`;

        const diesTbody = document.getElementById('detailDiesTableBody');
        diesTbody.innerHTML = b.dies_details?.length
            ? b.dies_details.map((d, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-xs text-gray-500">${i + 1}</td>
                    <td class="px-3 py-2 text-xs font-mono font-medium text-gray-900">${esc(d.child_part_code) || '-'}</td>
                    <td class="px-3 py-2 text-xs text-gray-700">${esc(d.part_name) || '-'}</td>
                    <td class="px-3 py-2 text-xs text-gray-600">${esc(d.cust) || '-'}</td>
                    <td class="px-3 py-2 text-xs text-gray-600">${esc(d.model) || '-'}</td>
                    <td class="px-3 py-2 text-xs text-gray-600">${esc(d.process_name) || '-'}</td>
                    <td class="px-3 py-2 text-xs text-center">
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">${esc(d.process_no) || '-'}</span>
                    </td>
                </tr>`).join('')
            : `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ada data dies detail</td></tr>`;

        requestAnimationFrame(() => requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }));
    } catch (e) {
        modal.style.display = 'none';
        Swal.fire('Error!', 'Gagal memuat detail', 'error');
    }
}

function closeDetailModal() {
    const modal   = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

// ============================================
// DELETE
// ============================================
async function deleteBarang(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?', text: 'Data akan dihapus permanent!', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    });
    if (!result.isConfirmed) return;
    try {
        const res  = await fetch(`/barangs/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadBarangs();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (e) { Swal.fire('Error!', 'Gagal menghapus!', 'error'); }
}

// ============================================
// ERROR DISPLAY
// ============================================
function displayErrors(errors, prefix) {
    Object.keys(errors).forEach(key => {
        const el = document.getElementById(`error-${prefix}-${key}`);
        if (el) el.textContent = errors[key][0];
    });
}
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

// ============================================
// KEYBOARD
// ============================================
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCreateModal(); closeEditModal(); closeDetailModal();
        closeImagePreview(); closeImportModal();
    }
});
</script>
@endpush