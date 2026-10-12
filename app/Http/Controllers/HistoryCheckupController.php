<?php

namespace App\Http\Controllers;

use App\Models\HistoryCheckup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistoryCheckupController extends Controller
{
    /**
     * Halaman index (data di-load via AJAX)
     */
    public function index()
    {
        return view('history-checkups.index');
    }

    /**
     * Data table (AJAX) + periode ke-
     * Paginate dulu, periode_ke dihitung hanya untuk baris di halaman ini
     */
    public function data(Request $request)
    {
        try {
            $perPage = $request->per_page === 'all' ? 'all' : max(1, (int) ($request->per_page ?: 20));
            $page    = max(1, (int) ($request->page ?: 1));

            $base = HistoryCheckup::query();
            $this->applyFilters($base, $request);

            $total = (clone $base)->count();

            // Hanya kolom yang dipakai di tabel
            $query = (clone $base)
                ->select([
                    'history_checkups.id',
                    'history_checkups.barang_id',
                    'history_checkups.gambar',
                    'history_checkups.kode_barang',
                    'history_checkups.nama',
                    'history_checkups.tanggal_checkup',
                    'history_checkups.mulai_perbaikan',
                    'history_checkups.waktu_selesai',
                    'history_checkups.durasi_perbaikan',
                    'history_checkups.total_part_used',
                ])
                ->orderByDesc('history_checkups.tanggal_checkup')
                ->orderByDesc('history_checkups.id');

            if ($perPage === 'all') {
                $items      = $query->get();
                $offset     = 0;
                $page       = 1;
                $totalPages = 1;
            } else {
                $totalPages = max(1, (int) ceil($total / $perPage));
                $page       = min($page, $totalPages);
                $offset     = ($page - 1) * $perPage;
                $items      = $query->skip($offset)->take($perPage)->get();
            }

            // Hitung periode_ke hanya untuk id di halaman ini (1 query)
            $periodeMap = $this->getPeriodeMap($items->pluck('id')->all());

            $data = $items->values()->map(function ($h, $i) use ($offset, $periodeMap) {
                return [
                    'id'              => $h->id,
                    'row_number'      => $offset + $i + 1,
                    'barang_id'       => $h->barang_id,
                    'gambar'          => $h->gambar,
                    'kode_barang'     => $h->kode_barang,
                    'nama'            => $h->nama,
                    'tanggal_checkup' => optional($h->tanggal_checkup)->format('d/m/Y'),
                    'mulai'           => optional($h->mulai_perbaikan)->format('H:i'),
                    'selesai'         => optional($h->waktu_selesai)->format('H:i'),
                    'durasi'          => $h->durasi_display,
                    'total_part_used' => $h->total_part_used,
                    'periode_ke'      => (int) ($periodeMap[$h->id] ?? 0),
                ];
            });

            return response()->json([
                'success'    => true,
                'data'       => $data,
                'pagination' => [
                    'total'        => $total,
                    'current_page' => $page,
                    'total_pages'  => $totalPages,
                    'from'         => $total > 0 ? $offset + 1 : 0,
                    'to'           => $offset + $items->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('History Data Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stats + ranking barang
     */
    public function summary()
    {
        // Gabung semua agregat jadi 1 query
        $agg = HistoryCheckup::query()
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(total_ok), 0) as total_ok,
                COALESCE(SUM(total_ng), 0) as total_ng,
                COALESCE(AVG(durasi_perbaikan), 0) as avg_duration,
                COALESCE(SUM(total_part_used), 0) as total_parts_used
            ')
            ->first();

        $totalOk    = (int) $agg->total_ok;
        $totalNg    = (int) $agg->total_ng;
        $totalCheck = $totalOk + $totalNg;

        $stats = [
            'total'            => (int) $agg->total,
            'this_month'       => HistoryCheckup::thisMonth()->count(),
            'avg_duration'     => (int) round($agg->avg_duration),
            'total_parts_used' => (int) $agg->total_parts_used,
            'success_rate'     => $totalCheck > 0 ? round(($totalOk / $totalCheck) * 100, 2) : 0,
        ];

        $byPreventive = HistoryCheckup::query()
            ->whereNotNull('barang_id')
            ->select(
                'barang_id',
                DB::raw('MAX(kode_barang) as kode_barang'),
                DB::raw('MAX(nama) as nama'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $byNg = HistoryCheckup::query()
            ->whereNotNull('barang_id')
            ->select(
                'barang_id',
                DB::raw('MAX(kode_barang) as kode_barang'),
                DB::raw('MAX(nama) as nama'),
                DB::raw('SUM(total_ng) as total')
            )
            ->groupBy('barang_id')
            ->havingRaw('SUM(total_ng) > 0')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'success'       => true,
            'stats'         => $stats,
            'by_preventive' => $byPreventive,
            'by_ng'         => $byNg,
        ]);
    }

    /**
     * Semua periode preventive untuk 1 barang (untuk modal detail)
     */
    public function byBarang(Request $request)
    {
        $barangId = $request->barang_id;

        if (!$barangId) {
            return response()->json([
                'success' => false,
                'message' => 'barang_id wajib diisi',
            ], 422);
        }

        $histories = HistoryCheckup::with([
            'details.partReplacements',
            'details.inhouseRequest',
            'details.outhouseRequest',
            'partReplacements',
        ])
            ->where('barang_id', $barangId)
            ->orderBy('id')
            ->get();

        if ($histories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'History tidak ditemukan',
            ], 404);
        }

        $prevDate = null;

        $records = $histories->values()->map(function ($h, $i) use (&$prevDate) {
            // Jarak hari dari periode sebelumnya
            $jarakHari = ($prevDate && $h->tanggal_checkup)
                ? (int) abs(round($prevDate->diffInDays($h->tanggal_checkup, false)))
                : null;
            $prevDate = $h->tanggal_checkup;

            // Selisih jadwal: + = telat, - = lebih awal, 0 = tepat
            $selisihJadwal = ($h->tanggal_terjadwal && $h->tanggal_checkup)
                ? (int) round($h->tanggal_terjadwal->diffInDays($h->tanggal_checkup, false))
                : null;

            return array_merge($h->toArray(), [
                'periode_ke'            => $i + 1,
                'tanggal_terjadwal_fmt' => optional($h->tanggal_terjadwal)->format('d/m/Y'),
                'tanggal_checkup_fmt'   => optional($h->tanggal_checkup)->format('d/m/Y'),
                'mulai_fmt'             => optional($h->mulai_perbaikan)->format('d/m/Y H:i'),
                'selesai_fmt'           => optional($h->waktu_selesai)->format('d/m/Y H:i'),
                'durasi_display'        => $h->durasi_display,
                'jarak_hari'            => $jarakHari,
                'selisih_jadwal'        => $selisihJadwal,
            ]);
        });

        $latest = $histories->last();

        $summary = [
            'kode_barang'   => $latest->kode_barang,
            'nama'          => $latest->nama,
            'gambar'        => $latest->gambar,
            'total_periode' => $histories->count(),
            'total_ok'      => (int) $histories->sum('total_ok'),
            'total_ng'      => (int) $histories->sum('total_ng'),
            'total_parts'   => (int) $histories->sum('total_part_used'),
            'avg_durasi'    => $this->formatMinutes((int) round($histories->avg('durasi_perbaikan') ?? 0)),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'records' => $records->reverse()->values(), // terbaru di atas
        ]);
    }

    /**
     * Hapus history
     */
    public function destroy($id)
    {
        $history = HistoryCheckup::find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'History tidak ditemukan',
            ], 404);
        }

        try {
            $history->delete();

            return response()->json([
                'success' => true,
                'message' => 'History berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * periode_ke untuk sekumpulan id: [id => periode_ke]
     * periode_ke = jumlah history dengan barang_id sama dan id <= id ini
     */
    private function getPeriodeMap(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return DB::table('history_checkups as h')
            ->join('history_checkups as h2', function ($join) {
                $join->on('h2.barang_id', '=', 'h.barang_id')
                     ->on('h2.id', '<=', 'h.id');
            })
            ->whereIn('h.id', $ids)
            ->selectRaw('h.id as id, COUNT(h2.id) as periode_ke')
            ->groupBy('h.id')
            ->pluck('periode_ke', 'id')
            ->all();
    }

    /**
     * Filter search + tanggal checkup
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('history_checkups.kode_barang', 'like', "%{$search}%")
                  ->orWhere('history_checkups.nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('history_checkups.tanggal_checkup', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('history_checkups.tanggal_checkup', '<=', $request->date_to);
        }
    }

    private function formatMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;

        return $hours > 0 ? "{$hours} jam {$mins} menit" : "{$mins} menit";
    }
}