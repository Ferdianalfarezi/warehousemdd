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

    {{-- Grafik Pareto (ikut filter di bawah) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Pareto Kategori --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Pareto Kategori Problem</p>
            </div>
            <div style="position:relative;height:280px;">
                <canvas id="paretoKategori"></canvas>
                <div id="paretoKategoriEmpty" style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;" class="text-xs text-gray-400">Belum ada data</div>
            </div>
        </div>

        {{-- Pareto Part No --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Pareto Part No Terbanyak Repair <span class="text-xs font-normal text-gray-400">(Top 10)</span></p>
            </div>
            <div style="position:relative;height:280px;">
                <canvas id="paretoPartNo"></canvas>
                <div id="paretoPartNoEmpty" style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;" class="text-xs text-gray-400">Belum ada data</div>
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
                    placeholder="Cari no, part no, nama...">
            </div>

            {{-- Date Range Filter (Tgl Closed) --}}
            <div class="flex items-center gap-2">
                <label for="dateFromInput" class="text-xs font-medium text-gray-500 whitespace-nowrap">Tgl Closed</label>
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

            {{-- Customer Filter (bisa pilih lebih dari 1) --}}
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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Request</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Pengajuan</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Closed</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Part No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Repair ke-</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    <tr id="initialLoadingRow">
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

@include('history_repairs.detail')

@can('superadmin-only')
{{-- Modal Edit History Repair (khusus superadmin). z-index di atas modal detail (dibuka dari kartu repair) --}}
<div id="repairEditModal" style="display:none;position:fixed;inset:0;z-index:80;background-color:rgba(0,0,0,0);transition:background-color .3s ease;align-items:center;justify-content:center;padding:16px;">
    <div class="bg-white rounded-2xl shadow-2xl w-full" style="max-width:760px;max-height:92vh;overflow-y:auto;opacity:0;transform:translateY(16px) scale(.95);transition:opacity .3s cubic-bezier(.4,0,.2,1),transform .3s cubic-bezier(.4,0,.2,1);">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between" style="position:sticky;top:0;background:#fff;z-index:1;">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit History Request Repair</h3>
                <p id="reTitle" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button type="button" onclick="closeRepairEdit()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="repairEditForm" onsubmit="event.preventDefault(); submitRepairEdit();" class="p-6 space-y-5">
            <input type="hidden" id="reId">

            {{-- Info pengajuan --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Info Pengajuan</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tgl Pengajuan <span class="text-red-500">*</span></label>
                        <input type="date" id="reTanggalPengajuan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Stroke</label>
                        <input type="number" id="reJumlahStroke" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Group</label>
                        <select id="reGroup" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">-</option><option value="A">A</option><option value="B">B</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Shift</label>
                        <select id="reShift" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">-</option><option value="Pagi">Pagi</option><option value="Malam">Malam</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Line / Mesin</label>
                        <input type="text" id="reLineMesin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Process No</label>
                        <input type="text" id="reProcessNo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Customer</label>
                        <input type="text" id="reCustomer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis</label>
                        <select id="reJenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">-</option><option value="Milik Sendiri">Milik Sendiri</option><option value="Eksternal">Eksternal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Problem</label>
                        <select id="reKategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">-</option>
                            @foreach(['Dies', 'Burry', 'Dimensi', 'Human Error', 'Accessories'] as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Detail Proyek</label>
                    <textarea id="reDetailProyek" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                </div>
            </div>

            {{-- Waktu & durasi --}}
            <div style="border:1px solid #fde68a;background-color:#fffbeb;border-radius:12px;padding:14px;">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Waktu & Durasi</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">On Process</label>
                        <input type="datetime-local" id="reOnProcess" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Finish Repair</label>
                        <input type="datetime-local" id="reFinishRepair" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">On Trial</label>
                        <input type="datetime-local" id="reOnTrial" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Closed <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="reClosed" required class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <p id="reDurasiPreview" class="text-xs text-gray-600" style="margin-top:10px;"></p>
            </div>

            {{-- Tindakan & PIC --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tindakan & PIC</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">PIC</label>
                        <input type="text" id="rePic" placeholder="Pisahkan dengan koma, mis. Mekanik 1, Mekanik 2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Analisa Penyebab</label>
                            <textarea id="reAnalisa" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tindakan Perbaikan</label>
                            <textarea id="reTindakan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Rootcause</label>
                            <textarea id="reRootcause" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Recovery</label>
                            <textarea id="reRecovery" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Penggantian Sparepart</label>
                        <textarea id="reCatatanSparepart" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeRepairEdit()" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</button>
                <button type="submit" id="reSubmitBtn" class="px-5 py-2.5 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">Simpan</button>
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
// Edit history hanya untuk superadmin (server juga menolak role lain).
// Ditaruh di window karena tombol Edit dirender oleh script di history_repairs/detail.blade.php
window.CAN_EDIT_HISTORY = @json(Gate::allows('superadmin-only'));

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
// BADGES
// ════════════════════════════════════════════════════════
const KATEGORI_BADGE = {
    'Dies':        'bg-blue-100 text-blue-800',
    'Burry':       'bg-yellow-100 text-yellow-800',
    'Dimensi':     'bg-purple-100 text-purple-800',
    'Human Error': 'bg-red-100 text-red-800',
    'Accessories': 'bg-green-100 text-green-800',
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
        dateTo = '';
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

// Filter berubah -> tabel balik ke halaman 1 + grafik ikut di-update
function onFiltersChanged() {
    currentPage = 1;
    loadData();
    loadSummary();
}

// Query string filter yang sama untuk tabel & grafik
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
// LOAD SUMMARY (grafik Pareto, ikut filter)
// ════════════════════════════════════════════════════════
let summaryRequestId = 0;

// ════════════════════════════════════════════════════════
// EDIT HISTORY REPAIR (superadmin) — dibuka dari kartu repair di modal detail
// ════════════════════════════════════════════════════════
const RE_FIELDS = {
    reTanggalPengajuan: 'tanggal_pengajuan', reJumlahStroke: 'jumlah_stroke', reGroup: 'group', reShift: 'shift',
    reLineMesin: 'line_mesin', reProcessNo: 'process_no', reCustomer: 'customer', reJenis: 'jenis',
    reKategori: 'kategori_problem', reDetailProyek: 'detail_proyek',
    reOnProcess: 'on_process_at', reFinishRepair: 'finish_repair_at', reOnTrial: 'on_trial_at', reClosed: 'closed_at',
    rePic: 'pic_names', reAnalisa: 'analisa_penyebab', reTindakan: 'tindakan_perbaikan',
    reRootcause: 'rootcause', reRecovery: 'recovery', reCatatanSparepart: 'catatan_penggantian_sparepart',
};
let rePausedSeconds = 0;

function reFormatDurasi(sec) {
    if (sec === null || sec === undefined || sec < 0) return '-';
    const d = Math.floor(sec / 86400), h = Math.floor((sec % 86400) / 3600), m = Math.floor((sec % 3600) / 60);
    return [d ? d + ' hari' : '', h ? h + ' jam' : '', m + ' menit'].filter(Boolean).join(' ');
}

// Preview durasi (rumus sama dengan server: waktu pause tidak dihitung)
function renderRepairDurasiPreview() {
    const t = id => document.getElementById(id).value ? new Date(document.getElementById(id).value).getTime() / 1000 : null;
    const p = t('reOnProcess'), fr = t('reFinishRepair'), tr = t('reOnTrial'), c = t('reClosed');
    const repairEnd = fr !== null ? fr : tr; // durasi repair berakhir di Finish Repair (data lama: On Trial)
    const onProcess = (p !== null && repairEnd !== null) ? Math.max(0, repairEnd - p - rePausedSeconds) : null;
    const onTrial = (tr !== null && c !== null) ? c - tr : null;
    const total = (p !== null && c !== null) ? Math.max(0, c - p - rePausedSeconds) : null;
    document.getElementById('reDurasiPreview').innerHTML =
        'Durasi dihitung ulang otomatis — Repair (On Process → Finish Repair): <b>' + reFormatDurasi(onProcess) + '</b> · On Trial: <b>' + reFormatDurasi(onTrial)
        + '</b> · Total: <b>' + reFormatDurasi(total) + '</b>'
        + (rePausedSeconds ? ' <span style="color:#6b7280;">(pause ' + reFormatDurasi(rePausedSeconds) + ' tidak dihitung)</span>' : '');
}

async function openRepairEdit(id) {
    try {
        const res = await fetch('/history-repairs/' + id + '/edit-data', { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) throw new Error();
        const d = result.data;

        document.getElementById('reId').value = d.id;
        document.getElementById('reTitle').textContent = d.no + ' · ' + d.part_no + ' · Repair ke-' + d.repair_count;
        Object.entries(RE_FIELDS).forEach(([elId, key]) => { document.getElementById(elId).value = d[key] ?? ''; });
        rePausedSeconds = Number(d.total_paused_seconds) || 0;
        renderRepairDurasiPreview();

        animateModal(document.getElementById('repairEditModal'), true);
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

function closeRepairEdit() {
    const modal = document.getElementById('repairEditModal');
    if (modal) animateModal(modal, false);
}

async function submitRepairEdit() {
    const id = document.getElementById('reId').value;
    const payload = {};
    Object.entries(RE_FIELDS).forEach(([elId, key]) => { payload[key] = document.getElementById(elId).value || null; });

    const btn = document.getElementById('reSubmitBtn');
    btn.disabled = true;
    try {
        const res = await fetch('/history-repairs/' + id, {
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
            closeRepairEdit();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            openDetailModal(data.part_no); // refresh isi modal detail
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
    if (!window.CAN_EDIT_HISTORY) return;
    ['reOnProcess', 'reFinishRepair', 'reOnTrial', 'reClosed'].forEach(id => document.getElementById(id).addEventListener('input', renderRepairDurasiPreview));
});

async function loadSummary() {
    const requestId = ++summaryRequestId;
    try {
        const res    = await fetch('/history-repairs/summary?' + filterParams(), { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (requestId !== summaryRequestId || !result.success) return; // ada request filter yang lebih baru

        ['paretoKategoriEmpty', 'paretoPartNoEmpty'].forEach(id => { document.getElementById(id).textContent = 'Belum ada data'; });

        renderPareto('paretoKategori', result.by_kategori.map(i => ({ label: i.label, value: i.value })), {
            total: result.total,
            barColor: '#8b5cf6',
            valueLabel: 'Repair',
            emptyId: 'paretoKategoriEmpty',
        });

        renderPareto('paretoPartNo', result.by_part_no.map(i => ({ label: i.label, title: i.title, value: i.value })), {
            total: result.total,
            barColor: '#3b82f6',
            valueLabel: 'Repair',
            emptyId: 'paretoPartNoEmpty',
            onBarClick: item => openDetailModal(item.label),
        });
    } catch (e) {
        if (requestId !== summaryRequestId) return;
        ['paretoKategoriEmpty', 'paretoPartNoEmpty'].forEach(id => {
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
        if (reloadQueued) { reloadQueued = false; loadData(); }
    }
}

// ════════════════════════════════════════════════════════
// RENDER TABLE
// ════════════════════════════════════════════════════════
function renderTable(items) {
    const tbody = document.getElementById('historyTableBody');
    if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Tidak ada data history</p>'
            + '<p class="text-gray-500 text-sm">' + (searchQuery || dateFrom || dateTo || customers.length ? 'Coba kata kunci / tanggal / customer lain' : 'Belum ada repair yang closed') + '</p>'
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
// Load pertama sudah ada baris "Loading data..." di tabel, jadi overlay tidak perlu ikut muncul (biar tidak ada 2 loading)
function showLoading(show) {
    if (show && document.getElementById('initialLoadingRow')) return;
    document.getElementById('loadingOverlay').classList.toggle('hidden', !show);
}
function showEmpty(msg) {
    document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>';
}

document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const editModal = document.getElementById('repairEditModal');
    if (editModal && editModal.style.display === 'flex') closeRepairEdit();
    else closeDetailModal();
});
</script>
@endpush