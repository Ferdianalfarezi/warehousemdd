{{-- ════════════════════════════════════════════════════
    Modal: Detail History Preventive (per Barang)
    File: resources/views/history-checkups/detail-modal.blade.php
════════════════════════════════════════════════════ --}}
<div id="detailModal"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6"
    onclick="handleDetailBackdrop(event)">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div id="detailModalContent"
        class="relative bg-white rounded-[20px] shadow-xl w-full max-w-2xl
               transform transition-all duration-300 scale-95 opacity-0
               overflow-hidden max-h-[88vh] flex flex-col border border-zinc-200">

        {{-- Header --}}
        <div class="bg-white border-b border-zinc-200 px-6 py-5 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-[38px] h-[38px] rounded-[10px] bg-zinc-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[14px] font-medium text-zinc-900 leading-none">History Preventive</p>
                    <p id="detailKodeHeader" class="text-[12px] text-zinc-400 font-mono mt-1"></p>
                </div>
            </div>
            <button onclick="closeDetailModal()"
                class="w-[30px] h-[30px] rounded-lg bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 flex items-center justify-center transition text-zinc-500 hover:text-zinc-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-5">
            <div id="detailLoading" class="flex flex-col items-center justify-center py-16">
                <svg class="animate-spin h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-4 text-gray-500 text-sm">Memuat data...</p>
            </div>

            <div id="detailContent" class="hidden flex flex-col gap-5">
                {{-- Info Barang --}}
                <div class="flex items-center gap-3 pb-4 border-b border-zinc-100">
                    <img id="detailImage" src="" class="w-14 h-14 rounded-xl object-cover border border-zinc-200 hidden">
                    <div id="detailImagePlaceholder" class="w-14 h-14 rounded-xl bg-zinc-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="detailNama" class="text-[16px] font-semibold text-zinc-900 truncate"></p>
                        <p id="detailKode" class="text-[12px] text-zinc-400 font-mono mt-0.5"></p>
                    </div>
                    <div id="detailPeriodeBadge"></div>
                </div>

                {{-- Ringkasan --}}
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-3">Ringkasan</p>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="bg-zinc-50 rounded-xl border border-zinc-200 px-4 py-3">
                            <p class="text-[10px] text-zinc-400 mb-1.5">Total periode</p>
                            <p id="summaryTotalPeriode" class="text-[22px] font-semibold text-zinc-900 leading-none"></p>
                        </div>
                        <div class="bg-green-50 rounded-xl border border-green-200 px-4 py-3">
                            <p class="text-[10px] text-green-600 mb-1.5">Total OK</p>
                            <p id="summaryTotalOk" class="text-[22px] font-semibold text-green-700 leading-none"></p>
                        </div>
                        <div class="bg-red-50 rounded-xl border border-red-200 px-4 py-3">
                            <p class="text-[10px] text-red-500 mb-1.5">Total NG</p>
                            <p id="summaryTotalNg" class="text-[22px] font-semibold text-red-600 leading-none"></p>
                        </div>
                        <div class="bg-orange-50 rounded-xl border border-orange-200 px-4 py-3">
                            <p class="text-[10px] text-orange-600 mb-1.5">Parts dipakai</p>
                            <p id="summaryTotalParts" class="text-[22px] font-semibold text-orange-700 leading-none"></p>
                        </div>
                        <div class="bg-zinc-50 rounded-xl border border-zinc-200 px-4 py-3">
                            <p class="text-[10px] text-zinc-400 mb-1.5">Rata-rata durasi</p>
                            <p id="summaryAvgDurasi" class="text-[13px] font-semibold text-zinc-700 leading-tight pt-1"></p>
                        </div>
                    </div>
                </div>

                {{-- List Periode --}}
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-3">Riwayat per periode</p>
                    <div id="periodeCardList" class="flex flex-col gap-3"></div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-zinc-200 flex-shrink-0 flex justify-end">
            <button onclick="closeDetailModal()"
                class="px-5 py-2 text-[13.5px] font-medium text-white bg-zinc-900 hover:bg-black rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// ════════════════════════════════════════════════════════
// DETAIL MODAL — semua periode preventive 1 barang
// ════════════════════════════════════════════════════════
async function openDetailModal(barangId) {
    if (!barangId) return;

    const modal   = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');

    document.getElementById('detailLoading').classList.remove('hidden');
    document.getElementById('detailContent').classList.add('hidden');
    document.getElementById('periodeCardList').innerHTML = '';
    document.getElementById('detailKodeHeader').textContent = '';

    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));

    try {
        const res    = await fetch('/history-checkups/by-barang?barang_id=' + encodeURIComponent(barangId), { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) throw new Error(result.message || 'Gagal');

        const s       = result.summary;
        const records = result.records;

        // Info barang
        document.getElementById('detailKodeHeader').textContent = s.kode_barang || '';
        document.getElementById('detailNama').textContent       = s.nama || '-';
        document.getElementById('detailKode').textContent       = s.kode_barang || '';

        const imgEl = document.getElementById('detailImage');
        const phEl  = document.getElementById('detailImagePlaceholder');
        if (s.gambar) {
            imgEl.src = '/storage/barangs/' + encodeURIComponent(s.gambar);
            imgEl.classList.remove('hidden');
            phEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            phEl.classList.remove('hidden');
        }

        document.getElementById('detailPeriodeBadge').innerHTML =
            '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold bg-blue-100 text-blue-700 border-blue-200 whitespace-nowrap">'
            + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'
            + s.total_periode + 'x preventive</span>';

        // Ringkasan
        document.getElementById('summaryTotalPeriode').textContent = s.total_periode + '×';
        document.getElementById('summaryTotalOk').textContent      = s.total_ok;
        document.getElementById('summaryTotalNg').textContent      = s.total_ng;
        document.getElementById('summaryTotalParts').textContent   = s.total_parts;
        document.getElementById('summaryAvgDurasi').textContent    = s.avg_durasi || '-';

        // Card per periode
        const list = document.getElementById('periodeCardList');
        records.forEach(function (r, idx) {
            list.insertAdjacentHTML('beforeend', buildPeriodeCard(r, idx === 0));
        });

        document.getElementById('detailLoading').classList.add('hidden');
        document.getElementById('detailContent').classList.remove('hidden');

    } catch (e) {
        closeDetailModal();
        if (typeof Swal !== 'undefined') Swal.fire('Error!', 'Gagal memuat detail history', 'error');
    }
}

// ════════════════════════════════════════════════════════
// BUILD CARD PER PERIODE
// ════════════════════════════════════════════════════════
function buildPeriodeCard(r, expanded) {
    function sRow(label, val) {
        return '<div><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">' + label + '</p>'
             + '<p class="text-[12.5px] text-zinc-900">' + escHtml(val || '-') + '</p></div>';
    }
    function sRowFull(label, val) {
        return '<div class="col-span-2"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">' + label + '</p>'
             + '<p class="text-[12.5px] text-zinc-900 whitespace-pre-line">' + escHtml(val || '-') + '</p></div>';
    }

    // Badge status jadwal
    var jadwalBadge = '';
    if (r.selisih_jadwal !== null && r.selisih_jadwal !== undefined) {
        if (r.selisih_jadwal === 0) {
            jadwalBadge = '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-green-100 text-green-800 border-green-200">Tepat jadwal</span>';
        } else if (r.selisih_jadwal > 0) {
            jadwalBadge = '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-red-100 text-red-800 border-red-200">Telat ' + r.selisih_jadwal + ' hari</span>';
        } else {
            jadwalBadge = '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-blue-100 text-blue-800 border-blue-200">Lebih awal ' + Math.abs(r.selisih_jadwal) + ' hari</span>';
        }
    }

    var ngBadge = r.total_ng > 0
        ? '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-red-100 text-red-800 border-red-200">' + r.total_ng + ' NG</span>'
        : '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-green-100 text-green-800 border-green-200">All OK</span>';

    var partBadge = r.total_part_used > 0
        ? '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-orange-100 text-orange-800 border-orange-200">' + r.total_part_used + ' part</span>'
        : '';

    var jarakText = r.jarak_hari !== null && r.jarak_hari !== undefined
        ? r.jarak_hari + ' hari'
        : 'Periode pertama';

    // ── Info utama
    var infoHtml = '<div class="grid grid-cols-2 gap-x-8 gap-y-3">'
        + sRow('Tanggal Terjadwal', r.tanggal_terjadwal_fmt)
        + sRow('Tanggal Checkup', r.tanggal_checkup_fmt)
        + sRow('Mulai', r.mulai_fmt)
        + sRow('Selesai', r.selesai_fmt)
        + sRow('Durasi', r.durasi_display)
        + sRow('Jarak dari Periode Sebelumnya', jarakText)
        + sRow('Hasil', 'OK: ' + r.total_ok + '  ·  NG: ' + r.total_ng + '  ·  Parts: ' + r.total_part_used)
        + sRowFull('Catatan Umum', r.catatan_umum || 'Tidak ada catatan')
        + '</div>';

    // ── Detail poin checkup
    var detailsHtml = '';
    if (r.details && r.details.length > 0) {
        detailsHtml = '<div class="mt-4 pt-4 border-t border-zinc-100">'
            + '<p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Detail Poin Checkup</p>'
            + '<div class="flex flex-col gap-2">'
            + r.details.map(buildDetailItem).join('')
            + '</div></div>';
    }

    // ── Part replacement standalone (tidak terikat ke poin)
    var standalone = (r.part_replacements || []).filter(function (p) { return !p.history_checkup_detail_id; });
    var standaloneHtml = '';
    if (standalone.length > 0) {
        standaloneHtml = '<div class="mt-4 pt-4 border-t border-zinc-100">'
            + '<p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Part Replacement Lainnya</p>'
            + '<div class="flex flex-col gap-2">'
            + standalone.map(function (p) {
                return '<div class="bg-orange-50 border border-orange-200 rounded-lg px-3 py-2">'
                     + '<p class="text-[12px] font-semibold text-zinc-900">' + escHtml(p.nama_part) + '</p>'
                     + '<p class="text-[11px] text-zinc-600">' + escHtml(p.kode_part) + ' — Qty: ' + escHtml(p.quantity_used) + '</p>'
                     + (p.catatan ? '<p class="text-[11px] text-zinc-500 italic mt-1">' + escHtml(p.catatan) + '</p>' : '')
                     + '</div>';
            }).join('')
            + '</div></div>';
    }

    return '<div class="border border-zinc-200 rounded-[14px] overflow-hidden">'
        + '<div class="flex items-center justify-between px-4 py-3.5 bg-zinc-50 cursor-pointer hover:bg-zinc-100 transition select-none" onclick="togglePeriodeCard(this)">'
        +   '<div class="flex items-center gap-3">'
        +     '<div class="w-[26px] h-[26px] rounded-full bg-zinc-900 flex items-center justify-center text-[12px] font-semibold text-white flex-shrink-0">' + r.periode_ke + '</div>'
        +     '<div>'
        +       '<p class="text-[13px] font-semibold text-zinc-900 leading-none">Periode ke-' + r.periode_ke + '</p>'
        +       '<p class="text-[11px] text-zinc-400 mt-0.5">' + escHtml(r.tanggal_checkup_fmt || '-') + ' &nbsp;·&nbsp; ' + escHtml(r.durasi_display || '-') + '</p>'
        +     '</div>'
        +   '</div>'
        +   '<div class="flex items-center gap-2 flex-wrap justify-end">'
        +     jadwalBadge + ngBadge + partBadge
        +     '<svg class="periode-chevron w-4 h-4 text-zinc-400 transition-transform duration-200 ' + (expanded ? 'rotate-180' : '') + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>'
        +   '</div>'
        + '</div>'
        + '<div class="' + (expanded ? '' : 'hidden ') + 'border-t border-zinc-200 bg-white px-5 py-4">'
        +   infoHtml + detailsHtml + standaloneHtml
        + '</div>'
        + '</div>';
}

function buildDetailItem(d) {
    var isOk = d.status === 'ok';

    var statusBadge = isOk
        ? '<span class="px-2.5 py-0.5 bg-green-500 text-white rounded-full text-[11px] font-bold">✓ OK</span>'
        : '<span class="px-2.5 py-0.5 bg-red-500 text-white rounded-full text-[11px] font-bold">✗ NG</span>';

    var actionBadge = '';
    if (!isOk && d.ng_action_type) {
        if (d.ng_action_type === 'part')     actionBadge = '<span class="ml-1.5 px-2 py-0.5 bg-orange-500 text-white rounded text-[11px] font-semibold">🔧 Ganti Part</span>';
        if (d.ng_action_type === 'inhouse')  actionBadge = '<span class="ml-1.5 px-2 py-0.5 bg-blue-500 text-white rounded text-[11px] font-semibold">🏭 Inhouse</span>';
        if (d.ng_action_type === 'outhouse') actionBadge = '<span class="ml-1.5 px-2 py-0.5 bg-purple-500 text-white rounded text-[11px] font-semibold">🏢 Outhouse</span>';
    }

    var catatanHtml = d.catatan
        ? '<div class="mt-2 pl-3 border-l-2 ' + (isOk ? 'border-green-400' : 'border-red-400') + '"><p class="text-[11px] text-zinc-600 italic">Catatan: ' + escHtml(d.catatan) + '</p></div>'
        : '';

    var partsHtml = '';
    if (d.part_replacements && d.part_replacements.length > 0) {
        partsHtml = '<div class="mt-2 bg-white rounded-lg p-2.5 border border-orange-300">'
            + '<p class="text-[11px] font-bold text-orange-800 mb-1.5">Part yang Digunakan:</p>'
            + d.part_replacements.map(function (p) {
                return '<div class="bg-orange-50 rounded p-2 border border-orange-200 mb-1 last:mb-0">'
                     + '<p class="text-[11px] font-semibold text-zinc-900">' + escHtml(p.nama_part) + '</p>'
                     + '<p class="text-[11px] text-zinc-600">' + escHtml(p.kode_part) + ' — Qty: ' + escHtml(p.quantity_used) + '</p>'
                     + (p.catatan ? '<p class="text-[11px] text-zinc-500 italic mt-0.5">' + escHtml(p.catatan) + '</p>' : '')
                     + '</div>';
            }).join('')
            + '</div>';
    }

    var requestHtml = '';
    if (d.inhouse_request) {
        var ih = d.inhouse_request;
        requestHtml = '<div class="mt-2 bg-blue-50 rounded-lg p-2.5 border border-blue-200">'
            + '<p class="text-[11px] font-bold text-blue-800 mb-1">Inhouse Request</p>'
            + '<div class="grid grid-cols-2 gap-2 text-[11px]">'
            +   '<div><span class="text-zinc-500">Problem:</span><p class="font-semibold text-zinc-900">' + escHtml(ih.problem || '-') + '</p></div>'
            +   '<div><span class="text-zinc-500">Mesin:</span><p class="font-semibold text-zinc-900">' + escHtml(ih.mesin || '-') + '</p></div>'
            + '</div>'
            + (ih.proses_dilakukan ? '<div class="text-[11px] mt-1"><span class="text-zinc-500">Proses:</span><p class="font-semibold text-zinc-900">' + escHtml(ih.proses_dilakukan) + '</p></div>' : '')
            + '</div>';
    } else if (d.outhouse_request) {
        var oh = d.outhouse_request;
        requestHtml = '<div class="mt-2 bg-purple-50 rounded-lg p-2.5 border border-purple-200">'
            + '<p class="text-[11px] font-bold text-purple-800 mb-1">Outhouse Request</p>'
            + '<div class="grid grid-cols-2 gap-2 text-[11px]">'
            +   '<div><span class="text-zinc-500">Problem:</span><p class="font-semibold text-zinc-900">' + escHtml(oh.problem || '-') + '</p></div>'
            +   '<div><span class="text-zinc-500">Mesin:</span><p class="font-semibold text-zinc-900">' + escHtml(oh.mesin || '-') + '</p></div>'
            + '</div>'
            + (oh.supplier ? '<div class="text-[11px] mt-1"><span class="text-zinc-500">Supplier:</span><p class="font-semibold text-zinc-900">' + escHtml(oh.supplier) + '</p></div>' : '')
            + '</div>';
    }

    return '<div class="border rounded-lg p-3 ' + (isOk ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') + '">'
        + '<div class="flex items-center justify-between gap-2">'
        +   '<div class="flex items-center gap-2 min-w-0">'
        +     '<span class="bg-zinc-700 text-white px-2.5 py-0.5 rounded-full text-[11px] font-bold flex-shrink-0">' + escHtml(d.poin) + '</span>'
        +     '<p class="text-[12.5px] font-semibold text-zinc-900 truncate">' + escHtml(d.nama_bagian) + '</p>'
        +   '</div>'
        +   '<div class="flex items-center flex-shrink-0">' + statusBadge + actionBadge + '</div>'
        + '</div>'
        + catatanHtml + partsHtml + requestHtml
        + '</div>';
}

// ════════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════════
function togglePeriodeCard(header) {
    const body     = header.nextElementSibling;
    const chevron  = header.querySelector('.periode-chevron');
    const isHidden = body.classList.contains('hidden');
    body.classList.toggle('hidden', !isHidden);
    chevron.classList.toggle('rotate-180', isHidden);
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

function escHtml(str) {
    if (!str && str !== 0) return '';
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}
</script>