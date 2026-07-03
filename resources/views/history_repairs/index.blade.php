@extends('layouts.app')

@section('title', 'History Request Repair')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">History Request Repair</h1>
            <p class="text-gray-600 mt-1">Riwayat semua perbaikan yang telah closed</p>
        </div>
        <a href="{{ route('request-repairs.index') }}"
            class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-semibold hover:bg-gray-50 transition flex items-center space-x-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Request Repair</span>
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Top Kategori --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Kategori Terbanyak</p>
            </div>
            <div id="summaryKategori" class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-24 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-20 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-16 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
            </div>
        </div>

        {{-- Top Part No --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Part No Terbanyak Repair</p>
            </div>
            <div id="summaryPartNo" class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-24 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-20 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-16 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 flex-1 bg-gray-100 rounded animate-pulse"></div>
                </div>
            </div>
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
                    placeholder="Cari no, part no, nama...">
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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Request</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Pengajuan</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Closed</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Part No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Repair ke-</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Judge Monitoring</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Judge Permanen</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Durasi</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    <tr>
                        <td colspan="12" class="px-6 py-16 text-center">
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

@include('history_repairs.detail')

@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════════
let currentPage   = 1;
let perPage       = 20;
let searchQuery   = '';
let totalPages    = 1;
let isLoading     = false;
let searchTimeout = null;

// ════════════════════════════════════════════════════════
// BADGES
// ════════════════════════════════════════════════════════
const KATEGORI_BADGE = {
    'Dies':        'bg-blue-100 text-blue-800',
    'Burry':       'bg-yellow-100 text-yellow-800',
    'Dimensi':     'bg-purple-100 text-purple-800',
    'Human Error': 'bg-red-100 text-red-800',
    'Accessories': 'bg-green-100 text-green-800',
};

const KATEGORI_BAR = {
    'Dies':        'bg-blue-400',
    'Burry':       'bg-yellow-400',
    'Dimensi':     'bg-purple-400',
    'Human Error': 'bg-red-400',
    'Accessories': 'bg-green-400',
};

function okngBadge(val) {
    if (!val) return '<span class="text-gray-400 text-xs">-</span>';
    const cls = val === 'OK'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ' + cls + '">' + val + '</span>';
}

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
});

// ════════════════════════════════════════════════════════
// LOAD SUMMARY
// ════════════════════════════════════════════════════════
async function loadSummary() {
    try {
        const res    = await fetch('/history-repairs/summary', { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) return;
        renderKategoriSummary(result.by_kategori);
        renderPartNoSummary(result.by_part_no);
    } catch (e) {
        document.getElementById('summaryKategori').innerHTML = '<p class="text-xs text-gray-400">Gagal memuat data</p>';
        document.getElementById('summaryPartNo').innerHTML   = '<p class="text-xs text-gray-400">Gagal memuat data</p>';
    }
}

function renderKategoriSummary(data) {
    const el = document.getElementById('summaryKategori');
    if (!data || !data.length) { el.innerHTML = '<p class="text-xs text-gray-400">Belum ada data</p>'; return; }
    const max = data[0].total;
    el.innerHTML = data.map(function (item, idx) {
        const barCls   = KATEGORI_BAR[item.kategori_problem]   || 'bg-gray-400';
        const badgeCls = KATEGORI_BADGE[item.kategori_problem] || 'bg-gray-100 text-gray-700';
        const pct      = max > 0 ? Math.round((item.total / max) * 100) : 0;
        const rankCls  = idx === 0 ? 'text-yellow-600 font-bold' : 'text-gray-400';
        return '<div class="flex items-center gap-2">'
            + '<span class="text-xs w-4 text-center ' + rankCls + '">' + (idx + 1) + '</span>'
            + '<span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium ' + badgeCls + ' w-24 justify-center flex-shrink-0">' + esc(item.kategori_problem) + '</span>'
            + '<div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden"><div class="' + barCls + ' h-2 rounded-full transition-all" style="width:' + pct + '%"></div></div>'
            + '<span class="text-xs font-semibold text-gray-700 w-6 text-right">' + item.total + '</span>'
            + '</div>';
    }).join('');
}

function renderPartNoSummary(data) {
    const el = document.getElementById('summaryPartNo');
    if (!data || !data.length) { el.innerHTML = '<p class="text-xs text-gray-400">Belum ada data</p>'; return; }
    const max = data[0].total;
    el.innerHTML = data.map(function (item, idx) {
        const pct     = max > 0 ? Math.round((item.total / max) * 100) : 0;
        const rankCls = idx === 0 ? 'text-yellow-600 font-bold' : 'text-gray-400';
        const barCls  = idx === 0 ? 'bg-red-400' : idx === 1 ? 'bg-orange-400' : 'bg-blue-300';
        return '<div class="flex items-center gap-2">'
            + '<span class="text-xs w-4 text-center ' + rankCls + '">' + (idx + 1) + '</span>'
            + '<span class="text-[11px] font-mono text-gray-800 w-48 flex-shrink-0" title="' + esc(item.part_no) + '">' + esc(item.part_no) + '</span>'
            + '<div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden"><div class="' + barCls + ' h-2 rounded-full transition-all" style="width:' + pct + '%"></div></div>'
            + '<span class="text-xs font-semibold text-gray-700 w-12 text-right whitespace-nowrap">' + item.total + 'x repair</span>'
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
        const params = new URLSearchParams({ page: currentPage, per_page: perPage, search: searchQuery });
        const res    = await fetch('/history-repairs/data?' + params, {
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
        tbody.innerHTML = '<tr><td colspan="12" class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Tidak ada data history</p>'
            + '<p class="text-gray-500 text-sm">' + (searchQuery ? 'Coba kata kunci lain' : 'Belum ada repair yang closed') + '</p>'
            + '</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(function (r) {
        var katCls = KATEGORI_BADGE[r.kategori_problem] || 'bg-gray-100 text-gray-700';

        var repairBadge = r.repair_count >= 3
            ? '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 text-red-800 text-xs font-bold">' + r.repair_count + '</span>'
            : r.repair_count === 2
                ? '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold">' + r.repair_count + '</span>'
                : '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">' + r.repair_count + '</span>';

        return '<tr class="hover:bg-gray-50 transition">'
             + '<td class="px-4 py-3 text-sm text-gray-500">' + r.row_number + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900">' + esc(r.no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + (esc(r.tanggal_pengajuan) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + (esc(r.closed_at) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono text-gray-900">' + esc(r.part_no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-800 max-w-xs truncate" title="' + esc(r.nama) + '">' + esc(r.nama) + '</td>'
             + '<td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + katCls + '">' + esc(r.kategori_problem) + '</span></td>'
             + '<td class="px-4 py-3 text-center">' + repairBadge + '</td>'
             + '<td class="px-4 py-3 text-center">' + okngBadge(r.judge_monitoring) + '</td>'
             + '<td class="px-4 py-3 text-center">' + okngBadge(r.judge_permanen) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600">' + esc(r.durasi_total) + '</td>'
             + '<td class="px-4 py-3"><div class="flex items-center justify-center">'
             +   '<button onclick="openDetailModal(\'' + esc(r.part_no) + '\', ' + r.id + ')" title="Lihat History Part No"'
             +     ' class="bg-yellow-500 hover:bg-yellow-600 text-white w-8 h-8 rounded-lg transition flex items-center justify-center">'
             +     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">'
             +       '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
             +       '<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
             +     '</svg>'
             +   '</button>'
             + '</div></td>'
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
    let html = '<button onclick="goToPage(' + (currentPage-1) + ')" ' + (currentPage===1?'disabled':'') + ' class="' + b + ' ' + (currentPage===1?bD:bN) + '">' + arL + '</button>';
    const max=5; let start=Math.max(1,currentPage-Math.floor(max/2)); let end=Math.min(totalPages,start+max-1);
    if(end-start<max-1) start=Math.max(1,end-max+1);
    if(start>1){html+='<button onclick="goToPage(1)" class="'+b+' '+bN+'">1</button>';if(start>2)html+='<span class="px-1 text-gray-500 text-xs">...</span>';}
    for(let i=start;i<=end;i++) html+='<button onclick="goToPage('+i+')" class="'+b+' '+(i===currentPage?bA:bN)+'">'+i+'</button>';
    if(end<totalPages){if(end<totalPages-1)html+='<span class="px-1 text-gray-500 text-xs">...</span>';html+='<button onclick="goToPage('+totalPages+')" class="'+b+' '+bN+'">'+totalPages+'</button>';}
    html+='<button onclick="goToPage('+(currentPage+1)+')" '+(currentPage===totalPages?'disabled':'')+' class="'+b+' '+(currentPage===totalPages?bD:bN)+'">'+arR+'</button>';
    container.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page; loadData();
}

function updateShowingInfo(p) {
    document.getElementById('showingFrom').textContent  = p.from;
    document.getElementById('showingTo').textContent    = p.to;
    document.getElementById('totalEntries').textContent = p.total;
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
    document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="12" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDetailModal();
});
</script>
@endpush