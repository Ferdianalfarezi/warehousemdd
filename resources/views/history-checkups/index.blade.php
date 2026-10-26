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
                <p class="text-sm font-semibold text-gray-700">Pareto Dies Paling Sering Preventive <span class="text-xs font-normal text-gray-400">(Top 10)</span></p>
            </div>
            <div style="position:relative;height:280px;">
                <canvas id="paretoPreventive"></canvas>
                <div id="paretoPreventiveEmpty" style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;" class="text-xs text-gray-400">Belum ada data</div>
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
                <p class="text-sm font-semibold text-gray-700">Pareto Dies Paling Banyak NG <span class="text-xs font-normal text-gray-400">(Top 10)</span></p>
            </div>
            <div style="position:relative;height:280px;">
                <canvas id="paretoNg"></canvas>
                <div id="paretoNgEmpty" style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;" class="text-xs text-gray-400">Belum ada data</div>
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

            {{-- Customer Filter (bisa pilih lebih dari 1, diambil dari master barang) --}}
            <div style="min-width:220px;max-width:360px;flex:1 1 220px;">
                <select id="customerFilter" multiple style="width:100%;">
                    @foreach($customers as $cust)
                        <option value="{{ $cust }}">{{ $cust }}</option>
                    @endforeach
                </select>
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
                    <tr id="initialLoadingRow">
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

@can('superadmin-only')
{{-- Modal Edit History (khusus superadmin). Id sengaja bukan #editModal (ada style global di layout) --}}
<div id="historyEditModal" style="display:none;position:fixed;inset:0;z-index:70;background-color:rgba(0,0,0,0);transition:background-color .3s ease;align-items:center;justify-content:center;padding:16px;">
    <div class="bg-white rounded-2xl shadow-2xl w-full" style="max-width:560px;max-height:90vh;overflow-y:auto;opacity:0;transform:translateY(16px) scale(.95);transition:opacity .3s cubic-bezier(.4,0,.2,1),transform .3s cubic-bezier(.4,0,.2,1);">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit History Checkup</h3>
                <p id="heTitle" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button type="button" onclick="closeHistoryEdit()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="historyEditForm" onsubmit="event.preventDefault(); submitHistoryEdit();" class="p-6 space-y-4">
            <input type="hidden" id="heId">

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal & Waktu</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tgl Terjadwal</label>
                    <input type="date" id="heTanggalTerjadwal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tgl Checkup <span class="text-red-500">*</span></label>
                    <input type="date" id="heTanggalCheckup" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai Perbaikan</label>
                    <input type="datetime-local" id="heMulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Waktu Selesai</label>
                    <input type="datetime-local" id="heSelesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div style="border:1px solid #fde68a;background-color:#fffbeb;border-radius:12px;padding:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <p class="text-sm font-semibold text-gray-700">Durasi Preventive</p>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;" class="text-xs text-gray-600">
                        <input type="checkbox" id="heDurasiOtomatis" onchange="renderHeDurasi()">
                        Hitung otomatis dari Mulai–Selesai
                    </label>
                </div>
                <p id="heDurasiAutoInfo" class="text-xs text-gray-500" style="margin-top:4px;"></p>
                <div id="heDurasiInputs" class="grid grid-cols-3 gap-3" style="margin-top:10px;">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Hari</label><input type="number" id="heHari" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Jam</label><input type="number" id="heJam" min="0" max="23" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Menit</label><input type="number" id="heMenit" min="0" max="59" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                </div>
            </div>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Line & Catatan</p>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Line</label>
                <input type="text" id="heLine" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Umum</label>
                <textarea id="heCatatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeHistoryEdit()" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</button>
                <button type="submit" id="heSubmitBtn" class="px-5 py-2.5 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
<script src="{{ asset('js/pareto-chart.js') }}?v={{ filemtime(public_path('js/pareto-chart.js')) }}"></script>
<script>
// ════════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════════
// Edit history hanya untuk superadmin (server juga menolak role lain)
const CAN_EDIT_HISTORY = @json(Gate::allows('superadmin-only'));

let currentPage   = 1;
let perPage       = 20;
let searchQuery   = '';
let dateFrom      = '';
let dateTo        = '';
let customers     = [];
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
        searchTimeout = setTimeout(() => { searchQuery = this.value; onFiltersChanged(); }, 300);
    });

    document.getElementById('perPageSelect').addEventListener('change', function () {
        perPage = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        loadData();
    });

    document.getElementById('dateFromInput').addEventListener('change', function () {
        dateFrom = this.value;
        toggleClearDateBtn();
        onFiltersChanged();
    });

    document.getElementById('dateToInput').addEventListener('change', function () {
        dateTo = this.value;
        toggleClearDateBtn();
        onFiltersChanged();
    });

    document.getElementById('clearDateBtn').addEventListener('click', function () {
        dateFrom = '';
        dateTo   = '';
        document.getElementById('dateFromInput').value = '';
        document.getElementById('dateToInput').value   = '';
        toggleClearDateBtn();
        onFiltersChanged();
    });

    initCustomerFilter();
});

function toggleClearDateBtn() {
    document.getElementById('clearDateBtn').classList.toggle('hidden', !dateFrom && !dateTo);
}

// Filter berubah -> tabel balik ke halaman 1 + stats & grafik ikut di-update
function onFiltersChanged() {
    currentPage = 1;
    loadData();
    loadSummary();
}

// Query string filter yang sama untuk tabel, stats & grafik
function filterParams(extra) {
    const params = new URLSearchParams(Object.assign({
        search: searchQuery,
        date_from: dateFrom,
        date_to: dateTo,
    }, extra || {}));
    customers.forEach(c => params.append('customers[]', c));
    return params;
}

// ════════════════════════════════════════════════════════
// CUSTOMER FILTER (select2 multiple, fallback ke select biasa)
// ════════════════════════════════════════════════════════
function initCustomerFilter() {
    const select = document.getElementById('customerFilter');
    const onChange = () => {
        customers = Array.from(select.selectedOptions).map(o => o.value);
        onFiltersChanged();
    };

    const start = Date.now();
    (function tryInit() {
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            jQuery(select).select2({ placeholder: 'Semua Customer', allowClear: true, width: '100%', closeOnSelect: false })
                .on('change', onChange);
        } else if (Date.now() - start < 5000) {
            setTimeout(tryInit, 50);
        } else {
            select.addEventListener('change', onChange);
        }
    })();
}

// ════════════════════════════════════════════════════════
// LOAD SUMMARY (stats + grafik Pareto, ikut filter)
// ════════════════════════════════════════════════════════
let summaryRequestId = 0;

async function loadSummary() {
    const requestId = ++summaryRequestId;
    try {
        const res    = await fetch('/history-checkups/summary?' + filterParams(), { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (requestId !== summaryRequestId || !result.success) return; // ada request filter yang lebih baru

        const s = result.stats;
        document.getElementById('statTotal').textContent       = s.total;
        document.getElementById('statThisMonth').textContent   = s.this_month;
        document.getElementById('statAvgDuration').textContent = s.avg_duration;
        document.getElementById('statSuccess').textContent     = s.success_rate + '%';

        ['paretoPreventiveEmpty', 'paretoNgEmpty'].forEach(id => { document.getElementById(id).textContent = 'Belum ada data'; });

        const toItems = rows => rows.map(r => ({ label: r.kode_barang, title: r.nama, value: Number(r.total), payload: r.barang_id }));

        renderPareto('paretoPreventive', toItems(result.by_preventive), {
            total: s.total,
            barColor: '#3b82f6',
            valueLabel: 'Preventive',
            emptyId: 'paretoPreventiveEmpty',
            onBarClick: item => openDetailModal(Number(item.payload)),
        });

        renderPareto('paretoNg', toItems(result.by_ng), {
            total: result.total_ng,
            barColor: '#ef4444',
            valueLabel: 'NG',
            emptyId: 'paretoNgEmpty',
            onBarClick: item => openDetailModal(Number(item.payload)),
        });
    } catch (e) {
        if (requestId !== summaryRequestId) return;
        ['paretoPreventiveEmpty', 'paretoNgEmpty'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = 'Gagal memuat data';
            el.style.display = 'flex';
        });
    }
}

// ════════════════════════════════════════════════════════
// LOAD DATA
// ════════════════════════════════════════════════════════
let reloadQueued = false;

async function loadData() {
    // Filter diubah saat masih loading -> jalankan lagi setelah selesai (jangan dibuang)
    if (isLoading) { reloadQueued = true; return; }
    isLoading = true;
    showLoading(true);
    try {
        const params = filterParams({ page: currentPage, per_page: perPage });
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
        if (reloadQueued) { reloadQueued = false; loadData(); }
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
            + '<p class="text-gray-500 text-sm">' + (searchQuery || dateFrom || dateTo || customers.length ? 'Coba kata kunci / tanggal / customer lain' : 'Belum ada checkup yang selesai') + '</p>'
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
             + '<td class="px-4 py-3"><div class="flex items-center justify-center gap-2">' + detailBtn + editHistoryBtn(r.id) + deleteBtn + '</div></td>'
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
// ════════════════════════════════════════════════════════
// EDIT HISTORY (superadmin)
// ════════════════════════════════════════════════════════
function editHistoryBtn(id) {
    if (!CAN_EDIT_HISTORY) return '';
    return '<button onclick="openHistoryEdit(' + Number(id) + ')" title="Edit"'
         + ' style="background-color:#f97316;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;"'
         + ' onmouseover="this.style.backgroundColor=\'#ea580c\'" onmouseout="this.style.backgroundColor=\'#f97316\'">'
         + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
         + '</button>';
}

// Selisih Mulai -> Selesai dalam menit (null kalau salah satu kosong)
function heAutoMenit() {
    const mulai = document.getElementById('heMulai').value;
    const selesai = document.getElementById('heSelesai').value;
    if (!mulai || !selesai) return null;
    return Math.max(0, Math.floor((new Date(selesai) - new Date(mulai)) / 60000));
}

function heSetDurasi(total) {
    total = Math.max(0, parseInt(total, 10) || 0);
    document.getElementById('heHari').value = Math.floor(total / 1440);
    document.getElementById('heJam').value = Math.floor((total % 1440) / 60);
    document.getElementById('heMenit').value = total % 60;
}

function heGetDurasi() {
    const v = id => Math.max(0, parseInt(document.getElementById(id).value, 10) || 0);
    return v('heHari') * 1440 + v('heJam') * 60 + v('heMenit');
}

function renderHeDurasi() {
    const auto = document.getElementById('heDurasiOtomatis').checked;
    const autoMenit = heAutoMenit();
    if (auto && autoMenit !== null) heSetDurasi(autoMenit);
    document.getElementById('heDurasiAutoInfo').textContent = autoMenit === null
        ? 'Isi Mulai & Selesai untuk menghitung otomatis.'
        : 'Hitungan dari Mulai–Selesai: ' + autoMenit + ' menit';
    document.getElementById('heDurasiInputs').style.opacity = auto ? '0.5' : '1';
    ['heHari', 'heJam', 'heMenit'].forEach(id => { document.getElementById(id).disabled = auto; });
}

async function openHistoryEdit(id) {
    try {
        const res = await fetch('/history-checkups/' + id + '/edit-data', { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) throw new Error();
        const d = result.data;

        document.getElementById('heId').value = d.id;
        document.getElementById('heTitle').textContent = d.kode_barang + ' - ' + d.nama;
        document.getElementById('heTanggalTerjadwal').value = d.tanggal_terjadwal || '';
        document.getElementById('heTanggalCheckup').value = d.tanggal_checkup || '';
        document.getElementById('heMulai').value = d.mulai_perbaikan || '';
        document.getElementById('heSelesai').value = d.waktu_selesai || '';
        document.getElementById('heLine').value = d.line || '';
        document.getElementById('heCatatan').value = d.catatan_umum || '';

        // Toggle otomatis aktif kalau durasi tersimpan = selisih mulai-selesai
        heSetDurasi(d.durasi_perbaikan);
        const autoMenit = heAutoMenit();
        document.getElementById('heDurasiOtomatis').checked = autoMenit !== null && autoMenit === Number(d.durasi_perbaikan);
        renderHeDurasi();

        animateModal(document.getElementById('historyEditModal'), true);
    } catch (e) {
        Swal.fire('Error!', 'Gagal memuat data history', 'error');
    }
}

// Animasi masuk/keluar modal edit (latar memudar + kartu membesar), sama seperti modal lain di aplikasi
function animateModal(modal, show) {
    const card = modal.firstElementChild;
    clearTimeout(modal._hideTimer);
    if (show) {
        modal.style.display = 'flex';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            modal.style.backgroundColor = 'rgba(0,0,0,.5)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
        }));
    } else {
        modal.style.backgroundColor = 'rgba(0,0,0,0)';
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px) scale(.95)';
        modal._hideTimer = setTimeout(() => { modal.style.display = 'none'; }, 300);
    }
}

function closeHistoryEdit() {
    const modal = document.getElementById('historyEditModal');
    if (modal) animateModal(modal, false);
}

async function submitHistoryEdit() {
    const id = document.getElementById('heId').value;
    const auto = document.getElementById('heDurasiOtomatis').checked;
    const payload = {
        tanggal_terjadwal: document.getElementById('heTanggalTerjadwal').value || null,
        tanggal_checkup: document.getElementById('heTanggalCheckup').value,
        mulai_perbaikan: document.getElementById('heMulai').value || null,
        waktu_selesai: document.getElementById('heSelesai').value || null,
        durasi_otomatis: auto,
        durasi_perbaikan: auto ? null : heGetDurasi(),
        line: document.getElementById('heLine').value || null,
        catatan_umum: document.getElementById('heCatatan').value || null,
    };

    const btn = document.getElementById('heSubmitBtn');
    btn.disabled = true;
    try {
        const res = await fetch('/history-checkups/' + id, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            closeHistoryEdit();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
            loadSummary();
        } else {
            Swal.fire('Error!', data.message || 'Gagal menyimpan', 'error');
        }
    } catch (e) {
        Swal.fire('Error!', 'Gagal menyimpan history!', 'error');
    } finally {
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (!CAN_EDIT_HISTORY) return;
    // Hitungan otomatis ikut berubah saat Mulai/Selesai diubah
    ['heMulai', 'heSelesai'].forEach(id => document.getElementById(id).addEventListener('input', renderHeDurasi));
});

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
// Load pertama sudah ada baris "Loading data..." di tabel, jadi overlay tidak perlu ikut muncul (biar tidak ada 2 loading)
function showLoading(show) {
    if (show && document.getElementById('initialLoadingRow')) return;
    document.getElementById('loadingOverlay').classList.toggle('hidden', !show);
}
function showEmpty(msg) {
    document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="10" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>';
}

document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const editModal = document.getElementById('historyEditModal');
    if (editModal && editModal.style.display === 'flex') closeHistoryEdit();
    else closeDetailModal();
});
</script>
@endpush