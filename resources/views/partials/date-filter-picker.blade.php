{{--
    Filter kalender: 1 tombol, pilih 1 tanggal (input "tanggal") atau sebulan penuh (input "bulan").
    Pakai di dalam <form> filter:
        @include('partials.date-filter-picker', ['label' => 'Tgl Terjadwal', 'onChange' => 'submitFilter()'])
    $onChange = kode JS yang dijalankan setelah tanggal dipilih / dihapus.
--}}
<div class="flex-shrink-0" id="tjPicker" style="position:relative;">
    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
    <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
    <button type="button" onclick="tjToggle()" title="Filter {{ $label }}"
            class="px-4 py-2.5 border border-gray-300 rounded-lg transition hover:border-gray-400"
            style="display:flex; align-items:center; gap:8px; background:#fff; white-space:nowrap;">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span id="tjLabel" style="font-size:14px;">{{ $label }}</span>
    </button>
    <div id="tjPanel" style="display:none; position:absolute; top:calc(100% + 6px); left:0; z-index:40; width:288px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.12); padding:12px;"></div>
</div>
<script>
// ============================================
// KALENDER FILTER: isi hidden input "tanggal" (per hari) atau "bulan" (YYYY-MM)
// ============================================
var TJ_MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var tjView = null; // {y, m} bulan yang sedang ditampilkan di kalender

function tjInput(name) { return document.querySelector('#tjPicker input[name="' + name + '"]'); }
function tjPad(n) { return String(n).padStart(2, '0'); }

function tjRenderLabel() {
    var tgl = tjInput('tanggal').value, bln = tjInput('bulan').value, label = @json($label);
    if (tgl) { var p = tgl.split('-'); label = +p[2] + ' ' + TJ_MONTHS[p[1] - 1] + ' ' + p[0]; }
    else if (bln) { var q = bln.split('-'); label = TJ_MONTHS[q[1] - 1] + ' ' + q[0]; }
    var el = document.getElementById('tjLabel');
    el.textContent = label;
    el.style.fontWeight = (tgl || bln) ? '600' : '400';
    el.style.color = (tgl || bln) ? '#111827' : '#4b5563';
}

function tjToggle() {
    var panel = document.getElementById('tjPanel');
    if (panel.style.display === 'block') { panel.style.display = 'none'; return; }
    var base = tjInput('tanggal').value || (tjInput('bulan').value ? tjInput('bulan').value + '-01' : '');
    var d = base ? new Date(base + 'T00:00:00') : new Date();
    tjView = { y: d.getFullYear(), m: d.getMonth() };
    tjRenderPanel();
    panel.style.display = 'block';
}

function tjShift(delta) {
    var d = new Date(tjView.y, tjView.m + delta, 1);
    tjView = { y: d.getFullYear(), m: d.getMonth() };
    tjRenderPanel();
}

function tjPick(tanggal, bulan) {
    tjInput('tanggal').value = tanggal || '';
    tjInput('bulan').value = bulan || '';
    document.getElementById('tjPanel').style.display = 'none';
    tjRenderLabel();
    {!! $onChange !!};
}

function tjRenderPanel() {
    var y = tjView.y, m = tjView.m, ym = y + '-' + tjPad(m + 1);
    var selTgl = tjInput('tanggal').value, selBln = tjInput('bulan').value;
    var now = new Date(), today = now.getFullYear() + '-' + tjPad(now.getMonth() + 1) + '-' + tjPad(now.getDate());
    var navBtn = 'width:30px;height:30px;border-radius:8px;border:none;background:transparent;cursor:pointer;font-size:18px;color:#374151;';

    var html = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">'
        + '<button type="button" onclick="tjShift(-1)" style="' + navBtn + '" onmouseover="this.style.background=\'#f3f4f6\'" onmouseout="this.style.background=\'transparent\'">&lsaquo;</button>'
        + '<span style="font-weight:600;font-size:14px;color:#111827;">' + TJ_MONTHS[m] + ' ' + y + '</span>'
        + '<button type="button" onclick="tjShift(1)" style="' + navBtn + '" onmouseover="this.style.background=\'#f3f4f6\'" onmouseout="this.style.background=\'transparent\'">&rsaquo;</button>'
        + '</div>';

    html += '<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;">';
    ['Sen','Sel','Rab','Kam','Jum','Sab','Min'].forEach(function (d) {
        html += '<div style="font-size:11px;font-weight:600;color:#9ca3af;padding:4px 0;">' + d + '</div>';
    });
    var offset = (new Date(y, m, 1).getDay() + 6) % 7; // minggu mulai Senin
    for (var i = 0; i < offset; i++) html += '<div></div>';
    var days = new Date(y, m + 1, 0).getDate();
    for (var d = 1; d <= days; d++) {
        var val = ym + '-' + tjPad(d), selected = val === selTgl, inMonth = selBln === ym;
        var bg = selected ? '#111827' : (inMonth ? '#e5e7eb' : 'transparent');
        var color = selected ? '#fff' : '#111827';
        var border = val === today && !selected ? '1px solid #9ca3af' : '1px solid transparent';
        html += '<button type="button" onclick="tjPick(\'' + val + '\')" style="height:34px;border-radius:8px;border:' + border + ';background:' + bg + ';color:' + color + ';font-size:13px;cursor:pointer;"'
            + (selected ? '' : ' onmouseover="this.style.background=\'#f3f4f6\'" onmouseout="this.style.background=\'' + bg + '\'"') + '>' + d + '</button>';
    }
    html += '</div>';

    var footBtn = 'flex:1;padding:7px 0;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;';
    html += '<div style="display:flex;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid #f3f4f6;">'
        + '<button type="button" onclick="tjPick(\'\', \'' + ym + '\')" style="' + footBtn + 'border:none;background:#111827;color:#fff;">Sebulan penuh</button>'
        + '<button type="button" onclick="tjPick(\'' + today + '\')" style="' + footBtn + 'border:1px solid #d1d5db;background:#fff;color:#374151;">Hari ini</button>'
        + '<button type="button" onclick="tjPick()" style="' + footBtn + 'border:1px solid #d1d5db;background:#fff;color:#374151;">Hapus</button>'
        + '</div>';

    document.getElementById('tjPanel').innerHTML = html;
}

onPageReady(function () {
    tjRenderLabel();
    // Klik di luar kalender -> tutup
    pageListen(document, 'click', function (e) {
        var picker = document.getElementById('tjPicker');
        if (picker && e.target.isConnected && !picker.contains(e.target)) document.getElementById('tjPanel').style.display = 'none';
    });
});
</script>
