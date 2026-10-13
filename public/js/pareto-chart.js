/**
 * Grafik Pareto (Chart.js 4): batang diurutkan dari terbesar + garis % kumulatif + acuan 80%.
 * Dipakai di halaman History Request Repair & History Checkups.
 *
 * renderPareto(canvasId, items, options)
 *   items   : [{ label, value, title?, payload? }] sudah urut desc dari server
 *   options : {
 *     total       : total keseluruhan untuk % kumulatif (default = jumlah value di items).
 *                   Kirim total asli kalau items cuma Top N, biar % kumulatif tetap jujur.
 *     barColor    : warna batang
 *     valueLabel  : nama nilai di tooltip, mis. 'Repair', 'Preventive', 'NG'
 *     emptyId     : id elemen pesan "belum ada data" (ditampilkan kalau items kosong)
 *     onBarClick  : function(item) saat batang diklik (opsional)
 *   }
 */
(function () {
    const charts = {};

    function truncate(str, max) {
        str = String(str ?? '');
        return str.length > max ? str.slice(0, max - 1) + '…' : str;
    }

    window.renderPareto = function (canvasId, items, options) {
        const opts   = options || {};
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;

        if (charts[canvasId]) {
            charts[canvasId].destroy();
            delete charts[canvasId];
        }

        const empty = opts.emptyId ? document.getElementById(opts.emptyId) : null;
        const hasData = items && items.length > 0;
        canvas.style.display = hasData ? 'block' : 'none';
        if (empty) empty.style.display = hasData ? 'none' : 'flex';
        if (!hasData) return;

        const values = items.map(i => Number(i.value) || 0);
        const total  = Number(opts.total) || values.reduce((a, b) => a + b, 0);

        let running = 0;
        const cumulative = values.map(v => {
            running += v;
            return total > 0 ? Math.round((running / total) * 1000) / 10 : 0;
        });

        const barColor = opts.barColor || '#3b82f6';

        charts[canvasId] = new Chart(canvas, {
            data: {
                labels: items.map(i => i.label),
                datasets: [
                    {
                        type: 'line',
                        label: '% Kumulatif',
                        data: cumulative,
                        yAxisID: 'yPct',
                        borderColor: '#f97316',
                        backgroundColor: '#f97316',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0,
                        order: 1,
                    },
                    {
                        type: 'line',
                        label: '80%',
                        data: items.map(() => 80),
                        yAxisID: 'yPct',
                        borderColor: '#9ca3af',
                        borderDash: [6, 4],
                        borderWidth: 1,
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        order: 2,
                    },
                    {
                        type: 'bar',
                        label: opts.valueLabel || 'Jumlah',
                        data: values,
                        yAxisID: 'y',
                        backgroundColor: barColor,
                        borderRadius: 4,
                        maxBarThickness: 48,
                        order: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } },
                    },
                    tooltip: {
                        callbacks: {
                            title: ctx => {
                                const it = items[ctx[0].dataIndex];
                                return it.title ? it.label + ' — ' + it.title : it.label;
                            },
                            label: ctx => ctx.dataset.yAxisID === 'yPct'
                                ? (ctx.dataset.label === '80%' ? null : ' Kumulatif: ' + ctx.parsed.y + '%')
                                : ' ' + ctx.dataset.label + ': ' + ctx.parsed.y,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            font: { size: 10 },
                            maxRotation: 45,
                            autoSkip: false,
                            callback: function (value) { return truncate(this.getLabelForValue(value), 14); },
                        },
                        grid: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 10 } },
                        title: { display: true, text: opts.valueLabel || 'Jumlah', font: { size: 11 } },
                    },
                    yPct: {
                        position: 'right',
                        min: 0,
                        max: 100,
                        grid: { drawOnChartArea: false },
                        ticks: { callback: v => v + '%', font: { size: 10 } },
                    },
                },
                onClick: (evt, elements) => {
                    if (!opts.onBarClick || !elements.length) return;
                    opts.onBarClick(items[elements[0].index]);
                },
                onHover: (evt, elements) => {
                    if (opts.onBarClick) evt.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                },
            },
        });
    };
})();
