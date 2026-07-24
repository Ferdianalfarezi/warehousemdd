@extends('layouts.app')

@section('title', 'Request Repair')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Request Repair</h1>
            <p class="text-gray-600 mt-1">Manage All Repair Request Data</p>
        </div>
        <button onclick="openCreateModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add Request</span>
        </button>
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
                    placeholder="Search no, part no, nama...">
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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Grp / Shift</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Part No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Process No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock FG</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Diajukan Oleh</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PIC</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="rrTableBody">
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center">
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

@include('request_repairs.create')
@include('request_repairs.edit')
@include('request_repairs.detail')
@include('request_repairs.select-pic')
@include('request_repairs.additional-info')
@include('request_repairs.closed-info')

@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// AUTH CONTEXT
// ════════════════════════════════════════════════════════
const AUTH_USER_ID = {{ auth()->id() }};

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
// SELECT PIC MODAL STATE
// ════════════════════════════════════════════════════════
let _selectPicId = null;

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL STATE
// ════════════════════════════════════════════════════════
let _additionalInfoId   = null;
let _durasiStartSeconds = 0;
let _durasiStartTime    = null;
let _durasiTimer        = null;

// ════════════════════════════════════════════════════════
// CLOSED INFO MODAL STATE
// ════════════════════════════════════════════════════════
let _closedInfoId = null;

// ════════════════════════════════════════════════════════
// PAUSE REASON LABELS (⬅️ baru)
// ════════════════════════════════════════════════════════
const PAUSE_REASON_LABEL = {
    adjust_dimensi: 'Adjust Dimensi',
    repair_line:    'Repair di Line',
    trial:          'Trial',
    cek_dies:       'Cek Dies',
    meeting:        'Meeting',
};

// ════════════════════════════════════════════════════════
// ICONS
// ════════════════════════════════════════════════════════
function makeBtn(onclick, title, bgColor, bgHoverColor, svgPath) {
    return '<button onclick="' + onclick + '" title="' + title + '"'
         + ' style="background-color:' + bgColor + ';width:32px;height:32px;border-radius:8px;transition:background-color .15s;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:none;cursor:pointer;"'
         + ' onmouseover="this.style.backgroundColor=\'' + bgHoverColor + '\'"'
         + ' onmouseout="this.style.backgroundColor=\'' + bgColor + '\'">'
         + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">'
         + svgPath
         + '</svg></button>';
}

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
const JENIS_BADGE = {
    'Milik Sendiri': 'bg-teal-100 text-teal-800',
    'Eksternal':     'bg-orange-100 text-orange-800',
};
const STATUS_CFG = {
    'open':       { bg: '#dcfce7', color: '#166534', label: 'Open'       }, // hijau
    'on_process': { bg: '#dbeafe', color: '#1e40af', label: 'On Process' },
    'on_trial':   { bg: '#fef9c3', color: '#854d0e', label: 'On Trial'   },
    'closed':     { bg: '#dcfce7', color: '#166534', label: 'Closed'     },
    'paused':     { bg: '#fee2e2', color: '#991b1b', label: 'Paused'     }, // ⬅️ baru — merah, gantiin "On Process" pas lagi di-pause
};

// ⬅️ diubah — terima isPaused & pauseReason, badge status langsung jadi "Paused" (bukan numpuk 2 badge)
function statusBadge(status, isPaused, pauseReason) {
    const key   = isPaused ? 'paused' : status;
    const s     = STATUS_CFG[key] || { bg: '#f3f4f6', color: '#4b5563', label: status };
    const title = isPaused ? ' title="' + esc(PAUSE_REASON_LABEL[pauseReason] || pauseReason || '') + '"' : '';
    return '<span' + title + ' style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:9999px;font-size:12px;font-weight:600;background-color:' + s.bg + ';color:' + s.color + ';cursor:' + (isPaused ? 'help' : 'default') + ';">'
         + s.label + (isPaused ? ' · ' + (PAUSE_REASON_LABEL[pauseReason] || pauseReason) : '') + '</span>';
}

function okngBadge(val) {
    if (!val) return '-';
    const cls = val === 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ' + cls + '">' + val + '</span>';
}

// ════════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    loadData();

    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { searchQuery = this.value; currentPage = 1; loadData(); }, 300);
    });

    document.getElementById('perPageSelect').addEventListener('change', function () {
        perPage = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        loadData();
    });

    document.addEventListener('change', function (e) {
        if (e.target.id === 'createProcessNoSelect') syncProcessNoFromSelect('createProcessNoSelect');
        if (e.target.id === 'editProcessNoSelect')   syncProcessNoFromSelect('editProcessNoSelect');

        // Toggle mode PIC: Sendiri / Bersama Tim
        if (e.target.name === 'picMode') {
            const wrapper = document.getElementById('selectPicTeamWrapper');
            if (e.target.value === 'tim') {
                wrapper.classList.remove('hidden');
                initPicSelect2();
            } else {
                wrapper.classList.add('hidden');
            }
        }

        // Preview gambar create
        if (e.target.id === 'createGambar') {
            const file = e.target.files[0];
            const preview = document.getElementById('createGambarPreview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        // Preview gambar edit
        if (e.target.id === 'editGambar') {
            const file = e.target.files[0];
            const preview = document.getElementById('editGambarPreview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }
    });
});

function waitForJQuery(cb, maxWait = 5000) {
    const start = Date.now();
    const check = () => {
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) cb();
        else if (Date.now() - start < maxWait) setTimeout(check, 50);
        else cb();
    };
    check();
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
        const res    = await fetch('/request-repairs/data?' + params, {
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
    const tbody = document.getElementById('rrTableBody');
    if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Tidak ada data ditemukan</p>'
            + '<p class="text-gray-500 text-sm">' + (searchQuery ? 'Coba kata kunci lain' : 'Klik "Add Request" untuk memulai') + '</p>'
            + '</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(function (r) {
         var btns = '';
            btns += makeBtn('openDetailModal(' + r.id + ')', 'Detail', '#eab308', '#ca8a04',
                '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>');
            if (r.can_edit) {
                btns += makeBtn('openEditModal(' + r.id + ')', 'Edit', '#f97316', '#ea580c',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>');
            }
            if (r.can_to_process) {
                btns += makeBtn('confirmStatus(' + r.id + ', \'on_process\')', 'Proses Request', '#0048e8', '#4f46e5',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>');
            }
            if (r.can_to_on_trial) {
                btns += makeBtn('confirmStatus(' + r.id + ', \'on_trial\')', 'Konfirmasi ke On Trial', '#16a34a', '#15803d',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>');
            }
            if (r.can_to_closed) {
                btns += makeBtn('confirmStatus(' + r.id + ', \'closed\')', 'Konfirmasi ke Closed', '#16a34a', '#15803d',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>');
            }
            if (r.can_delete) {
                btns += makeBtn('deleteRR(' + r.id + ')', 'Hapus', '#ef4444', '#dc2626',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>');
            }
        var katCls = KATEGORI_BADGE[r.kategori_problem] || 'bg-gray-100 text-gray-700';

        // ⬅️ baru — row jadi merah muda kalau lagi paused
        var rowCls = 'hover:bg-gray-50 transition ' + (r.status === 'closed' ? 'opacity-70' : '') + (r.is_paused ? ' bg-red-50 hover:bg-red-100' : '');

        return '<tr class="' + rowCls + '">'
             + '<td class="px-4 py-3 text-sm text-gray-500">' + r.row_number + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900">' + esc(r.no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + (esc(r.tanggal_pengajuan) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><span class="font-semibold">' + esc(r.group) + '</span> / ' + esc(r.shift) + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono text-gray-900">' + esc(r.part_no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-800 max-w-xs truncate" title="' + esc(r.process_no) + '">' + esc(r.process_no || '-') + '</td>'
             + '<td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + katCls + '">' + esc(r.kategori_problem) + '</span></td>'
             + '<td class="px-4 py-3 text-sm text-gray-700 text-center font-medium">' + (r.kekuatan_stock_fg ?? '-') + ' Hari</td>' 
             + '<td class="px-4 py-3 text-sm text-gray-600">' + esc(r.created_by_name || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="' + esc(r.pic_names || '-') + '">' + esc(r.pic_names || '-') + '</td>'
             + '<td class="px-4 py-3">' + statusBadge(r.status, r.is_paused, r.pause_reason) + '</td>'
             + '<td class="px-4 py-3"><div class="flex items-center justify-center space-x-1">' + btns + '</div></td>'
             + '</tr>';
    }).join('');
}

// ════════════════════════════════════════════════════════
// CONFIRM STATUS
// ════════════════════════════════════════════════════════
async function confirmStatus(id, newStatus) {
    if (newStatus === 'on_process') { await openSelectPicModal(id); return; }
    if (newStatus === 'on_trial')   { await openAdditionalInfoModal(id); return; }
    if (newStatus === 'closed')     { await openClosedInfoModal(id);     return; }
}

// ════════════════════════════════════════════════════════
// SELECT PIC MODAL — open (Open → On Process)
// ════════════════════════════════════════════════════════
async function openSelectPicModal(id) {
    _selectPicId = id;

    // Reset mode ke "Sendiri", sembunyikan pilihan tim
    document.querySelectorAll('input[name="picMode"]').forEach(r => r.checked = (r.value === 'sendiri'));
    document.getElementById('selectPicTeamWrapper').classList.add('hidden');
    const errEl = document.getElementById('errorSelectPicTeam');
    errEl.textContent = ''; errEl.classList.add('hidden');

    waitForJQuery(function () {
        try { $('#selectPicTeamSelect').val(null).trigger('change'); } catch (e) {}
    });

    const btn = document.getElementById('submitSelectPicBtn');
    btn.disabled = false;
    btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Proses Request</span>';

    const modal   = document.getElementById('selectPicModal');
    const content = document.getElementById('selectPicContent');
    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));

    try {
        const res    = await fetch('/request-repairs/' + id);
        const result = await res.json();
        if (result.success) document.getElementById('selectPicNo').textContent = result.data.no || '';
    } catch (e) { /* no-op */ }
}

function closeSelectPicModal() {
    const modal = document.getElementById('selectPicModal'), content = document.getElementById('selectPicContent');
    content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}
function handleSelectPicBackdrop(e) {
    if (e.target === document.getElementById('selectPicModal')) closeSelectPicModal();
}

// ════════════════════════════════════════════════════════
// SELECT2 PIC CANDIDATES (role_id 1 & 7)
// ════════════════════════════════════════════════════════
function initPicSelect2() {
    waitForJQuery(function () {
        try { $('#selectPicTeamSelect').select2('destroy'); } catch (e) {}
        $('#selectPicTeamSelect').select2({
            placeholder: 'Cari nama / NIK...',
            allowClear: true,
            width: '100%',
            multiple: true,
            minimumInputLength: 0,
            dropdownParent: $('#selectPicModal'),
            ajax: {
                url: '/request-repairs/pic-candidates',
                dataType: 'json',
                delay: 250,
                data: function (p) { return { q: p.term || '' }; },
                processResults: function (d) { return { results: d.results }; },
                cache: true,
            },
        });
    });
}

// ════════════════════════════════════════════════════════
// SELECT PIC MODAL — submit
// ════════════════════════════════════════════════════════
async function submitSelectPic() {
    const mode = document.querySelector('input[name="picMode"]:checked')?.value || 'sendiri';
    const errEl = document.getElementById('errorSelectPicTeam');
    errEl.textContent = ''; errEl.classList.add('hidden');

    let picUserIds = [AUTH_USER_ID];

    if (mode === 'tim') {
        waitForJQuery(function () {}); // pastikan select2 sudah siap sebelum dibaca
        const selected = ($('#selectPicTeamSelect').val() || []).map(v => parseInt(v, 10));
        if (selected.length === 0) {
            errEl.textContent = 'Pilih minimal 1 anggota tim, atau pilih mode "Sendiri".';
            errEl.classList.remove('hidden');
            return;
        }
        picUserIds = Array.from(new Set([AUTH_USER_ID, ...selected]));
    }

    const btn = document.getElementById('submitSelectPicBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Memproses...</span>';

    try {
        const res = await fetch('/request-repairs/' + _selectPicId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: 'on_process', pic_user_ids: picUserIds }),
        });
        const data = await res.json();
        if (data.success) {
            closeSelectPicModal();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Proses Request</span>';
        }
    } catch (e) {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Proses Request</span>';
    }
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — open
// ════════════════════════════════════════════════════════
async function openAdditionalInfoModal(id) {
    _additionalInfoId = id;

    // Reset section 1
    document.getElementById('additionalAnalisaPenyebab').value   = '';
    document.getElementById('additionalTindakanPerbaikan').value = '';
    document.getElementById('additionalCatatanSparepart').value  = '';
    // Reset section 2
    document.getElementById('additionalItem').value           = '';
    document.getElementById('additionalProsesGrinding').value = '';
    document.getElementById('additionalShimUp').value         = '';
    document.getElementById('additionalGroupLeader').value    = '';
    document.getElementById('additionalOperator').value       = '';
    document.querySelectorAll('input[name="additionalStatusBurry"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="additionalStandartBurry"]').forEach(r => r.checked = false);
    // Reset section 3
    document.getElementById('additionalPlan').value   = '';
    document.getElementById('additionalActual').value = '';
    document.getElementById('additionalRemark').value = '';
    document.querySelectorAll('input[name="additionalJudge"]').forEach(r => r.checked = false);
    // Reset errors
    ['AnalisaPenyebab', 'TindakanPerbaikan', 'CatatanSparepart'].forEach(k => {
        const el = document.getElementById('errorAdditional' + k);
        if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    // Reset durasi & button
    document.getElementById('durasiDisplay').textContent = 'Menghitung...';
    const btn = document.getElementById('submitAdditionalInfoBtn');
    btn.disabled = false;
    btn.classList.remove('opacity-50', 'cursor-not-allowed');
    btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Konfirmasi On Trial</span>';

    // ⬅️ baru — reset UI pause/resume ke kondisi netral setiap buka modal
    resetPauseUI();

    const modal   = document.getElementById('additionalInfoModal');
    const content = document.getElementById('additionalInfoContent');
    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));

    try {
        const res    = await fetch('/request-repairs/' + id);
        const result = await res.json();
        if (result.success) document.getElementById('additionalInfoNo').textContent = result.data.no || '';

        // ⬅️ diubah — pakai applyDurasiState() biar status paused ke-handle otomatis
        await refreshDurasiState();
    } catch (e) {
        document.getElementById('durasiDisplay').textContent = '—';
    }
}

// ════════════════════════════════════════════════════════
// DURASI LIVE TIMER
// ════════════════════════════════════════════════════════
function startDurasiTimer() {
    stopDurasiTimer();
    updateDurasiDisplay();
    _durasiTimer = setInterval(updateDurasiDisplay, 1000);
}
function stopDurasiTimer() {
    if (_durasiTimer) { clearInterval(_durasiTimer); _durasiTimer = null; }
}
function updateDurasiDisplay() {
    const elapsed = Math.floor((Date.now() - _durasiStartTime) / 1000);
    document.getElementById('durasiDisplay').textContent = formatDurasiJS(_durasiStartSeconds + elapsed);
}
function formatDurasiJS(seconds) {
    const days = Math.floor(seconds / 86400), hours = Math.floor((seconds % 86400) / 3600),
          mins = Math.floor((seconds % 3600) / 60), secs = seconds % 60, parts = [];
    if (days)  parts.push(days  + ' hari');
    if (hours) parts.push(hours + ' jam');
    if (mins)  parts.push(mins  + ' menit');
    if (secs && !days) parts.push(secs + ' detik');
    return parts.length ? parts.join(' ') : '< 1 menit';
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — submit
// ════════════════════════════════════════════════════════
async function submitAdditionalInfo() {
    // ⬅️ baru — guard tambahan di frontend, backend juga sudah menolak ini
    const submitBtnCheck = document.getElementById('submitAdditionalInfoBtn');
    if (submitBtnCheck && submitBtnCheck.disabled) return;

    ['AnalisaPenyebab', 'TindakanPerbaikan', 'CatatanSparepart'].forEach(k => {
        const el = document.getElementById('errorAdditional' + k);
        if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });

    const analisaPenyebab   = document.getElementById('additionalAnalisaPenyebab').value.trim();
    const tindakanPerbaikan = document.getElementById('additionalTindakanPerbaikan').value.trim();
    const catatanSparepart  = document.getElementById('additionalCatatanSparepart').value.trim();
    const item           = document.getElementById('additionalItem').value.trim();
    const prosesGrinding = document.getElementById('additionalProsesGrinding').value.trim();
    const shimUp         = document.getElementById('additionalShimUp').value.trim();
    const statusBurry    = document.querySelector('input[name="additionalStatusBurry"]:checked')?.value || '';
    const standartBurry  = document.querySelector('input[name="additionalStandartBurry"]:checked')?.value || '';
    const groupLeader    = document.getElementById('additionalGroupLeader').value.trim();
    const operator       = document.getElementById('additionalOperator').value.trim();
    const plan   = document.getElementById('additionalPlan').value.trim();
    const actual = document.getElementById('additionalActual').value.trim();
    const remark = document.getElementById('additionalRemark').value.trim();
    const judge  = document.querySelector('input[name="additionalJudge"]:checked')?.value || '';

    let hasError = false;
    if (!analisaPenyebab) {
        const el = document.getElementById('errorAdditionalAnalisaPenyebab');
        el.textContent = 'Analisa penyebab wajib diisi.'; el.classList.remove('hidden'); hasError = true;
    }
    if (!tindakanPerbaikan) {
        const el = document.getElementById('errorAdditionalTindakanPerbaikan');
        el.textContent = 'Tindakan perbaikan wajib diisi.'; el.classList.remove('hidden'); hasError = true;
    }
    if (!catatanSparepart) {
        const el = document.getElementById('errorAdditionalCatatanSparepart');
        el.textContent = 'Catatan penggantian sparepart wajib diisi.'; el.classList.remove('hidden'); hasError = true;
    }
    if (hasError) return;

    const btn = document.getElementById('submitAdditionalInfoBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan...</span>';
    stopDurasiTimer();

    try {
        const res = await fetch('/request-repairs/' + _additionalInfoId + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({
                status: 'on_trial',
                analisa_penyebab: analisaPenyebab, tindakan_perbaikan: tindakanPerbaikan,
                catatan_penggantian_sparepart: catatanSparepart,
                item: item || null, proses_grinding: prosesGrinding || null, shim_up: shimUp || null,
                status_burry: statusBurry || null, standart_burry: standartBurry || null,
                group_leader: groupLeader || null, operator: operator || null,
                plan: plan || null, actual: actual || null, remark: remark || null, judge: judge || null,
            }),
        });
        const data = await res.json();
        if (data.success) {
            closeAdditionalInfoModal();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else {
            if (data.errors) {
                const fieldMap = {
                    'analisa_penyebab': 'errorAdditionalAnalisaPenyebab',
                    'tindakan_perbaikan': 'errorAdditionalTindakanPerbaikan',
                    'catatan_penggantian_sparepart': 'errorAdditionalCatatanSparepart',
                };
                Object.keys(fieldMap).forEach(key => {
                    if (data.errors[key]) { const el = document.getElementById(fieldMap[key]); if (el) { el.textContent = data.errors[key][0]; el.classList.remove('hidden'); } }
                });
            } else { Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error'); }
            btn.disabled = false;
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Konfirmasi On Trial</span>';
            startDurasiTimer();
        }
    } catch (e) {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Konfirmasi On Trial</span>';
        startDurasiTimer();
    }
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — close
// ════════════════════════════════════════════════════════
function closeAdditionalInfoModal() {
    stopDurasiTimer();
    const modal = document.getElementById('additionalInfoModal'), content = document.getElementById('additionalInfoContent');
    content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}
function handleAdditionalInfoBackdrop(e) {
    if (e.target === document.getElementById('additionalInfoModal')) closeAdditionalInfoModal();
}

// ════════════════════════════════════════════════════════
// CLOSED INFO MODAL — open
// ════════════════════════════════════════════════════════
async function openClosedInfoModal(id) {
    _closedInfoId = id;

    // Reset Hasil Akhir
    document.querySelectorAll('input[name="closedHasilAkhir"]').forEach(r => r.checked = false);
    const errHasilAkhir = document.getElementById('errorClosedHasilAkhir');
    errHasilAkhir.textContent = ''; errHasilAkhir.classList.add('hidden');
    const ngWarning = document.getElementById('hasilAkhirNgWarning');
    if (ngWarning) ngWarning.classList.add('hidden');

    // Reset section 1 — Monitoring Dies Temporary
    document.getElementById('closedTanggalCek').value    = '';
    document.getElementById('closedLotProd').value       = '';
    document.getElementById('closedRemarkMonitoring').value = '';
    document.querySelectorAll('input[name="closedAwal"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="closedTengah"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="closedAkhir"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="closedQty"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="closedJudgeMonitoring"]').forEach(r => r.checked = false);

    // Reset section 2 — Target Permanen Action
    document.getElementById('closedPlanPermanen').value    = '';
    document.getElementById('closedActualPermanen').value  = '';
    document.getElementById('closedRootcause').value       = '';
    document.getElementById('closedRecovery').value        = '';
    document.getElementById('closedAssyTrialCheck').value  = '';
    document.querySelectorAll('input[name="closedJudgePermanen"]').forEach(r => r.checked = false);

    // Reset button
    const btn = document.getElementById('submitClosedInfoBtn');
    btn.disabled = false;
    btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Konfirmasi Closed</span>';

    const modal   = document.getElementById('closedInfoModal');
    const content = document.getElementById('closedInfoContent');
    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));

    try {
        const res    = await fetch('/request-repairs/' + id);
        const result = await res.json();
        if (result.success) document.getElementById('closedInfoNo').textContent = result.data.no || '';
    } catch (e) { /* no-op */ }
}

// ════════════════════════════════════════════════════════
// CLOSED INFO MODAL — submit
// ════════════════════════════════════════════════════════
async function submitClosedInfo() {
    const errHasilAkhir = document.getElementById('errorClosedHasilAkhir');
    errHasilAkhir.textContent = ''; errHasilAkhir.classList.add('hidden');

    const hasilAkhir = document.querySelector('input[name="closedHasilAkhir"]:checked')?.value || '';
    if (!hasilAkhir) {
        errHasilAkhir.textContent = 'Pilih Hasil Akhir (OK / NG) terlebih dahulu.';
        errHasilAkhir.classList.remove('hidden');
        return;
    }

    // Section 1 — Monitoring Dies Temporary
    const tanggalCek       = document.getElementById('closedTanggalCek').value;
    const lotProd          = document.getElementById('closedLotProd').value.trim();
    const awal             = document.querySelector('input[name="closedAwal"]:checked')?.value || '';
    const tengah           = document.querySelector('input[name="closedTengah"]:checked')?.value || '';
    const akhir            = document.querySelector('input[name="closedAkhir"]:checked')?.value || '';
    const qty              = document.querySelector('input[name="closedQty"]:checked')?.value || '';
    const remarkMonitoring = document.getElementById('closedRemarkMonitoring').value.trim();
    const judgeMonitoring  = document.querySelector('input[name="closedJudgeMonitoring"]:checked')?.value || '';

    // Section 2 — Target Permanen Action
    const planPermanen   = document.getElementById('closedPlanPermanen').value.trim();
    const actualPermanen = document.getElementById('closedActualPermanen').value.trim();
    const rootcause      = document.getElementById('closedRootcause').value.trim();
    const recovery       = document.getElementById('closedRecovery').value.trim();
    const assyTrialCheck = document.getElementById('closedAssyTrialCheck').value.trim();
    const judgePermanen  = document.querySelector('input[name="closedJudgePermanen"]:checked')?.value || '';

    const btn = document.getElementById('submitClosedInfoBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan...</span>';

    try {
        const res = await fetch('/request-repairs/' + _closedInfoId + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({
                status: 'closed',
                hasil_akhir: hasilAkhir,
                // Section 1
                tanggal_cek:       tanggalCek      || null,
                lot_prod:          lotProd         || null,
                awal:              awal            || null,
                tengah:            tengah          || null,
                akhir:             akhir           || null,
                qty:               qty             || null,
                remark_monitoring: remarkMonitoring|| null,
                judge_monitoring:  judgeMonitoring || null,
                // Section 2
                plan_permanen:     planPermanen    || null,
                actual_permanen:   actualPermanen  || null,
                rootcause:         rootcause       || null,
                recovery:          recovery        || null,
                assy_trial_check:  assyTrialCheck  || null,
                judge_permanen:    judgePermanen   || null,
            }),
        });
        const data = await res.json();

        if (data.success) {
            closeClosedInfoModal();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Konfirmasi Closed</span>';
        }
    } catch (e) {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Konfirmasi Closed</span>';
    }
}

// ════════════════════════════════════════════════════════
// CLOSED INFO MODAL — close
// ════════════════════════════════════════════════════════
function closeClosedInfoModal() {
    const modal = document.getElementById('closedInfoModal'), content = document.getElementById('closedInfoContent');
    content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}
function handleClosedInfoBackdrop(e) {
    if (e.target === document.getElementById('closedInfoModal')) closeClosedInfoModal();
}

// ════════════════════════════════════════════════════════
// SELECT2 BARANG
// ════════════════════════════════════════════════════════
function initBarangSelect2(selectId, modalId, cb) {
    waitForJQuery(function () {
        try { $('#' + selectId).select2('destroy'); } catch(e) {}
        $('#' + selectId).select2({
            placeholder: 'Cari kode / nama part...', allowClear: true, width: '100%',
            minimumInputLength: 0, dropdownParent: $('#' + modalId),
            ajax: { url: '/request-repairs/search-barang', dataType: 'json', delay: 250,
                    data: function (p) { return { q: p.term || '' }; },
                    processResults: function (d) { return { results: d.results }; }, cache: true },
        })
        .on('select2:select', function (e) { cb(e.params.data); })
        .on('select2:clear',  function ()  { cb(null); });
    });
}

// ════════════════════════════════════════════════════════
// PROCESS NO
// ════════════════════════════════════════════════════════
async function loadProcessNos(barangId, selectElId, current) {
    current = current || '';
    const sel = document.getElementById(selectElId);
    sel.innerHTML = '<option value="">Loading...</option>';
    try {
        const res  = await fetch('/request-repairs/process-nos?barang_id=' + barangId);
        const data = await res.json();
        let opts = '<option value="">— Pilih Process No —</option>';
        if (data.data && data.data.length) {
            data.data.forEach(function (pn) { opts += '<option value="' + esc(pn) + '"' + (pn === current ? ' selected' : '') + '>' + esc(pn) + '</option>'; });
        } else { opts = '<option value="">Tidak ada process no terdaftar</option>'; }
        sel.innerHTML = opts; sel.value = current; syncProcessNoFromSelect(selectElId);
    } catch (e) { sel.innerHTML = '<option value="">Gagal load process no</option>'; }
}
function syncProcessNoFromSelect(selectElId) {
    const prefix = selectElId.replace('ProcessNoSelect', '');
    const inputEl = document.getElementById(prefix + 'ProcessNoInput');
    if (!inputEl || !inputEl.classList.contains('hidden')) return;
    inputEl.value = document.getElementById(selectElId).value || '';
}
function toggleManualProcessNo(prefix) {
    const sel = document.getElementById(prefix + 'ProcessNoSelect'), inp = document.getElementById(prefix + 'ProcessNoInput'), btn = document.getElementById(prefix + 'ProcessNoToggleBtn');
    if (!inp.classList.contains('hidden')) { inp.classList.add('hidden'); sel.classList.remove('hidden'); btn.textContent = 'Manual'; inp.value = sel.value || ''; }
    else { sel.classList.add('hidden'); inp.classList.remove('hidden'); btn.textContent = 'Pilih List'; inp.value = ''; inp.focus(); }
}
function getProcessNoValue(prefix) {
    const inp = document.getElementById(prefix + 'ProcessNoInput'), sel = document.getElementById(prefix + 'ProcessNoSelect');
    return inp.classList.contains('hidden') ? (sel.value || '') : (inp.value || '');
}

// ════════════════════════════════════════════════════════
// CREATE MODAL
// ════════════════════════════════════════════════════════
function openCreateModal() {
    modalShow('createModal');
    document.getElementById('createForm').reset();
    document.getElementById('createNamaDisplay').value     = '';
    document.getElementById('createCustomerDisplay').value = '';
    document.getElementById('createProcessNoSelect').innerHTML = '<option value="">— Pilih dulu Part No —</option>';
    document.getElementById('createProcessNoInput').value  = '';
    document.getElementById('createProcessNoInput').classList.add('hidden');
    document.getElementById('createProcessNoSelect').classList.remove('hidden');
    document.getElementById('createProcessNoToggleBtn').textContent = 'Manual';
    document.getElementById('createGambarPreview').classList.add('hidden');
    clearErrors();
    initBarangSelect2('createBarangId', 'createModal', function (d) {
        if (d) {
            document.getElementById('createNamaDisplay').value     = d.nama     || '';
            document.getElementById('createCustomerDisplay').value = d.customer || '';
            loadProcessNos(d.id, 'createProcessNoSelect');
        } else {
            document.getElementById('createNamaDisplay').value     = '';
            document.getElementById('createCustomerDisplay').value = '';
            document.getElementById('createProcessNoSelect').innerHTML = '<option value="">— Pilih dulu Part No —</option>';
            document.getElementById('createProcessNoInput').value  = '';
        }
    });
    document.getElementById('createForm').onsubmit = async function (e) {
        e.preventDefault(); clearErrors();
        const fd = new FormData(this); fd.set('process_no', getProcessNoValue('create'));
        try {
            const res  = await fetch('/request-repairs', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: fd });
            const data = await res.json();
            if (data.success) { await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 }); closeCreateModal(); loadData(); }
            else { if (data.errors) displayErrors(data.errors, 'create'); else Swal.fire('Error!', data.message || 'Unknown error', 'error'); }
        } catch (err) { Swal.fire('Error!', 'Terjadi kesalahan', 'error'); }
    };
}
function closeCreateModal() {
    waitForJQuery(function () { try { $('#createBarangId').select2('destroy'); } catch(e) {} });
    modalHide('createModal');
}

// ════════════════════════════════════════════════════════
// EDIT MODAL
// ════════════════════════════════════════════════════════
async function openEditModal(id) {
    modalShow('editModal');
    try {
        const res = await fetch('/request-repairs/' + id), result = await res.json();
        if (!result.success) throw new Error();
        const r = result.data;
        document.getElementById('editRrId').value            = r.id;
        document.getElementById('editModalNo').textContent   = r.no;
        document.getElementById('editTanggal').value         = r.tanggal_pengajuan ? r.tanggal_pengajuan.substring(0,10) : '';
        document.getElementById('editGroup').value           = r.group;
        document.getElementById('editShift').value           = r.shift;
        document.getElementById('editStroke').value          = r.jumlah_stroke;
        document.getElementById('editLineId').value = r.line_id || '';
        document.getElementById('editNamaDisplay').value     = r.nama       || '';
        document.getElementById('editCustomerDisplay').value = r.customer   || '';
        document.getElementById('editJenis').value           = r.jenis;
        document.getElementById('editKategori').value        = r.kategori_problem;
        document.getElementById('editKekuatanStockFg').value = r.kekuatan_stock_fg ?? '';
        document.getElementById('editDetailProyek').value    = r.detail_proyek || '';
        document.getElementById('editStatusBadge').innerHTML = statusBadge(r.status, r.is_paused, r.pause_reason);
        document.getElementById('editProcessNoInput').classList.add('hidden');
        document.getElementById('editProcessNoSelect').classList.remove('hidden');
        document.getElementById('editProcessNoToggleBtn').textContent = 'Manual';
        document.getElementById('editProcessNoInput').value = r.process_no || '';

        // Gambar existing
        const editGambarCurrent  = document.getElementById('editGambarCurrent');
        const editGambarEmptyTxt = document.getElementById('editGambarEmptyText');
        if (r.gambar_url) {
            editGambarCurrent.src = r.gambar_url;
            editGambarCurrent.classList.remove('hidden');
            editGambarEmptyTxt.classList.add('hidden');
        } else {
            editGambarCurrent.classList.add('hidden');
            editGambarEmptyTxt.classList.remove('hidden');
        }
        document.getElementById('editGambarPreview').classList.add('hidden');
        document.getElementById('editGambar').value = '';

        waitForJQuery(function () {
            try { $('#editBarangId').select2('destroy'); } catch(e) {}
            $('#editBarangId').empty().append(new Option(r.part_no + ' — ' + r.nama, r.barang_id, true, true));
            $('#editBarangId').select2({
                placeholder: 'Cari kode / nama part...', allowClear: true, width: '100%', minimumInputLength: 0, dropdownParent: $('#editModal'),
                ajax: { url: '/request-repairs/search-barang', dataType: 'json', delay: 250, data: function (p) { return { q: p.term || '' }; }, processResults: function (d) { return { results: d.results }; }, cache: true },
            })
            .on('select2:select', function (e) { const d = e.params.data; document.getElementById('editNamaDisplay').value = d.nama || ''; document.getElementById('editCustomerDisplay').value = d.customer || ''; loadProcessNos(d.id, 'editProcessNoSelect'); })
            .on('select2:clear', function () { document.getElementById('editNamaDisplay').value = ''; document.getElementById('editCustomerDisplay').value = ''; document.getElementById('editProcessNoSelect').innerHTML = '<option value="">— Pilih dulu Part No —</option>'; document.getElementById('editProcessNoInput').value = ''; });
        });
        await loadProcessNos(r.barang_id, 'editProcessNoSelect', r.process_no || '');
        clearErrors();
        document.getElementById('editForm').onsubmit = async function (e) {
            e.preventDefault(); clearErrors();
            const fd = new FormData(this); fd.append('_method', 'PUT'); fd.set('process_no', getProcessNoValue('edit'));
            try {
                const res  = await fetch('/request-repairs/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: fd });
                const data = await res.json();
                if (data.success) { await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 }); closeEditModal(); loadData(); }
                else { if (data.errors) displayErrors(data.errors, 'edit'); else Swal.fire('Error!', data.message || 'Unknown error', 'error'); }
            } catch (err) { Swal.fire('Error!', 'Terjadi kesalahan!', 'error'); }
        };
    } catch (e) { modalHide('editModal'); Swal.fire('Error!', 'Gagal memuat data', 'error'); }
}
function closeEditModal() {
    waitForJQuery(function () { try { $('#editBarangId').select2('destroy'); } catch(e) {} });
    modalHide('editModal');
}

// ════════════════════════════════════════════════════════
// DETAIL MODAL
// ════════════════════════════════════════════════════════
async function openDetailModal(id) {
    const modal = document.getElementById('detailModal'), content = document.getElementById('detailModalContent');
    modal.style.display = 'flex';
    content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
    try {
        const res = await fetch('/request-repairs/' + id), result = await res.json();
        if (!result.success) throw new Error();
        const r = result.data, tl = r.timeline || {};
        const fmt = (s) => s ? s.substring(0,10).split('-').reverse().join('/') : '-';
        const fmtDT = (iso) => {
            if (!iso) return '-';
            const d = new Date(iso);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        };

        document.getElementById('detailNo').textContent         = r.no;
        document.getElementById('detailTanggal').textContent    = fmt(r.tanggal_pengajuan);
        document.getElementById('detailGroupShift').textContent = r.group + ' / ' + r.shift;
        document.getElementById('detailStroke').textContent     = r.jumlah_stroke ? Number(r.jumlah_stroke).toLocaleString('id-ID') : '-';
        document.getElementById('detailLineMesin').textContent  = r.line_mesin  || '-';
        document.getElementById('detailPartNo').textContent     = r.part_no     || '-';
        document.getElementById('detailNama').textContent       = r.process_no  || '-';
        document.getElementById('detailProcessNo').textContent  = r.process_no  || '-';
        document.getElementById('detailCustomer').textContent   = r.customer    || '-';
        document.getElementById('detailKekuatanStockFg').textContent = r.kekuatan_stock_fg ?? '-';
        document.getElementById('detailProyek').textContent     = r.detail_proyek || '-';
        document.getElementById('detailStatus').innerHTML       = statusBadge(r.status, r.is_paused, r.pause_reason);
        document.getElementById('detailJenis').innerHTML        = '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + (JENIS_BADGE[r.jenis] || 'bg-gray-100 text-gray-700') + '">' + esc(r.jenis) + '</span>';
        document.getElementById('detailKategori').innerHTML     = '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + (KATEGORI_BADGE[r.kategori_problem] || 'bg-gray-100 text-gray-700') + '">' + esc(r.kategori_problem) + '</span>';
        

        // ── Dibuat Oleh & Gambar ──
        document.getElementById('detailCreatedBy').textContent = r.created_by_name || '-';
        const detailGambarEl    = document.getElementById('detailGambar');
        const detailGambarEmpty = document.getElementById('detailGambarEmpty');
        if (r.gambar_url) {
            detailGambarEl.src = r.gambar_url;
            detailGambarEl.classList.remove('hidden');
            detailGambarEmpty.classList.add('hidden');
        } else {
            detailGambarEl.classList.add('hidden');
            detailGambarEmpty.classList.remove('hidden');
        }

        // ── On Trial sections ──
        const hasSection1 = r.analisa_penyebab || r.tindakan_perbaikan || r.catatan_penggantian_sparepart;
        const hasSection2 = r.item || r.proses_grinding || r.shim_up || r.status_burry || r.standart_burry || r.group_leader || r.operator;
        const hasSection3 = r.plan || r.actual || r.remark || r.judge;
        if (hasSection1 || hasSection2 || hasSection3) {
            document.getElementById('detailAnalisaPenyebab').textContent             = r.analisa_penyebab              || '-';
            document.getElementById('detailTindakanPerbaikan').textContent           = r.tindakan_perbaikan            || '-';
            document.getElementById('detailCatatanPenggantianSparepart').textContent = r.catatan_penggantian_sparepart || '-';
            document.getElementById('detailItem').textContent            = r.item            || '-';
            document.getElementById('detailProsesGrinding').textContent  = r.proses_grinding || '-';
            document.getElementById('detailShimUp').textContent          = r.shim_up         || '-';
            document.getElementById('detailGroupLeader').textContent     = r.group_leader    || '-';
            document.getElementById('detailOperator').textContent        = r.operator        || '-';
            document.getElementById('detailStatusBurry').innerHTML       = okngBadge(r.status_burry);
            document.getElementById('detailStandartBurry').innerHTML     = okngBadge(r.standart_burry);
            document.getElementById('detailPlan').textContent   = r.plan   || '-';
            document.getElementById('detailActual').textContent = r.actual || '-';
            document.getElementById('detailRemark').textContent = r.remark || '-';
            document.getElementById('detailJudge').innerHTML    = okngBadge(r.judge);
            document.getElementById('detailAdditionalInfo').classList.remove('hidden');
        } else {
            document.getElementById('detailAdditionalInfo').classList.add('hidden');
        }

        // ── Closed sections ──
        const hasClosed1 = r.tanggal_cek || r.lot_prod || r.awal || r.tengah || r.akhir || r.qty || r.remark_monitoring || r.judge_monitoring;
        const hasClosed2 = r.plan_permanen || r.actual_permanen || r.rootcause || r.recovery || r.assy_trial_check || r.judge_permanen;
        if (hasClosed1 || hasClosed2) {
            document.getElementById('detailTanggalCek').textContent      = r.tanggal_cek ? r.tanggal_cek.substring(0,10).split('-').reverse().join('/') : '-';
            document.getElementById('detailLotProd').textContent         = r.lot_prod          || '-';
            document.getElementById('detailAwal').innerHTML              = okngBadge(r.awal);
            document.getElementById('detailTengah').innerHTML            = okngBadge(r.tengah);
            document.getElementById('detailAkhir').innerHTML             = okngBadge(r.akhir);
            document.getElementById('detailQty').innerHTML               = okngBadge(r.qty);
            document.getElementById('detailRemarkMonitoring').textContent= r.remark_monitoring || '-';
            document.getElementById('detailJudgeMonitoring').innerHTML   = okngBadge(r.judge_monitoring);
            document.getElementById('detailPlanPermanen').textContent    = r.plan_permanen    || '-';
            document.getElementById('detailActualPermanen').textContent  = r.actual_permanen  || '-';
            document.getElementById('detailRootcause').textContent       = r.rootcause        || '-';
            document.getElementById('detailRecovery').textContent        = r.recovery         || '-';
            document.getElementById('detailAssyTrialCheck').textContent  = r.assy_trial_check || '-';
            document.getElementById('detailJudgePermanen').innerHTML     = okngBadge(r.judge_permanen);
            document.getElementById('detailClosedInfo').classList.remove('hidden');
        } else {
            document.getElementById('detailClosedInfo').classList.add('hidden');
        }

        // ── Timeline ──
        if (tl.on_trial_at) {
            document.getElementById('detailTimeline').classList.remove('hidden');
            document.getElementById('timelineOnProcessAt').textContent = fmtDT(tl.on_process_at);
            if (tl.durasi_on_process) { document.getElementById('timelineDurasiOnProcessVal').textContent = tl.durasi_on_process; document.getElementById('timelineDurasiOnProcess').classList.remove('hidden'); }
            document.getElementById('timelineOnTrialRow').classList.remove('hidden');
            document.getElementById('timelineOnTrialAt').textContent = fmtDT(tl.on_trial_at);
            if (tl.durasi_on_trial) { document.getElementById('timelineDurasiOnTrialVal').textContent = tl.durasi_on_trial; document.getElementById('timelineDurasiOnTrial').classList.remove('hidden'); }
            if (tl.closed_at) { document.getElementById('timelineClosedRow').classList.remove('hidden'); document.getElementById('timelineClosedAt').textContent = fmtDT(tl.closed_at); }
            if (tl.durasi_total) { document.getElementById('timelineDurasiTotal').textContent = tl.durasi_total; document.getElementById('timelineTotalRow').classList.remove('hidden'); }
        } else {
            document.getElementById('detailTimeline').classList.add('hidden');
        }

        requestAnimationFrame(() => requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100');
        }));
    } catch (e) { modal.style.display = 'none'; Swal.fire('Error!', 'Gagal memuat detail', 'error'); }
}
function closeDetailModal() {
    const modal = document.getElementById('detailModal'), content = document.getElementById('detailModalContent');
    content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}
function handleDetailBackdrop(e) {
    if (e.target === document.getElementById('detailModal')) closeDetailModal();
}

// ════════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════════
async function deleteRR(id) {
    const result = await Swal.fire({ title: 'Yakin hapus?', text: 'Data akan dihapus permanent!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' });
    if (!result.isConfirmed) return;
    try {
        const res  = await fetch('/request-repairs/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const data = await res.json();
        if (data.success) { await Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1500 }); loadData(); }
        else Swal.fire('Error!', data.message, 'error');
    } catch (e) { Swal.fire('Error!', 'Gagal menghapus!', 'error'); }
}

// ════════════════════════════════════════════════════════
// PAGINATION
// ════════════════════════════════════════════════════════
function renderPagination(p) {
    const container = document.getElementById('paginationContainer');
    totalPages = p.total_pages;
    if (totalPages <= 1) { container.innerHTML = ''; return; }
    const b = 'px-3 py-1.5 rounded-lg border text-xs transition', bA = 'bg-black text-white border-black font-semibold', bN = 'border-gray-300 text-gray-700 hover:bg-gray-50', bD = 'border-gray-200 text-gray-400 cursor-not-allowed';
    const arL = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
    const arR = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
    let html = '<button onclick="goToPage(' + (currentPage-1) + ')" ' + (currentPage===1?'disabled':'') + ' class="' + b + ' ' + (currentPage===1?bD:bN) + '">' + arL + '</button>';
    const max=5; let start=Math.max(1,currentPage-Math.floor(max/2)), end=Math.min(totalPages,start+max-1);
    if(end-start<max-1) start=Math.max(1,end-max+1);
    if(start>1){html+='<button onclick="goToPage(1)" class="'+b+' '+bN+'">1</button>';if(start>2)html+='<span class="px-1 text-gray-500 text-xs">...</span>';}
    for(let i=start;i<=end;i++) html+='<button onclick="goToPage('+i+')" class="'+b+' '+(i===currentPage?bA:bN)+'">'+i+'</button>';
    if(end<totalPages){if(end<totalPages-1)html+='<span class="px-1 text-gray-500 text-xs">...</span>';html+='<button onclick="goToPage('+totalPages+')" class="'+b+' '+bN+'">'+totalPages+'</button>';}
    html+='<button onclick="goToPage('+(currentPage+1)+')" '+(currentPage===totalPages?'disabled':'')+' class="'+b+' '+(currentPage===totalPages?bD:bN)+'">'+arR+'</button>';
    container.innerHTML = html;
}
function goToPage(page) { if (page < 1 || page > totalPages || page === currentPage) return; currentPage = page; loadData(); }
function updateShowingInfo(p) { document.getElementById('showingFrom').textContent = p.from; document.getElementById('showingTo').textContent = p.to; document.getElementById('totalEntries').textContent = p.total; }

// ════════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════════
function esc(str) { if (!str && str !== 0) return ''; const d = document.createElement('div'); d.textContent = String(str); return d.innerHTML; }
function showLoading(show) { document.getElementById('loadingOverlay').classList.toggle('hidden', !show); }
function showEmpty(msg) { document.getElementById('rrTableBody').innerHTML = '<tr><td colspan="11" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>'; }
function modalShow(id) { const el = document.getElementById(id); el.style.display = 'flex'; requestAnimationFrame(function () { requestAnimationFrame(function () { el.classList.add('modal-fade-in'); }); }); }
function modalHide(id) { const el = document.getElementById(id); el.classList.remove('modal-fade-in'); setTimeout(function () { el.style.display = 'none'; }, 300); }
function clearErrors() { document.querySelectorAll('.error-message').forEach(function (el) { el.textContent = ''; }); }
function displayErrors(errors, prefix) { Object.keys(errors).forEach(function (key) { const el = document.getElementById('error-' + prefix + '-' + key); if (el) el.textContent = errors[key][0]; }); }

// ESC key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeCreateModal(); closeEditModal(); closeDetailModal(); closeSelectPicModal(); closeAdditionalInfoModal(); closeClosedInfoModal(); }
});
</script>
@endpush