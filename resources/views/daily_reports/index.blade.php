@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daily Report</h1>
            <p class="text-gray-600 mt-1">Aktivitas harian tercatat otomatis dari Request Repair &amp; General Checkup</p>
        </div>
        <button onclick="openItemModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Aktivitas</span>
        </button>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-3 md:space-y-0">
            <div class="w-full md:w-56">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Kerja</label>
                <input type="date" id="filterTanggal"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
            </div>

            @if($isAdmin)
            <div class="w-full md:w-72">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Karyawan</label>
                <select id="filterUser"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">— Diri Sendiri —</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}{{ $m->nik ? ' ('.$m->nik.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-center space-x-2">
                <button onclick="shiftDay(-1)" title="Hari sebelumnya"
                    class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="goToday()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Hari Ini
                </button>
                <button onclick="shiftDay(1)" title="Hari berikutnya"
                    class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div id="timelineBody" class="divide-y divide-gray-100 relative min-h-[200px]">
            <div class="px-6 py-16 text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-4 text-gray-500">Menyinkronkan aktivitas...</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH / EDIT AKTIVITAS                          --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div id="itemModal" style="display:none;" onclick="handleItemBackdrop(event)"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 overflow-y-auto">
    <div id="itemModalContent"
        class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0">

        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 id="itemModalTitle" class="text-lg font-bold text-gray-900">Tambah Aktivitas</h3>
                <p id="itemModalSub" class="text-sm text-gray-500 mt-0.5"></p>
            </div>
            <button onclick="closeItemModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            {{-- Info baris terkunci --}}
            <div id="itemLockedNotice" class="hidden bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <p class="text-sm text-amber-800">
                    Baris ini tercatat otomatis dari sistem, jadi jam &amp; kategorinya terkunci.
                    Kamu tetap bisa menambahkan catatan di bawah.
                </p>
            </div>

            <div id="itemEditableFields" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" id="itemMulai"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        <p class="error-message text-red-500 text-xs mt-1" id="error-item-mulai"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" id="itemSelesai"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        <p class="error-message text-red-500 text-xs mt-1" id="error-item-selesai"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select id="itemKategori"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        <option value="">— Pilih kategori —</option>
                        @foreach($kategoriManual as $k)
                            <option value="{{ $k }}">{{ $kategoriLabels[$k] ?? $k }}</option>
                        @endforeach
                    </select>
                    <p class="error-message text-red-500 text-xs mt-1" id="error-item-kategori"></p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Keterangan</label>
                <textarea id="itemKeterangan" rows="3" placeholder="Deskripsi aktivitas..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"></textarea>
                <p class="error-message text-red-500 text-xs mt-1" id="error-item-keterangan"></p>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3 rounded-b-2xl">
            <button onclick="closeItemModal()"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button onclick="submitItem()" id="submitItemBtn"
                class="bg-black text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-800 transition flex items-center space-x-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════════
const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};

let currentData   = null;   // payload terakhir dari server
let currentItemId = null;   // null = mode tambah
let isLoading     = false;

// ════════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('filterTanggal').value = tanggalKerjaHariIni();

    document.getElementById('filterTanggal').addEventListener('change', loadData);
    if (IS_ADMIN) document.getElementById('filterUser').addEventListener('change', loadData);

    loadData();
});

// Cutoff 06:00 — jam 00:00-05:59 masih dianggap tanggal kerja kemarin
function tanggalKerjaHariIni() {
    const d = new Date();
    if (d.getHours() < 6) d.setDate(d.getDate() - 1);
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function goToday() {
    document.getElementById('filterTanggal').value = tanggalKerjaHariIni();
    loadData();
}

function shiftDay(delta) {
    const el = document.getElementById('filterTanggal');
    const d  = new Date(el.value + 'T12:00:00');
    d.setDate(d.getDate() + delta);
    el.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    loadData();
}

// ════════════════════════════════════════════════════════
// LOAD DATA
// ════════════════════════════════════════════════════════
async function loadData() {
    if (isLoading) return;
    isLoading = true;
    showSkeleton();

    const params = new URLSearchParams({ tanggal: document.getElementById('filterTanggal').value });
    if (IS_ADMIN) {
        const uid = document.getElementById('filterUser').value;
        if (uid) params.set('user_id', uid);
    }

    try {
        const res    = await fetch('/daily-reports/data?' + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const result = await res.json();

        if (result.success) {
            currentData = result.data;
            render(result.data);
        } else {
            showError(result.message || 'Gagal memuat data');
        }
    } catch (e) {
        showError('Error memuat data. Coba refresh halaman.');
    } finally {
        isLoading = false;
    }
}

// ════════════════════════════════════════════════════════
// RENDER
// ════════════════════════════════════════════════════════
function render(data) {
    renderTimeline(data);
}

function renderTimeline(data) {
    const body = document.getElementById('timelineBody');

    // Gabung item + gap, urut by waktu
    const rows = [];
    data.items.forEach(it => rows.push({ t: new Date(it.mulai_at).getTime(), kind: 'item', d: it }));
    data.gaps.forEach(g  => rows.push({ t: new Date(g.mulai_at).getTime(),  kind: 'gap',  d: g  }));
    rows.sort((a, b) => a.t - b.t);

    if (rows.length === 0) {
        body.innerHTML = '<div class="px-6 py-16 text-center">'
            + '<p class="text-gray-600 font-semibold">Belum ada aktivitas tercatat</p>'
            + '<p class="text-gray-500 text-sm mt-1">Aktivitas akan muncul otomatis saat Request Repair diproses.</p>'
            + '</div>';
        return;
    }

    body.innerHTML = rows.map(row =>
        row.kind === 'gap' ? renderGapRow(row.d) : renderItemRow(row.d)
    ).join('');
}

function renderItemRow(it) {
    const w = it.warna || { bg: '#f3f4f6', text: '#4b5563', bar: '#9ca3af' };

    // Referensi part / no request
    let ref = '';
    if (it.ref_no || it.ref_part_no) {
        const parts = [];
        if (it.ref_no)      parts.push(esc(it.ref_no));
        if (it.ref_part_no) parts.push(esc(it.ref_part_no));
        ref = '<span class="text-xs font-mono text-gray-500">' + parts.join(' · ') + '</span>';
    }

    // Flag kecil
    let flags = '';
    if (it.is_running)  flags += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">berjalan</span>';
    if (it.is_overtime) flags += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-800">overtime</span>';
    if (it.is_overlap)  flags += '<span title="Bentrok dengan aktivitas lain" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-800">overlap</span>';

    // Tombol aksi
    let actions = '';
    if (it.is_auto) {
        actions += '<button onclick="openItemModal(' + it.id + ')" title="Tambah catatan"'
                +  ' class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">'
                +  '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'
                +  '</button>';
    } else {
        actions += '<button onclick="openItemModal(' + it.id + ')" title="Edit"'
                +  ' class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-orange-600 hover:bg-orange-50 transition">'
                +  '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
                +  '</button>'
                +  '<button onclick="deleteItem(' + it.id + ')" title="Hapus"'
                +  ' class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-red-600 hover:bg-red-50 transition">'
                +  '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
                +  '</button>';
    }

    return '<div class="flex items-stretch hover:bg-gray-50 transition group">'
         + '<div style="width:4px;background-color:' + w.bar + ';flex-shrink:0;"></div>'
         + '<div class="flex-1 px-5 py-3.5 flex items-center gap-4 min-w-0">'

         // Jam
         + '<div class="w-28 flex-shrink-0">'
         + '<p class="text-sm font-semibold text-gray-900 font-mono">' + it.mulai + ' – ' + (it.selesai || '…') + '</p>'
         + '<p class="text-xs text-gray-500 mt-0.5">' + it.durasi + '</p>'
         + '</div>'

         // Kategori
         + '<div class="w-36 flex-shrink-0">'
         + '<span style="background-color:' + w.bg + ';color:' + w.text + ';" class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold">'
         + esc(it.kategori_label) + '</span>'
         + '</div>'

         // Detail
         + '<div class="flex-1 min-w-0">'
         + '<p class="text-sm text-gray-800 truncate">' + esc(it.ref_nama || it.keterangan || '-') + '</p>'
         + '<div class="flex items-center gap-2 mt-1 flex-wrap">' + ref + flags + '</div>'
         + (it.ref_nama && it.keterangan
             ? '<p class="text-xs text-gray-500 mt-1 italic">' + esc(it.keterangan) + '</p>' : '')
         + '</div>'

         // Aksi
         + '<div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">' + actions + '</div>'
         + '</div></div>';
}

function renderGapRow(g) {
    return '<div class="flex items-stretch bg-gray-50/60">'
         + '<div style="width:4px;flex-shrink:0;background:repeating-linear-gradient(180deg,#d1d5db 0 4px,transparent 4px 8px);"></div>'
         + '<div class="flex-1 px-5 py-2.5 flex items-center gap-4">'
         + '<div class="w-28 flex-shrink-0"><p class="text-sm text-gray-400 font-mono">' + g.mulai + ' – ' + g.selesai + '</p></div>'
         + '<div class="flex-1 text-sm text-gray-400 italic">Tidak tercatat · ' + g.durasi + '</div>'
         + '<button onclick="openItemModal(null, \'' + g.mulai + '\', \'' + g.selesai + '\')"'
         + ' class="text-xs font-semibold text-gray-600 border border-gray-300 rounded-lg px-3 py-1.5 hover:bg-white hover:border-gray-400 transition flex-shrink-0">'
         + '+ Isi</button>'
         + '</div></div>';
}

function showSkeleton() {
    document.getElementById('timelineBody').innerHTML =
        '<div class="px-6 py-16 text-center">'
      + '<svg class="animate-spin h-8 w-8 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24">'
      + '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>'
      + '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>'
      + '</svg><p class="mt-4 text-gray-500">Menyinkronkan aktivitas...</p></div>';
}

function showError(msg) {
    document.getElementById('timelineBody').innerHTML =
        '<div class="px-6 py-16 text-center text-gray-500">' + esc(msg) + '</div>';
}

// ════════════════════════════════════════════════════════
// MODAL ITEM
// ════════════════════════════════════════════════════════
function openItemModal(itemId, prefillMulai, prefillSelesai) {
    currentItemId = itemId || null;

    clearItemErrors();
    document.getElementById('itemMulai').value      = prefillMulai   || '';
    document.getElementById('itemSelesai').value    = prefillSelesai || '';
    document.getElementById('itemKategori').value   = '';
    document.getElementById('itemKeterangan').value = '';
    document.getElementById('itemLockedNotice').classList.add('hidden');
    document.getElementById('itemEditableFields').classList.remove('hidden');
    document.getElementById('itemModalSub').textContent = '';

    if (currentItemId && currentData) {
        const it = currentData.items.find(x => x.id === currentItemId);
        if (it) {
            document.getElementById('itemModalTitle').textContent = it.is_auto ? 'Catatan Aktivitas' : 'Edit Aktivitas';
            document.getElementById('itemModalSub').textContent   =
                it.mulai + ' – ' + (it.selesai || '…') + ' · ' + it.kategori_label;
            document.getElementById('itemKeterangan').value = it.keterangan || '';

            if (it.is_auto) {
                document.getElementById('itemLockedNotice').classList.remove('hidden');
                document.getElementById('itemEditableFields').classList.add('hidden');
            } else {
                document.getElementById('itemMulai').value    = it.mulai;
                document.getElementById('itemSelesai').value  = it.selesai || '';
                document.getElementById('itemKategori').value = it.kategori;
            }
        }
    } else {
        document.getElementById('itemModalTitle').textContent = 'Tambah Aktivitas';
    }

    const modal   = document.getElementById('itemModal');
    const content = document.getElementById('itemModalContent');
    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));
}

function closeItemModal() {
    const modal = document.getElementById('itemModal'), content = document.getElementById('itemModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

function handleItemBackdrop(e) {
    if (e.target === document.getElementById('itemModal')) closeItemModal();
}

function clearItemErrors() {
    ['mulai', 'selesai', 'kategori', 'keterangan'].forEach(k => {
        const el = document.getElementById('error-item-' + k);
        if (el) el.textContent = '';
    });
}

async function submitItem() {
    clearItemErrors();

    const isLockedEdit = document.getElementById('itemEditableFields').classList.contains('hidden');

    const payload = { keterangan: document.getElementById('itemKeterangan').value.trim() || null };

    if (!isLockedEdit) {
        payload.mulai    = document.getElementById('itemMulai').value;
        payload.selesai  = document.getElementById('itemSelesai').value;
        payload.kategori = document.getElementById('itemKategori').value;

        let err = false;
        if (!payload.mulai)    { document.getElementById('error-item-mulai').textContent    = 'Jam mulai wajib diisi.';   err = true; }
        if (!payload.selesai)  { document.getElementById('error-item-selesai').textContent  = 'Jam selesai wajib diisi.'; err = true; }
        if (!payload.kategori) { document.getElementById('error-item-kategori').textContent = 'Pilih kategori.';          err = true; }
        if (err) return;
    }

    let url    = '/daily-reports/items';
    let method = 'POST';

    if (currentItemId) {
        url    = '/daily-reports/items/' + currentItemId;
        method = 'PUT';
    } else {
        payload.tanggal = document.getElementById('filterTanggal').value;
        if (IS_ADMIN) {
            const uid = document.getElementById('filterUser').value;
            if (uid) payload.user_id = uid;
        }
    }

    const btn = document.getElementById('submitItemBtn');
    btn.disabled  = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan...</span>';

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            closeItemModal();
            if (data.data) { currentData = data.data; render(data.data); }
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1400 });
        } else if (data.errors) {
            Object.keys(data.errors).forEach(k => {
                const el = document.getElementById('error-item-' + k);
                if (el) el.textContent = data.errors[k][0];
            });
        } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
        }
    } catch (e) {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Simpan</span>';
    }
}

// ════════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════════
async function deleteItem(id) {
    const ok = await Swal.fire({
        title: 'Hapus aktivitas?', text: 'Baris ini akan dihapus permanen.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    });
    if (!ok.isConfirmed) return;

    try {
        const res = await fetch('/daily-reports/items/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.success) {
            if (data.data) { currentData = data.data; render(data.data); }
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1400 });
        } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
        }
    } catch (e) {
        Swal.fire('Error!', 'Gagal menghapus!', 'error');
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

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeItemModal();
});
</script>
@endpush