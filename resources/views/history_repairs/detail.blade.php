{{-- ════════════════════════════════════════════════════
    Modal: Detail History Repair (per Part No)
    File: resources/views/history_repairs/detail.blade.php
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
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[14px] font-medium text-zinc-900 leading-none">History Repair</p>
                    <p id="detailPartNoHeader" class="text-[12px] text-zinc-400 font-mono mt-1"></p>
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
                <div class="flex items-start gap-3 pb-4 border-b border-zinc-100">
                    <div class="flex-1">
                        <p id="detailNamaFull" class="text-[16px] font-semibold text-zinc-900"></p>
                        <p id="detailCustomer" class="text-[12px] text-zinc-400 mt-0.5"></p>
                    </div>
                    <div id="detailRepairCountBadge"></div>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-3">Ringkasan</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-zinc-50 rounded-xl border border-zinc-200 px-4 py-3">
                            <p class="text-[10px] text-zinc-400 mb-1.5">Total repair</p>
                            <p id="summaryTotalRepair" class="text-[22px] font-semibold text-zinc-900 leading-none"></p>
                        </div>
                        <div class="bg-green-50 rounded-xl border border-green-200 px-4 py-3">
                            <p class="text-[10px] text-green-600 mb-1.5">Judge OK</p>
                            <p id="summaryTotalOk" class="text-[22px] font-semibold text-green-700 leading-none"></p>
                        </div>
                        <div class="bg-red-50 rounded-xl border border-red-200 px-4 py-3">
                            <p class="text-[10px] text-red-500 mb-1.5">Judge NG</p>
                            <p id="summaryTotalNg" class="text-[22px] font-semibold text-red-600 leading-none"></p>
                        </div>
                        <div class="bg-zinc-50 rounded-xl border border-zinc-200 px-4 py-3">
                            <p class="text-[10px] text-zinc-400 mb-1.5">Rata-rata durasi</p>
                            <p id="summaryAvgDurasi" class="text-[13px] font-semibold text-zinc-700 leading-tight pt-1"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-3">Riwayat per perbaikan</p>
                    <div id="repairCardList" class="flex flex-col gap-3"></div>
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

{{-- ════════════════════════════════════════════════════
    Modal: Print Preview (iframe dari route Laravel)
════════════════════════════════════════════════════ --}}
<div id="printPreviewModal"
    style="display:none"
    class="fixed inset-0 z-[60] flex items-center justify-center p-5"
    onclick="handlePrintPreviewBackdrop(event)">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div id="printPreviewContent"
        class="relative w-full max-w-[50rem] max-h-[95vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl
               transform transition-all duration-300 scale-95 opacity-0 bg-zinc-900">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-700 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <div>
                    <p id="printPreviewTitle" class="text-sm font-semibold text-white"></p>
                    <p class="text-xs text-zinc-400">Preview Cetak · A4</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="doPrint()"
                    class="flex items-center gap-2 bg-white text-zinc-900 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-zinc-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
                <button onclick="closePrintPreviewModal()"
                    class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition text-zinc-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Preview area: iframe load dari route Laravel --}}
        <div class="flex-1 overflow-hidden bg-zinc-800 flex flex-col">

            {{-- Loading state --}}
            <div id="printIframeLoading" class="flex flex-col items-center justify-center py-16">
                <svg class="animate-spin h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-3 text-zinc-400 text-sm">Memuat preview...</p>
            </div>

            {{-- Iframe --}}
            <iframe
                id="printPreviewIframe"
                style="display:none; width:100%; height:75vh; border:none;"
                onload="onIframeLoaded()">
            </iframe>

        </div>
    </div>
</div>

<script>
// ════════════════════════════════════════════════════════
// PRINT PREVIEW — iframe dari route Laravel
// ════════════════════════════════════════════════════════
var _printData  = null;
var _allRecords = [];

function printRepair(id) {
    const r = _allRecords.find(function(rec) { return rec.id == id; });
    if (!r) return;
    _printData = r;

    // Set judul modal
    document.getElementById('printPreviewTitle').textContent = r.no + ' · Repair ke-' + r.repair_count;

    // Reset iframe & tampilkan loading
    const iframe = document.getElementById('printPreviewIframe');
    iframe.style.display = 'none';
    document.getElementById('printIframeLoading').style.display = 'flex';

    // Load route Laravel ke iframe
    iframe.src = '/history-repairs/' + r.id + '/print';

    // Buka modal
    const modal   = document.getElementById('printPreviewModal');
    const content = document.getElementById('printPreviewContent');
    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));
}

function onIframeLoaded() {
    document.getElementById('printIframeLoading').style.display = 'none';
    document.getElementById('printPreviewIframe').style.display = 'block';
}

function doPrint() {
    const iframe = document.getElementById('printPreviewIframe');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.print();
    }
}

function closePrintPreviewModal() {
    const modal   = document.getElementById('printPreviewModal');
    const content = document.getElementById('printPreviewContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(function() {
        modal.style.display = 'none';
        // Reset iframe biar tidak ada request menggantung
        document.getElementById('printPreviewIframe').src = 'about:blank';
    }, 300);
}

function handlePrintPreviewBackdrop(e) {
    if (e.target === document.getElementById('printPreviewModal')) closePrintPreviewModal();
}

// ════════════════════════════════════════════════════════
// DETAIL MODAL
// ════════════════════════════════════════════════════════
async function openDetailModal(partNo, triggerId) {
    const modal   = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');

    document.getElementById('detailLoading').classList.remove('hidden');
    document.getElementById('detailContent').classList.add('hidden');
    document.getElementById('repairCardList').innerHTML = '';
    document.getElementById('detailPartNoHeader').textContent = partNo;
    _allRecords = [];

    modal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }));

    try {
        const res    = await fetch('/history-repairs/by-part-no?part_no=' + encodeURIComponent(partNo), { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (!result.success) throw new Error();

        const s       = result.summary;
        const records = result.records;
        _allRecords   = records;

        const firstRecord = records[0] || {};
        document.getElementById('detailNamaFull').textContent = firstRecord.nama || partNo;
        document.getElementById('detailCustomer').textContent = firstRecord.customer ? 'Customer: ' + firstRecord.customer : '';

        const badgeEl  = document.getElementById('detailRepairCountBadge');
        const cnt      = s.total_repair;
        const badgeCls = cnt >= 4 ? 'bg-red-100 text-red-700 border-red-200'
                       : cnt >= 3 ? 'bg-orange-100 text-orange-700 border-orange-200'
                       : cnt >= 2 ? 'bg-yellow-100 text-yellow-700 border-yellow-200'
                       :            'bg-blue-100 text-blue-700 border-blue-200';
        badgeEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold ' + badgeCls + '">'
            + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'
            + cnt + 'x repair</span>';

        document.getElementById('summaryTotalRepair').textContent = cnt + '×';
        document.getElementById('summaryTotalOk').textContent     = s.total_ok + '×';
        document.getElementById('summaryTotalNg').textContent     = s.total_ng + '×';
        document.getElementById('summaryAvgDurasi').textContent   = s.avg_durasi || '-';

        const list = document.getElementById('repairCardList');
        records.forEach(function (r) {
            const tl     = r.timeline || {};
            const katMap = { 'Dies':'bg-blue-100 text-blue-800','Burry':'bg-yellow-100 text-yellow-800','Dimensi':'bg-purple-100 text-purple-800','Human Error':'bg-red-100 text-red-800','Accessories':'bg-green-100 text-green-800' };
            const katCls = katMap[r.kategori_problem] || 'bg-gray-100 text-gray-700';
            const hasilVal = r.judge_permanen || r.judge_monitoring || '-';
            const hasilCls = hasilVal==='OK'?'bg-green-100 text-green-800 border-green-200':hasilVal==='NG'?'bg-red-100 text-red-800 border-red-200':'bg-gray-100 text-gray-600 border-gray-200';

            function sRow(label, val, mono) {
                return '<div><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">'+label+'</p><p class="text-[12.5px] text-zinc-900 '+(mono?'font-mono':'')+'">'+escHtml(val||'-')+'</p></div>';
            }
            function sRowFull(label, val) {
                return '<div class="col-span-2"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">'+label+'</p><p class="text-[12.5px] text-zinc-900 whitespace-pre-line">'+escHtml(val||'-')+'</p></div>';
            }
            function okngBadge(val) {
                if (!val) return '<span class="text-zinc-400">-</span>';
                return '<span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold '+(val==='OK'?'bg-green-100 text-green-800':'bg-red-100 text-red-800')+'">'+val+'</span>';
            }
            function sRowOkng(label, val) {
                return '<div><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">'+label+'</p>'+okngBadge(val)+'</div>';
            }

            var sec1='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Tindakan Perbaikan</p><div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3"><div class="grid grid-cols-2 gap-x-6 gap-y-3">'+sRowFull('Analisa Penyebab',r.analisa_penyebab)+sRowFull('Tindakan Perbaikan',r.tindakan_perbaikan)+sRowFull('Catatan Penggantian Sparepart',r.catatan_penggantian_sparepart)+'</div></div></div>';
            var sec2='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Penanganan Problem Burry</p><div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3"><div class="grid grid-cols-2 gap-x-6 gap-y-3">'+sRow('Item',r.item)+sRow('Proses Grinding',r.proses_grinding)+sRow('Shim Up',r.shim_up)+sRowOkng('Status',r.status_burry)+sRowOkng('Standart',r.standart_burry)+sRow('Group Leader',r.group_leader)+sRow('Operator',r.operator)+'</div></div></div>';
            var sec3='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Target Trial After Repair</p><div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3"><div class="grid grid-cols-2 gap-x-6 gap-y-3">'+sRow('Plan',r.plan)+sRow('Actual',r.actual)+sRow('Remark',r.remark)+sRowOkng('Judge',r.judge)+'</div></div></div>';
            var sec4='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Monitoring Dies Temporary</p><div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3"><div class="grid grid-cols-2 gap-x-6 gap-y-3">'+sRow('Tanggal Cek',r.tanggal_cek?r.tanggal_cek.substring(0,10).split('-').reverse().join('/'):'-')+sRow('Lot Prod',r.lot_prod)+sRowOkng('Awal',r.awal)+sRowOkng('Tengah',r.tengah)+sRowOkng('Akhir',r.akhir)+sRowOkng('Qty',r.qty)+sRow('Remark',r.remark_monitoring)+sRowOkng('Judge',r.judge_monitoring)+'</div></div></div>';
            var sec5='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-2">Target Permanen Action</p><div class="bg-purple-50 border border-purple-200 rounded-xl px-4 py-3"><div class="grid grid-cols-2 gap-x-6 gap-y-3">'+sRow('Plan',r.plan_permanen)+sRow('Actual',r.actual_permanen)+sRowFull('Rootcause',r.rootcause)+sRowFull('Recovery',r.recovery)+sRow('Assy Trial Check',r.assy_trial_check)+sRowOkng('Judge',r.judge_permanen)+'</div></div></div>';

            var tlHtml='';
            if (tl.on_process_at||tl.on_trial_at||tl.closed_at) {
                var tlItems='<div class="flex gap-3 pb-4 relative"><div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-zinc-900 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><div class="flex-1 min-w-0 pt-1"><p class="text-[12px] font-medium text-zinc-900">On Process</p><p class="text-[11px] font-mono text-zinc-400 mt-0.5">'+(tl.on_process_at?fmtDatetime(tl.on_process_at):'-')+'</p>'+(tl.durasi_on_process?'<span class="inline-flex mt-1.5 items-center gap-1 bg-zinc-100 border border-zinc-200 rounded px-2 py-0.5 text-[10px] text-zinc-600">Durasi: '+escHtml(tl.durasi_on_process)+'</span>':'')+'</div></div>';
                if(tl.on_trial_at)tlItems+='<div class="flex gap-3 pb-4 relative"><div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-green-50 border-[1.5px] border-green-600 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="flex-1 min-w-0 pt-1"><p class="text-[12px] font-medium text-zinc-900">On Trial</p><p class="text-[11px] font-mono text-zinc-400 mt-0.5">'+fmtDatetime(tl.on_trial_at)+'</p>'+(tl.durasi_on_trial?'<span class="inline-flex mt-1.5 items-center gap-1 bg-zinc-100 border border-zinc-200 rounded px-2 py-0.5 text-[10px] text-zinc-600">Durasi: '+escHtml(tl.durasi_on_trial)+'</span>':'')+'</div></div>';
                if(tl.closed_at)tlItems+='<div class="flex gap-3 relative"><div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-white border-[1.5px] border-zinc-200 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div><div class="flex-1 min-w-0 pt-1"><p class="text-[12px] font-medium text-zinc-400">Closed</p><p class="text-[11px] font-mono text-zinc-400 mt-0.5">'+fmtDatetime(tl.closed_at)+'</p></div></div>';
                var totalBar=tl.durasi_total?'<div class="flex items-center justify-between bg-zinc-900 rounded-xl px-4 py-2.5 mt-3"><span class="text-[11px] text-zinc-400">Total durasi repair</span><span class="text-[12px] font-medium text-white font-mono">'+escHtml(tl.durasi_total)+'</span></div>':'';
                tlHtml='<div class="mt-4 pt-4 border-t border-zinc-100"><p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-3">Timeline Durasi</p><div class="relative flex flex-col"><div class="absolute left-[15px] top-5 bottom-5 w-px bg-zinc-200"></div>'+tlItems+'</div>'+totalBar+'</div>';
            }

            list.insertAdjacentHTML('beforeend',
                '<div class="repair-card border border-zinc-200 rounded-[14px] overflow-hidden">'
                +'<div class="repair-card-header flex items-center justify-between px-4 py-3.5 bg-zinc-50 cursor-pointer hover:bg-zinc-100 transition select-none" onclick="toggleRepairCard(this)">'
                +  '<div class="flex items-center gap-3">'
                +    '<div class="w-[26px] h-[26px] rounded-full bg-zinc-200 flex items-center justify-center text-[12px] font-semibold text-zinc-600 flex-shrink-0">'+r.repair_count+'</div>'
                +    '<div><p class="text-[13px] font-semibold text-zinc-900 font-mono leading-none">'+escHtml(r.no)+'</p><p class="text-[11px] text-zinc-400 mt-0.5">'+escHtml(r.closed_at_formatted||'-')+' &nbsp;·&nbsp; Grp '+escHtml(r.group)+' / '+escHtml(r.shift)+'</p></div>'
                +  '</div>'
                +  '<div class="flex items-center gap-2">'
                +    '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border '+hasilCls+'">'+escHtml(hasilVal)+'</span>'
                +    '<span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium '+katCls+'">'+escHtml(r.kategori_problem)+'</span>'
                +    '<button onclick="printRepair('+r.id+'); event.stopPropagation();" title="Print Preview" class="w-7 h-7 rounded-lg bg-zinc-800 hover:bg-black flex items-center justify-center flex-shrink-0 transition">'
                +      '<svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>'
                +    '</button>'
                +    '<svg class="repair-chevron w-4 h-4 text-zinc-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>'
                +  '</div>'
                +'</div>'
                +'<div class="repair-card-body hidden border-t border-zinc-200 bg-white px-5 py-4">'
                +  '<div class="grid grid-cols-2 gap-x-8 gap-y-3">'
                +    sRow('Tanggal Pengajuan',r.tanggal_pengajuan||'-')+sRow('Tanggal Closed',r.closed_at_formatted||'-')
                +    sRow('Jumlah Stroke',r.jumlah_stroke?Number(r.jumlah_stroke).toLocaleString('id-ID'):'-')+sRow('Line Mesin',r.line_mesin||'-')
                +    sRow('Process No',r.process_no||'-',true)+sRow('Jenis',r.jenis||'-')
                +    sRow('Target Selesai',r.target_selesai||'-')+sRow('Kategori',r.kategori_problem||'-')
                +    (r.detail_proyek?sRowFull('Detail Proyek',r.detail_proyek):'')
                +  '</div>'
                +sec1+sec2+sec3+sec4+sec5+tlHtml
                +'</div></div>'
            );
        });

        document.getElementById('detailLoading').classList.add('hidden');
        document.getElementById('detailContent').classList.remove('hidden');

    } catch (e) {
        closeDetailModal();
        if (typeof Swal !== 'undefined') Swal.fire('Error!', 'Gagal memuat detail history', 'error');
    }
}

function toggleRepairCard(header) {
    const body    = header.nextElementSibling;
    const chevron = header.querySelector('.repair-chevron');
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

function fmtDatetime(iso) {
    if (!iso) return '-';
    const d = new Date(iso);
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
         + ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}
</script>