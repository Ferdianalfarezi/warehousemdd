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
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="rrTableBody">
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

@include('request_repairs.create')
@include('request_repairs.edit')
@include('request_repairs.detail')
@include('request_repairs.additional-info')
@include('request_repairs.closed-info')

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
// ICONS
// ════════════════════════════════════════════════════════
function makeBtn(onclick, title, bgClass, svgPath) {
    return '<button onclick="' + onclick + '" title="' + title + '"'
         + ' class="' + bgClass + ' text-white w-8 h-8 rounded-lg transition flex items-center justify-center flex-shrink-0">'
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
    'on_process': { cls: 'bg-blue-100 text-blue-800',     label: 'On Process' },
    'on_trial':   { cls: 'bg-yellow-100 text-yellow-800', label: 'On Trial'   },
    'closed':     { cls: 'bg-green-100 text-green-800',   label: 'Closed'     },
};

function statusBadge(status) {
    const s = STATUS_CFG[status] || { cls: 'bg-gray-100 text-gray-600', label: status };
    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ' + s.cls + '">'
         + s.label + '</span>';
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
        tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Tidak ada data ditemukan</p>'
            + '<p class="text-gray-500 text-sm">' + (searchQuery ? 'Coba kata kunci lain' : 'Klik "Add Request" untuk memulai') + '</p>'
            + '</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(function (r) {
        var btns = '';

        btns += makeBtn(
            'openDetailModal(' + r.id + ')',
            'Detail',
            'bg-yellow-500 hover:bg-yellow-600',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
        );

        if (r.can_edit) {
            btns += makeBtn(
                'openEditModal(' + r.id + ')',
                'Edit',
                'bg-orange-500 hover:bg-orange-600',
                '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
            );
        }

        if (r.can_to_on_trial) {
            btns += makeBtn(
                'confirmStatus(' + r.id + ', \'on_trial\')',
                'Konfirmasi ke On Trial',
                'bg-green-600 hover:bg-green-700',
                '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            );
        }

        if (r.can_to_closed) {
            btns += makeBtn(
                'confirmStatus(' + r.id + ', \'closed\')',
                'Konfirmasi ke Closed',
                'bg-green-600 hover:bg-green-700',
                '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
            );
        }

        if (r.can_delete) {
            btns += makeBtn(
                'deleteRR(' + r.id + ')',
                'Hapus',
                'bg-red-500 hover:bg-red-600',
                '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
            );
        }

        var katCls = KATEGORI_BADGE[r.kategori_problem] || 'bg-gray-100 text-gray-700';

        return '<tr class="hover:bg-gray-50 transition ' + (r.status === 'closed' ? 'opacity-70' : '') + '">'
             + '<td class="px-4 py-3 text-sm text-gray-500">' + r.row_number + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900">' + esc(r.no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">' + (esc(r.tanggal_pengajuan) || '-') + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><span class="font-semibold">' + esc(r.group) + '</span> / ' + esc(r.shift) + '</td>'
             + '<td class="px-4 py-3 text-sm font-mono text-gray-900">' + esc(r.part_no) + '</td>'
             + '<td class="px-4 py-3 text-sm text-gray-800 max-w-xs truncate" title="' + esc(r.nama) + '">' + esc(r.nama) + '</td>'
             + '<td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + katCls + '">' + esc(r.kategori_problem) + '</span></td>'
             + '<td class="px-4 py-3">' + statusBadge(r.status) + '</td>'
             + '<td class="px-4 py-3"><div class="flex items-center justify-center space-x-1">' + btns + '</div></td>'
             + '</tr>';
    }).join('');
}

// ════════════════════════════════════════════════════════
// CONFIRM STATUS
// ════════════════════════════════════════════════════════
async function confirmStatus(id, newStatus) {
    if (newStatus === 'on_trial') {
        await openAdditionalInfoModal(id);
        return;
    }

    if (newStatus === 'closed') {
        await openClosedInfoModal(id);
        return;
    }
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — open
// ════════════════════════════════════════════════════════
async function openAdditionalInfoModal(id) {
    _additionalInfoId = id;

    document.getElementById('additionalPenyebabVc').value = '';
    document.querySelectorAll('input[name="additionalTindakan"]').forEach(r => r.checked = false);
    document.getElementById('errorAdditionalPenyebabVc').classList.add('hidden');
    document.getElementById('errorAdditionalTindakanRepair').classList.add('hidden');
    document.getElementById('durasiDisplay').textContent = 'Menghitung...';

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
        if (result.success) {
            document.getElementById('additionalInfoNo').textContent = result.data.no || '';
        }

        const dRes  = await fetch('/request-repairs/' + id + '/durasi');
        const dData = await dRes.json();
        if (dData.success) {
            _durasiStartSeconds = dData.seconds;
            _durasiStartTime    = Date.now();
            startDurasiTimer();
        }
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
    const total   = _durasiStartSeconds + elapsed;
    document.getElementById('durasiDisplay').textContent = formatDurasiJS(total);
}

function formatDurasiJS(seconds) {
    const days    = Math.floor(seconds / 86400);
    const hours   = Math.floor((seconds % 86400) / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs    = seconds % 60;
    const parts   = [];
    if (days)    parts.push(days    + ' hari');
    if (hours)   parts.push(hours   + ' jam');
    if (minutes) parts.push(minutes + ' menit');
    if (secs && !days) parts.push(secs + ' detik');
    return parts.length ? parts.join(' ') : '< 1 menit';
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — submit
// ════════════════════════════════════════════════════════
async function submitAdditionalInfo() {
    document.getElementById('errorAdditionalPenyebabVc').classList.add('hidden');
    document.getElementById('errorAdditionalTindakanRepair').classList.add('hidden');

    const penyebab = document.getElementById('additionalPenyebabVc').value.trim();
    const tindakan = document.querySelector('input[name="additionalTindakan"]:checked')?.value || '';

    let hasError = false;
    if (!penyebab) {
        const el = document.getElementById('errorAdditionalPenyebabVc');
        el.textContent = 'Penyebab (VC) wajib diisi.';
        el.classList.remove('hidden');
        hasError = true;
    }
    if (!tindakan) {
        const el = document.getElementById('errorAdditionalTindakanRepair');
        el.textContent = 'Tindakan wajib dipilih.';
        el.classList.remove('hidden');
        hasError = true;
    }
    if (hasError) return;

    const btn = document.getElementById('submitAdditionalInfoBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan...</span>';

    stopDurasiTimer();

    try {
        const res  = await fetch('/request-repairs/' + _additionalInfoId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                status:          'on_trial',
                penyebab_vc:     penyebab,
                tindakan_repair: tindakan,
            }),
        });
        const data = await res.json();

        if (data.success) {
            closeAdditionalInfoModal();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else {
            if (data.errors) {
                if (data.errors.penyebab_vc) {
                    const el = document.getElementById('errorAdditionalPenyebabVc');
                    el.textContent = data.errors.penyebab_vc[0];
                    el.classList.remove('hidden');
                }
                if (data.errors.tindakan_repair) {
                    const el = document.getElementById('errorAdditionalTindakanRepair');
                    el.textContent = data.errors.tindakan_repair[0];
                    el.classList.remove('hidden');
                }
            } else {
                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
            }
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Konfirmasi On Trial</span>';
            startDurasiTimer();
        }
    } catch (e) {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Konfirmasi On Trial</span>';
        startDurasiTimer();
    }
}

// ════════════════════════════════════════════════════════
// ADDITIONAL INFO MODAL — close
// ════════════════════════════════════════════════════════
function closeAdditionalInfoModal() {
    stopDurasiTimer();
    const modal   = document.getElementById('additionalInfoModal');
    const content = document.getElementById('additionalInfoContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
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

    document.querySelectorAll('input[name="closedStatusAfterTrial"]').forEach(r => r.checked = false);
    document.getElementById('closedPointVerifikasi').value       = '';
    document.getElementById('closedApprovalSectionChief').value  = '';
    document.getElementById('errorClosedStatusAfterTrial').classList.add('hidden');
    document.getElementById('errorClosedPointVerifikasi').classList.add('hidden');
    document.getElementById('errorClosedApprovalSectionChief').classList.add('hidden');

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
        if (result.success) {
            document.getElementById('closedInfoNo').textContent = result.data.no || '';
        }
    } catch (e) {
        // no-op
    }
}

// ════════════════════════════════════════════════════════
// CLOSED INFO MODAL — submit
// ════════════════════════════════════════════════════════
async function submitClosedInfo() {
    document.getElementById('errorClosedStatusAfterTrial').classList.add('hidden');
    document.getElementById('errorClosedPointVerifikasi').classList.add('hidden');
    document.getElementById('errorClosedApprovalSectionChief').classList.add('hidden');

    const statusAfterTrial     = document.querySelector('input[name="closedStatusAfterTrial"]:checked')?.value || '';
    const pointVerifikasi      = document.getElementById('closedPointVerifikasi').value.trim();
    const approvalSectionChief = document.getElementById('closedApprovalSectionChief').value.trim();

    let hasError = false;
    if (!statusAfterTrial) {
        const el = document.getElementById('errorClosedStatusAfterTrial');
        el.textContent = 'Status after trial wajib dipilih.';
        el.classList.remove('hidden');
        hasError = true;
    }
    if (!pointVerifikasi) {
        const el = document.getElementById('errorClosedPointVerifikasi');
        el.textContent = 'Point verifikasi wajib diisi.';
        el.classList.remove('hidden');
        hasError = true;
    }
    if (!approvalSectionChief) {
        const el = document.getElementById('errorClosedApprovalSectionChief');
        el.textContent = 'Approval section chief wajib diisi.';
        el.classList.remove('hidden');
        hasError = true;
    }
    if (hasError) return;

    const btn = document.getElementById('submitClosedInfoBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan...</span>';

    try {
        const res  = await fetch('/request-repairs/' + _closedInfoId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                status:                  'closed',
                status_after_trial:      statusAfterTrial,
                point_verifikasi:        pointVerifikasi,
                approval_section_chief:  approvalSectionChief,
            }),
        });
        const data = await res.json();

        if (data.success) {
            closeClosedInfoModal();
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else {
            if (data.errors) {
                if (data.errors.status_after_trial) {
                    const el = document.getElementById('errorClosedStatusAfterTrial');
                    el.textContent = data.errors.status_after_trial[0];
                    el.classList.remove('hidden');
                }
                if (data.errors.point_verifikasi) {
                    const el = document.getElementById('errorClosedPointVerifikasi');
                    el.textContent = data.errors.point_verifikasi[0];
                    el.classList.remove('hidden');
                }
                if (data.errors.approval_section_chief) {
                    const el = document.getElementById('errorClosedApprovalSectionChief');
                    el.textContent = data.errors.approval_section_chief[0];
                    el.classList.remove('hidden');
                }
            } else {
                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
            }
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
    const modal   = document.getElementById('closedInfoModal');
    const content = document.getElementById('closedInfoContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
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
            data.data.forEach(function (pn) {
                opts += '<option value="' + esc(pn) + '"' + (pn === current ? ' selected' : '') + '>' + esc(pn) + '</option>';
            });
        } else {
            opts = '<option value="">Tidak ada process no terdaftar</option>';
        }
        sel.innerHTML = opts;
        sel.value = current;
        syncProcessNoFromSelect(selectElId);
    } catch (e) {
        sel.innerHTML = '<option value="">Gagal load process no</option>';
    }
}

function syncProcessNoFromSelect(selectElId) {
    const prefix  = selectElId.replace('ProcessNoSelect', '');
    const inputEl = document.getElementById(prefix + 'ProcessNoInput');
    if (!inputEl || !inputEl.classList.contains('hidden')) return;
    inputEl.value = document.getElementById(selectElId).value || '';
}

function toggleManualProcessNo(prefix) {
    const sel = document.getElementById(prefix + 'ProcessNoSelect');
    const inp = document.getElementById(prefix + 'ProcessNoInput');
    const btn = document.getElementById(prefix + 'ProcessNoToggleBtn');
    if (!inp.classList.contains('hidden')) {
        inp.classList.add('hidden'); sel.classList.remove('hidden');
        btn.textContent = 'Manual'; inp.value = sel.value || '';
    } else {
        sel.classList.add('hidden'); inp.classList.remove('hidden');
        btn.textContent = 'Pilih List'; inp.value = ''; inp.focus();
    }
}

function getProcessNoValue(prefix) {
    const inp = document.getElementById(prefix + 'ProcessNoInput');
    const sel = document.getElementById(prefix + 'ProcessNoSelect');
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
    document.getElementById('createTanggal').value = new Date().toISOString().split('T')[0];
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
        const fd = new FormData(this);
        fd.set('process_no', getProcessNoValue('create'));
        try {
            const res  = await fetch('/request-repairs', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
                closeCreateModal(); loadData();
            } else {
                if (data.errors) displayErrors(data.errors, 'create');
                else Swal.fire('Error!', data.message || 'Unknown error', 'error');
            }
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
        const res    = await fetch('/request-repairs/' + id);
        const result = await res.json();
        if (!result.success) throw new Error();
        const r = result.data;

        document.getElementById('editRrId').value            = r.id;
        document.getElementById('editModalNo').textContent   = r.no;
        document.getElementById('editTanggal').value         = r.tanggal_pengajuan ? r.tanggal_pengajuan.substring(0,10) : '';
        document.getElementById('editGroup').value           = r.group;
        document.getElementById('editShift').value           = r.shift;
        document.getElementById('editStroke').value          = r.jumlah_stroke;
        document.getElementById('editLineMesin').value       = r.line_mesin || '';
        document.getElementById('editNamaDisplay').value     = r.nama       || '';
        document.getElementById('editCustomerDisplay').value = r.customer   || '';
        document.getElementById('editJenis').value           = r.jenis;
        document.getElementById('editKategori').value        = r.kategori_problem;
        document.getElementById('editTargetSelesai').value   = r.target_selesai ? r.target_selesai.substring(0,10) : '';
        document.getElementById('editDetailProyek').value    = r.detail_proyek || '';
        document.getElementById('editStatusBadge').innerHTML = statusBadge(r.status);

        document.getElementById('editProcessNoInput').classList.add('hidden');
        document.getElementById('editProcessNoSelect').classList.remove('hidden');
        document.getElementById('editProcessNoToggleBtn').textContent = 'Manual';
        document.getElementById('editProcessNoInput').value = r.process_no || '';

        waitForJQuery(function () {
            try { $('#editBarangId').select2('destroy'); } catch(e) {}
            $('#editBarangId').empty().append(new Option(r.part_no + ' — ' + r.nama, r.barang_id, true, true));
            $('#editBarangId').select2({
                placeholder: 'Cari kode / nama part...', allowClear: true, width: '100%',
                minimumInputLength: 0, dropdownParent: $('#editModal'),
                ajax: { url: '/request-repairs/search-barang', dataType: 'json', delay: 250,
                        data: function (p) { return { q: p.term || '' }; },
                        processResults: function (d) { return { results: d.results }; }, cache: true },
            })
            .on('select2:select', function (e) {
                const d = e.params.data;
                document.getElementById('editNamaDisplay').value     = d.nama     || '';
                document.getElementById('editCustomerDisplay').value = d.customer || '';
                loadProcessNos(d.id, 'editProcessNoSelect');
            })
            .on('select2:clear', function () {
                document.getElementById('editNamaDisplay').value     = '';
                document.getElementById('editCustomerDisplay').value = '';
                document.getElementById('editProcessNoSelect').innerHTML = '<option value="">— Pilih dulu Part No —</option>';
                document.getElementById('editProcessNoInput').value  = '';
            });
        });

        await loadProcessNos(r.barang_id, 'editProcessNoSelect', r.process_no || '');
        clearErrors();

        document.getElementById('editForm').onsubmit = async function (e) {
            e.preventDefault(); clearErrors();
            const fd = new FormData(this);
            fd.append('_method', 'PUT');
            fd.set('process_no', getProcessNoValue('edit'));
            try {
                const res  = await fetch('/request-repairs/' + id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await res.json();
                if (data.success) {
                    await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 });
                    closeEditModal(); loadData();
                } else {
                    if (data.errors) displayErrors(data.errors, 'edit');
                    else Swal.fire('Error!', data.message || 'Unknown error', 'error');
                }
            } catch (err) { Swal.fire('Error!', 'Terjadi kesalahan!', 'error'); }
        };
    } catch (e) {
        modalHide('editModal');
        Swal.fire('Error!', 'Gagal memuat data', 'error');
    }
}

function closeEditModal() {
    waitForJQuery(function () { try { $('#editBarangId').select2('destroy'); } catch(e) {} });
    modalHide('editModal');
}

// ════════════════════════════════════════════════════════
// DETAIL MODAL
// ════════════════════════════════════════════════════════
async function openDetailModal(id) {
    const modal   = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');
    modal.style.display = 'flex';
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    try {
        const res    = await fetch('/request-repairs/' + id);
        const result = await res.json();
        if (!result.success) throw new Error();
        const r   = result.data;
        const tl  = r.timeline || {};
        const fmt = (s) => s ? s.substring(0, 10).split('-').reverse().join('/') : '-';
        const fmtDatetime = (iso) => {
            if (!iso) return '-';
            const d = new Date(iso);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                 + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        };

        document.getElementById('detailNo').textContent         = r.no;
        document.getElementById('detailTanggal').textContent    = fmt(r.tanggal_pengajuan);
        document.getElementById('detailGroupShift').textContent = r.group + ' / ' + r.shift;
        document.getElementById('detailStroke').textContent     = r.jumlah_stroke ? Number(r.jumlah_stroke).toLocaleString('id-ID') : '-';
        document.getElementById('detailLineMesin').textContent  = r.line_mesin  || '-';
        document.getElementById('detailPartNo').textContent     = r.part_no     || '-';
        document.getElementById('detailNama').textContent       = r.nama        || '-';
        document.getElementById('detailProcessNo').textContent  = r.process_no  || '-';
        document.getElementById('detailCustomer').textContent   = r.customer    || '-';
        document.getElementById('detailTarget').textContent     = fmt(r.target_selesai);
        document.getElementById('detailProyek').textContent     = r.detail_proyek || '-';
        document.getElementById('detailStatus').innerHTML       = statusBadge(r.status);
        document.getElementById('detailJenis').innerHTML        = '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + (JENIS_BADGE[r.jenis] || 'bg-gray-100 text-gray-700') + '">' + esc(r.jenis) + '</span>';
        document.getElementById('detailKategori').innerHTML     = '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ' + (KATEGORI_BADGE[r.kategori_problem] || 'bg-gray-100 text-gray-700') + '">' + esc(r.kategori_problem) + '</span>';

        // Additional info (on_trial)
        if (r.penyebab_vc || r.tindakan_repair) {
            document.getElementById('detailPenyebabVc').textContent     = r.penyebab_vc    || '-';
            document.getElementById('detailTindakanRepair').textContent = r.tindakan_repair || '-';
            document.getElementById('detailAdditionalInfo').classList.remove('hidden');
        } else {
            document.getElementById('detailAdditionalInfo').classList.add('hidden');
        }

        // Closed info
        if (r.status_after_trial || r.point_verifikasi || r.approval_section_chief) {
            document.getElementById('detailStatusAfterTrial').textContent     = r.status_after_trial     || '-';
            document.getElementById('detailPointVerifikasi').textContent      = r.point_verifikasi       || '-';
            document.getElementById('detailApprovalSectionChief').textContent = r.approval_section_chief || '-';
            document.getElementById('detailClosedInfo').classList.remove('hidden');
        } else {
            document.getElementById('detailClosedInfo').classList.add('hidden');
        }

        // Timeline
        if (tl.on_trial_at) {
            document.getElementById('detailTimeline').classList.remove('hidden');

            document.getElementById('timelineOnProcessAt').textContent = fmtDatetime(tl.on_process_at);
            if (tl.durasi_on_process) {
                document.getElementById('timelineDurasiOnProcessVal').textContent = tl.durasi_on_process;
                document.getElementById('timelineDurasiOnProcess').classList.remove('hidden');
            }

            document.getElementById('timelineOnTrialRow').classList.remove('hidden');
            document.getElementById('timelineOnTrialAt').textContent = fmtDatetime(tl.on_trial_at);
            if (tl.durasi_on_trial) {
                document.getElementById('timelineDurasiOnTrialVal').textContent = tl.durasi_on_trial;
                document.getElementById('timelineDurasiOnTrial').classList.remove('hidden');
            }

            if (tl.closed_at) {
                document.getElementById('timelineClosedRow').classList.remove('hidden');
                document.getElementById('timelineClosedAt').textContent = fmtDatetime(tl.closed_at);
            }

            if (tl.durasi_total) {
                document.getElementById('timelineDurasiTotal').textContent = tl.durasi_total;
                document.getElementById('timelineTotalRow').classList.remove('hidden');
            }
        } else {
            document.getElementById('detailTimeline').classList.add('hidden');
        }

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

function handleDetailBackdrop(e) {
    if (e.target === document.getElementById('detailModal')) closeDetailModal();
}

// ════════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════════
async function deleteRR(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?', text: 'Data akan dihapus permanent!', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
    });
    if (!result.isConfirmed) return;
    try {
        const res  = await fetch('/request-repairs/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1500 });
            loadData();
        } else Swal.fire('Error!', data.message, 'error');
    } catch (e) { Swal.fire('Error!', 'Gagal menghapus!', 'error'); }
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
    document.getElementById('rrTableBody').innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">' + msg + '</td></tr>';
}
function modalShow(id) {
    const el = document.getElementById(id);
    el.style.display = 'flex';
    requestAnimationFrame(function () { requestAnimationFrame(function () { el.classList.add('modal-fade-in'); }); });
}
function modalHide(id) {
    const el = document.getElementById(id);
    el.classList.remove('modal-fade-in');
    setTimeout(function () { el.style.display = 'none'; }, 300);
}
function clearErrors() { document.querySelectorAll('.error-message').forEach(function (el) { el.textContent = ''; }); }
function displayErrors(errors, prefix) {
    Object.keys(errors).forEach(function (key) {
        const el = document.getElementById('error-' + prefix + '-' + key);
        if (el) el.textContent = errors[key][0];
    });
}

// ESC key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeDetailModal();
        closeAdditionalInfoModal();
        closeClosedInfoModal();
    }
});
</script>
@endpush