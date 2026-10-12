@extends('layouts.app')

@section('title', 'History Checkups')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">History Checkups</h1>
            <p class="text-gray-600 mt-1">Riwayat preventive maintenance yang telah selesai</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Total History</p>
                    <p id="statTotal" class="text-2xl font-bold text-gray-900 mt-1">-</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">This Month</p>
                    <p id="statThisMonth" class="text-2xl font-bold text-blue-900 mt-1">-</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Avg Duration</p>
                    <p id="statAvgDuration" class="text-2xl font-bold text-purple-900 mt-1">-</p>
                    <p class="text-xs text-purple-600 font-medium mt-1">minutes</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Success Rate</p>
                    <p id="statSuccess" class="text-2xl font-bold text-green-900 mt-1">-</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Ranking Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Top Preventive --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Dies Paling Sering Preventive</p>
            </div>
            <div id="summaryPreventive" class="space-y-2">
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
            </div>
        </div>

        {{-- Top NG --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Dies Paling Banyak NG</p>
            </div>
            <div id="summaryNg" class="space-y-2">
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
                <div class="h-4 bg-gray-100 rounded animate-pulse"></div>
            </div>
        </div>

    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:flex-wrap gap-3">
            <div class="w-full md:w-1/3 lg:w-1/4 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Cari kode, nama barang...">
            </div>

            {{-- Date Range Filter (Tgl Checkup) --}}
            <div class="flex items-center gap-2">
                <label for="dateFromInput" class="text-xs font-medium text-gray-500 whitespace-nowrap">Tgl Checkup</label>
                <input type="date" id="dateFromInput"
                    class="px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition text-sm">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" id="dateToInput"
                    class="px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition text-sm">
                <button id="clearDateBtn" type="button"
                    class="hidden text-xs text-gray-500 hover:text-gray-800 underline whitespace-nowrap">
                    Reset
                </button>
            </div>

            <div class="flex-shrink-0 md:ml-auto">
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

    {{-- Table --}}
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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-10">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Checkup</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mulai</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Selesai</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Periode ke-</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
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

@include('history-checkups.detail-modal')

@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════════
let currentPage   = 1;
let perPage       = 20;
let searchQuery   = '';
let dateFrom      = '';
let dateTo        = '';
let totalPages    = 1;
let isLoading     = false;
let searchTimeout = null;

// ════════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    loadData();
    loadSummary();

    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { searchQuery = this.value; currentPage = 1; loadData(); }, 300);
    });

    document.getElementById('perPageSelect').addEventListener('change', function () {
        perPage = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        loadData();
    });

    document.getElementById('dateFromInput').addEventListener('change', function () {
        dateFrom = this.value;
        currentPage = 1;
        toggleClearDateBtn();
        loadData();
    });

    document.getElementById('dateToInput').addEventListener('change', function () {
        dateTo = this.value;
        currentPage = 1;
        toggleClearDateBtn();
        loadData();
    });

    document.getElementById('clearDateBtn').addEventListener('click', function () {
        dateFrom = '';
        dateTo   = '';
        document.getElementById('dateFromInput').value = '';
        document.getElementById('dateToInput').value   = '';
        currentPage = 1;
        toggleClearDateBtn();
        loadData();
    });
});

function toggleClearDateBtn() {
    document.getElementById('clearDateBtn').classList.toggle('hidden', !dateFrom && !dateTo);
}

// ════════════════════════════════════════════════════════
// LOAD SUMMARY (stats + ranking)
// ════════════════════════════════════════════════════════
async function loadSummary() {
    try {
        const res    = await fetch('/history-checkups/summary', { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) return;

        const s = result.stats;
        document.getElementById('statTotal').textContent       = s.total;
        document.getElementById('statThisMonth').textContent   = s.this_month;
        document.getElementById('statAvgDuration').textContent = s.avg_duration;
        document.getElementById('statSuccess').textContent     = s.success_rate + '%';

        renderTopList('summaryPreventive', result.by_preventive, 'x', 'blue');
        renderTopList('summaryNg', result.by_ng, ' NG', 'red');
    } catch (e) {
        document.getElementById('summaryPreventive').innerHTML = '<p class="text-xs text-gray-400">Gagal memuat data</p>';
        document.getElementById('summaryNg').innerHTML         = '<p class="text-xs text-gray-400">Gagal memuat data</p>';
    }
}

function renderTopList(elId, data, suffix, color) {
    const el = document.getElementById(elId);
    if (!data || !data.length) { el.innerHTML = '<p class="text-xs text-gray-400">Belum ada data</p>'; return; }

    const max  = Number(data[0].total);
    const bars = color === 'red'
        ? ['bg-red-500', 'bg-red-400', 'bg-red-300']
        : ['bg-blue-500', 'bg-blue-400', 'bg-blue-300'];

    el.innerHTML = data.map(function (item, idx) {
        const total   = Number(item.total);
        const pct     = max > 0 ? Math.round((total / max) * 100) : 0;
        const rankCls = idx === 0 ? 'text-yellow-600 font-bold' : 'text-gray-400';
        const barCls  = bars[Math.min(idx, 2)];
        return '<div class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 rounded px-1 -mx-1 py-0.5" onclick="openDetailModal(' + Number(item.barang_id) + ')" title="Lihat history ' + esc(item.nama) + '">'
            + '<span class="text-xs w-4 text-center ' + rankCls + '">' + (idx + 1) + '</span>'
            + '<div class="w-48 flex-shrink-0 min-w-0">'
            +   '<p class="text-[11px] font-mono font-semibold text-gray-800 truncate">' + esc(item.kode_barang) + '</p>'
            +   '<p class="text-[10px] text-gray-500 truncate">' + esc(item.nama) + '</p>'
            + '</div>'
            + '<div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden"><div class="' + barCls + ' h-2 rounded-full transition-all" style="width:' + pct + '%"></div></div>'
            + '<span class="text-xs font-semibold text-gray-700 w-14 text-right whitespace-nowrap">' + total + suffix + '</span>'
            + '</div>';
    }).join('');
}

// ════════════════════════════════════════════════════════
// LOAD DATA
// ════════════════════════════════════════════════════════
async function loadData() {
    if (isLoading) return;
    isLoading = true;
    showLoading(true);
    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage,
            search: searchQuery,
            date_from: dateFrom,
            date_to: dateTo,
        });
        const res = await fetch('/history-checkups/data?' + params, {
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

// ════════════════════════════════════════════════════════
// RENDER TABLE
// ════════════════════════════════════════════════════════
function renderTable(items) {
    const tbody = document.getElementById('historyTableBody');
    if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Tidak ada data history</p>'
            + '<p class="text-gray-500 text-sm">' + (searchQuery || dateFrom || dateTo ? 'Coba kata kunci / rentang tanggal lain' : 'Belum ada checkup yang selesai') + '</p>'
            + '</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(function (r) {
        var img = r.gambar
            ? '<img src="/storage/barangs/' + encodeURIComponent(r.gambar) + '" class="w-12 h-12 rounded-lg object-cover border border-gray-200">'
            : '<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">'
              + '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
              + '</div>';

        var periodeBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 whitespace-nowrap">Ke-' + r.periode_ke + '</span>';

        var detailBtn = r.barang_id
            ? '<button onclick="openDetailModal(' + Number(r.barang_id) + ')" title="Lihat History Barang"'
              + ' class="bg-yellow-500 hover:bg-yellow-600 text-white w-8 h-8 rounded-lg transition flex items-center justify-center">'
              + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">'
              +   '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
              +   '<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
              + '</svg></button>'
            : '';

        var deleteBtn = '<button onclick="deleteHistory(' + Number(r.id) + ')" title="Hapus"'
              + ' class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg transition flex items-center justify-center">'
              + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
              + '</button>';

        return '<tr class="hover:bg-gray-50 transition">'
             + '<td class="px-4 py-3 text-sm text-gray-500">' + r.row_number + '</td>'
             + '<td class="px-4 py-3">' + img + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900">' + esc(r.kode_barang) + '</td>'
             + '<td class="px-4 py-3 text-sm font-semibold text-gray-900 max-w-xs truncate" title="' + esc(r.nama) + '">' + esc(r.nama) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + (esc(r.tanggal_checkup) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600">' + (esc(r.mulai) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600">' + (esc(r.selesai) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + esc(r.durasi) + '</td>'
             + '<td class="px-4 py-3 text-center">' + periodeBadge + '</td>'
             + '<td class="px-4 py-3"><div class="flex items-center justify-center gap-2">' + detailBtn + deleteBtn + '</div></td>'
             + '</tr>';
    }).join('');
}

// ════════════════════════════════════════════════════════
// PAGINATION
// ════════════════════════════════════════════════════════
function renderPagination(p) {
    const container = document.getElementById('paginationContainer');
    totalPages = p.total_pages;
    if (totalPages <= 1) { container.innerHTML = ''; return; }
    const b  = 'px-3 py-1.5 rounded-lg border text-xs transition';
    const bA = 'bg-black text-white border-black font-semibold';
    const bN = 'border-gray-300 text-gray-700 hover:bg-gray-50';
    const bD = 'border-gray-200 text-gray-400 cursor-not-allowed';
    const arL = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
    const arR = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
    let html = '<button onclick="goToPage(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + ' class="' + b + ' ' + (currentPage === 1 ? bD : bN) + '">' + arL + '</button>';
    const max = 5;
    let start = Math.max(1, currentPage - Math.floor(max / 2));
    let end   = Math.min(totalPages, start + max - 1);
    if (end - start < max - 1) start = Math.max(1, end - max + 1);
    if (start > 1) {
        html += '<button onclick="goToPage(1)" class="' + b + ' ' + bN + '">1</button>';
        if (start > 2) html += '<span class="px-1 text-gray-500 text-xs">...</span>';
    }
    for (let i = start; i <= end; i++) {
        html += '<button onclick="goToPage(' + i + ')" class="' + b + ' ' + (i === currentPage ? bA : bN) + '">' + i + '</button>';
    }
    if (end < totalPages) {
        if (end < totalPages - 1) html += '<span class="px-1 text-gray-500 text-xs">...</span>';
        html += '<button onclick="goToPage(' + totalPages + ')" class="' + b + ' ' + bN + '">' + totalPages + '</button>';
    }
    html += '<button onclick="goToPage(' + (currentPage + 1) + ')" ' + (currentPage === totalPages ? 'disabled' : '') + ' class="' + b + ' ' + (currentPage === totalPages ? bD : bN) + '">' + arR + '</button>';
    container.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    loadData();
}

function updateShowingInfo(p) {
    document.getElementById('showingFrom').textContent  = p.from;
    document.getElementById('showingTo').textContent    = p.to;
    document.getElementById('totalEntries').textContent = p.total;
}

// ════════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════════
async function deleteHistory(historyId) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: 'History akan dihapus permanent! Nomor periode setelahnya akan ikut bergeser.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch('/history-checkups/' + historyId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await response.json();

        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
            loadSummary();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Gagal menghapus history!', 'error');
    }
}

// ════════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════════
function esc(str) {
    if (!str && str !== 0) return '';
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}
function showLoading(show) { document.getElementById('loadingOverlay').classList.toggle('hidden', !show); }
function showEmpty(msg) {
    document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="10" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDetailModal();
});
</script>
@endpush