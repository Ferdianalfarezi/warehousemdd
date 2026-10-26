<?php

namespace App\Http\Controllers;

use App\Models\RequestRepairHistory;
use App\Models\RequestRepairAttempt;
use App\Models\RequestRepairPause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HistoryRepairController extends Controller
{
    // ── Index view ──────────────────────────────────────────
    public function index()
    {
        // Pilihan filter customer
        $customers = RequestRepairHistory::whereNotNull('customer')
            ->where('customer', '!=', '')
            ->distinct()
            ->orderBy('customer')
            ->pluck('customer');

        return view('history_repairs.index', compact('customers'));
    }

    // ── AJAX: data grafik Pareto (ikut filter yang sama dengan tabel) ──
    public function getSummary(Request $request)
    {
        $base = RequestRepairHistory::query();
        $this->applyFilters($base, $request);

        // Pareto kategori: semua kategori (jumlahnya sedikit)
        $byKategori = (clone $base)
            ->selectRaw("COALESCE(NULLIF(kategori_problem, ''), '-') as label, COUNT(*) as value")
            ->groupBy('label')
            ->orderByDesc('value')
            ->get();

        // Pareto part no: jumlah repair closed per part no, Top 10.
        // total = semua repair (bukan cuma Top 10) supaya % kumulatif tetap benar.
        $byPartNo = (clone $base)
            ->selectRaw('part_no as label, MAX(nama) as title, COUNT(*) as value')
            ->groupBy('part_no')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        return response()->json([
            'success'     => true,
            'total'       => (clone $base)->count(),
            'by_kategori' => $byKategori,
            'by_part_no'  => $byPartNo,
        ]);
    }

    // ── Filter bersama: search + tgl closed + customer (bisa lebih dari 1) ──
    private function applyFilters($query, Request $request): void
    {
        $t = 'request_repair_histories.';

        $search = $request->get('search', '');
        if ($search !== '' && $search !== null) {
            $query->where(function ($q2) use ($search, $t) {
                $q2->where($t . 'no',                 'like', "%{$search}%")
                   ->orWhere($t . 'part_no',          'like', "%{$search}%")
                   ->orWhere($t . 'nama',             'like', "%{$search}%")
                   ->orWhere($t . 'customer',         'like', "%{$search}%")
                   ->orWhere($t . 'line_mesin',       'like', "%{$search}%")
                   ->orWhere($t . 'process_no',       'like', "%{$search}%")
                   ->orWhere($t . 'kategori_problem', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate($t . 'closed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate($t . 'closed_at', '<=', $request->date_to);
        }

        $customers = array_filter((array) $request->input('customers', []), fn ($c) => $c !== null && $c !== '');
        if ($customers) {
            $query->whereIn($t . 'customer', $customers);
        }
    }

        // ── AJAX: table data ─────────────────────────────────────
    public function getData(Request $request)
    {
        $perPage  = $request->get('per_page', 20);
        $page     = (int) $request->get('page', 1);

        $latestIds = RequestRepairHistory::selectRaw('MAX(id) as id')
            ->groupBy('part_no');

        $query = RequestRepairHistory::joinSub($latestIds, 'latest', function ($join) {
                $join->on('request_repair_histories.id', '=', 'latest.id');
            });
        $this->applyFilters($query, $request);

        $total = $query->count();

        if ($perPage === 'all') {
            $items      = $query->latest('request_repair_histories.closed_at')->get();
            $perPageInt = $total ?: 1;
        } else {
            $perPageInt = (int) $perPage;
            $items      = $query->latest('request_repair_histories.closed_at')
                                ->skip(($page - 1) * $perPageInt)
                                ->take($perPageInt)
                                ->get();
        }

        $totalPages = $perPage === 'all' ? 1 : (int) ceil($total / max($perPageInt, 1));
        $from       = $total > 0 ? (($page - 1) * $perPageInt) + 1 : 0;
        $to         = min($page * $perPageInt, $total);

        $data = $items->map(function ($h, $idx) use ($page, $perPageInt) {
            return [
                'id'                => $h->id,
                'row_number'        => (($page - 1) * $perPageInt) + $idx + 1,
                'no'                => $h->no,
                'tanggal_pengajuan' => $h->tanggal_pengajuan?->format('d/m/Y'),
                'closed_at'         => $h->closed_at?->format('d/m/Y H:i'),
                'group'             => $h->group,
                'shift'             => $h->shift,
                'part_no'           => $h->part_no,
                'nama'              => $h->nama,
                'process_no'        => $h->process_no,
                'customer'          => $h->customer,
                'kategori_problem'  => $h->kategori_problem,
                'judge_monitoring'  => $h->judge_monitoring,
                'judge_permanen'    => $h->judge_permanen,
                'repair_count'      => $h->repair_count,
                'ng_attempt_count'  => $h->ng_attempt_count,
                'durasi_total'      => RequestRepairHistory::formatDurasi($h->durasi_total_seconds),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPageInt,
                'current_page' => $page,
                'total_pages'  => $totalPages,
                'from'         => $from,
                'to'           => $to,
            ],
        ]);
    }

    // ── AJAX: show single record ─────────────────────────────
    public function show(RequestRepairHistory $historyRepair)
    {
        $data             = $historyRepair->toArray();
        $data['timeline'] = $historyRepair->getTimelineData();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── AJAX: semua history per part_no (untuk modal detail) ─
    public function getByPartNo(Request $request)
    {
        $partNo = $request->get('part_no');
        if (!$partNo) {
            return response()->json(['success' => false, 'message' => 'part_no diperlukan'], 422);
        }

        $summary = RequestRepairHistory::getSummaryByPartNo($partNo);

        $historyRecords = RequestRepairHistory::where('part_no', $partNo)
            ->orderBy('closed_at', 'desc')
            ->get();

        // ── Ambil semua riwayat percobaan NG (attempts) untuk kumpulan 'no' yang relevan ──
        $noList = $historyRecords->pluck('no')->unique()->values();

        $attemptsByNo = RequestRepairAttempt::whereIn('no', $noList)
            ->with('judgedBy:id,nama')
            ->orderBy('attempt_number')
            ->get()
            ->groupBy('no');

        // ── Ambil semua sesi pause untuk kumpulan 'no' yang relevan (⬅️ baru) ──
        // Di-group 2 level: [no][cycle_number] biar bisa dipetain persis ke siklus yang mana
        // (siklus yang berhasil closed OK, atau siklus attempt yang gagal NG).
        $pausesByNoCycle = RequestRepairPause::whereIn('no', $noList)
            ->with('pausedBy:id,nama')
            ->orderBy('paused_at')
            ->get()
            ->groupBy(['no', 'cycle_number']);

        $records = $historyRecords->map(function ($h) use ($attemptsByNo, $pausesByNoCycle) {

            // Snapshot riwayat percobaan NG untuk 'no' repair ini
            $ngAttempts = ($attemptsByNo->get($h->no) ?? collect())->map(function ($a) use ($pausesByNoCycle) {
                $pauseSessions = ($pausesByNoCycle->get($a->no)[$a->cycle_number] ?? collect())
                    ->map(fn ($p) => $this->mapPauseSession($p))
                    ->values();

                return [
                    'attempt_number'                 => $a->attempt_number,
                    'pic_names'                       => $a->pic_names,
                    'analisa_penyebab'               => $a->analisa_penyebab,
                    'tindakan_perbaikan'             => $a->tindakan_perbaikan,
                    'catatan_penggantian_sparepart'  => $a->catatan_penggantian_sparepart,
                    'judge'                          => $a->judge,
                    'judge_monitoring'               => $a->judge_monitoring,
                    'judge_permanen'                 => $a->judge_permanen,
                    'rootcause'                       => $a->rootcause,
                    'recovery'                        => $a->recovery,
                    'ng_judged_at'                    => $a->ng_judged_at?->format('d M Y, H:i'),
                    'judged_by_name'                  => $a->judgedBy->nama ?? '-',
                    'durasi_on_process'               => RequestRepairHistory::formatDurasi($a->durasi_on_process_seconds),
                    'durasi_on_trial'                 => RequestRepairHistory::formatDurasi($a->durasi_on_trial_seconds),
                    'total_paused_seconds'            => $a->total_paused_seconds, // ⬅️ baru
                    'total_paused'                     => RequestRepairHistory::formatDurasi($a->total_paused_seconds), // ⬅️ baru
                    'pause_sessions'                   => $pauseSessions, // ⬅️ baru
                ];
            })->values();

            // Sesi pause untuk siklus yang berhasil closed OK (⬅️ baru)
            $pauseSessionsMain = ($pausesByNoCycle->get($h->no)[$h->cycle_number] ?? collect())
                ->map(fn ($p) => $this->mapPauseSession($p))
                ->values();

            return [
                'id'                  => $h->id,
                'no'                  => $h->no,
                'repair_count'        => $h->repair_count,
                'tanggal_pengajuan'   => $h->tanggal_pengajuan?->format('d/m/Y'),
                'closed_at_formatted' => $h->closed_at?->format('d M Y, H:i'),
                'group'               => $h->group,
                'shift'               => $h->shift,
                'jumlah_stroke'       => $h->jumlah_stroke,
                'line_mesin'          => $h->line_mesin,
                'process_no'          => $h->process_no,
                'customer'            => $h->customer,
                'nama'                => $h->nama,
                'jenis'               => $h->jenis,
                'target_selesai'      => $h->target_selesai?->format('d/m/Y'),
                'kategori_problem'    => $h->kategori_problem,
                'detail_proyek'       => $h->detail_proyek,

                // ── On Trial: Section 1 — Tindakan Perbaikan
                'analisa_penyebab'              => $h->analisa_penyebab,
                'tindakan_perbaikan'            => $h->tindakan_perbaikan,
                'catatan_penggantian_sparepart' => $h->catatan_penggantian_sparepart,

                // ── On Trial: Section 2 — Penanganan Problem Burry
                'item'           => $h->item,
                'proses_grinding'=> $h->proses_grinding,
                'shim_up'        => $h->shim_up,
                'status_burry'   => $h->status_burry,
                'standart_burry' => $h->standart_burry,
                'group_leader'   => $h->group_leader,
                'operator'       => $h->operator,

                // ── On Trial: Section 3 — Target Trial After Repair
                'plan'   => $h->plan,
                'actual' => $h->actual,
                'remark' => $h->remark,
                'judge'  => $h->judge,

                // ── Closed: Section 1 — Monitoring Dies Temporary
                'tanggal_cek'       => $h->tanggal_cek,
                'lot_prod'          => $h->lot_prod,
                'awal'              => $h->awal,
                'tengah'            => $h->tengah,
                'akhir'             => $h->akhir,
                'qty'               => $h->qty,
                'remark_monitoring' => $h->remark_monitoring,
                'judge_monitoring'  => $h->judge_monitoring,

                // ── Closed: Section 2 — Target Permanen Action
                'plan_permanen'    => $h->plan_permanen,
                'actual_permanen'  => $h->actual_permanen,
                'rootcause'        => $h->rootcause,
                'recovery'         => $h->recovery,
                'assy_trial_check' => $h->assy_trial_check,
                'judge_permanen'   => $h->judge_permanen,

                // ── Durasi & timeline
                'durasi_total' => RequestRepairHistory::formatDurasi($h->durasi_total_seconds),
                'timeline'     => $h->getTimelineData(),

                // ── Riwayat percobaan NG sebelum akhirnya OK
                'pic_names'        => $h->pic_names,
                'ng_attempt_count' => $h->ng_attempt_count,
                'ng_attempts'      => $ngAttempts,

                // ── Riwayat Pause untuk siklus yang berhasil closed OK ini (⬅️ baru)
                'total_paused_seconds' => $h->total_paused_seconds,
                'total_paused'          => RequestRepairHistory::formatDurasi($h->total_paused_seconds),
                'pause_sessions'        => $pauseSessionsMain,
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'records' => $records,
        ]);
    }

    // ── AJAX: data 1 history untuk form edit (superadmin) ───
    public function editData(RequestRepairHistory $historyRepair)
    {
        $h = $historyRepair;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                            => $h->id,
                'no'                            => $h->no,
                'part_no'                       => $h->part_no,
                'repair_count'                  => $h->repair_count,
                'total_paused_seconds'          => (int) $h->total_paused_seconds,
                // Info pengajuan
                'tanggal_pengajuan'             => $h->tanggal_pengajuan?->format('Y-m-d'),
                'group'                         => $h->group,
                'shift'                         => $h->shift,
                'jumlah_stroke'                 => $h->jumlah_stroke,
                'line_mesin'                    => $h->line_mesin,
                'process_no'                    => $h->process_no,
                'customer'                      => $h->customer,
                'jenis'                         => $h->jenis,
                'kategori_problem'              => $h->kategori_problem,
                'detail_proyek'                 => $h->detail_proyek,
                // Waktu
                'on_process_at'                 => $h->on_process_at?->format('Y-m-d\TH:i'),
                'finish_repair_at'              => $h->finish_repair_at?->format('Y-m-d\TH:i'),
                'on_trial_at'                   => $h->on_trial_at?->format('Y-m-d\TH:i'),
                'closed_at'                     => $h->closed_at?->format('Y-m-d\TH:i'),
                // Tindakan & PIC
                'analisa_penyebab'              => $h->analisa_penyebab,
                'tindakan_perbaikan'            => $h->tindakan_perbaikan,
                'catatan_penggantian_sparepart' => $h->catatan_penggantian_sparepart,
                'pic_names'                     => $h->pic_names,
                'rootcause'                     => $h->rootcause,
                'recovery'                      => $h->recovery,
            ],
        ]);
    }

    // ── AJAX: update history (superadmin) ───────────────────
    // Info pengajuan, waktu (durasi dihitung ulang), tindakan & PIC
    public function update(Request $request, RequestRepairHistory $historyRepair)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pengajuan'             => 'required|date',
            'group'                         => 'nullable|in:A,B',
            'shift'                         => 'nullable|in:Pagi,Malam',
            'jumlah_stroke'                 => 'nullable|integer|min:0',
            'line_mesin'                    => 'nullable|string|max:255',
            'process_no'                    => 'nullable|string|max:100',
            'customer'                      => 'nullable|string|max:255',
            'jenis'                         => 'nullable|in:Milik Sendiri,Eksternal',
            'kategori_problem'              => 'nullable|in:Dies,Burry,Dimensi,Human Error,Accessories',
            'detail_proyek'                 => 'nullable|string',
            'on_process_at'                 => 'nullable|date',
            'finish_repair_at'              => 'nullable|date|after_or_equal:on_process_at',
            'on_trial_at'                   => 'nullable|date|after_or_equal:on_process_at|after_or_equal:finish_repair_at',
            'closed_at'                     => 'required|date|after_or_equal:on_trial_at|after_or_equal:on_process_at',
            'analisa_penyebab'              => 'nullable|string',
            'tindakan_perbaikan'            => 'nullable|string',
            'catatan_penggantian_sparepart' => 'nullable|string',
            'pic_names'                     => 'nullable|string|max:255',
            'rootcause'                     => 'nullable|string',
            'recovery'                      => 'nullable|string',
        ], [
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi.',
            'closed_at.required'         => 'Waktu closed wajib diisi.',
            'finish_repair_at.after_or_equal' => 'Waktu Finish Repair tidak boleh sebelum On Process.',
            'on_trial_at.after_or_equal' => 'Waktu On Trial tidak boleh sebelum On Process / Finish Repair.',
            'closed_at.after_or_equal'   => 'Waktu Closed tidak boleh sebelum On Process / On Trial.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $h = $historyRepair;
        $h->fill($request->only([
            'tanggal_pengajuan', 'group', 'shift', 'jumlah_stroke', 'line_mesin', 'process_no', 'customer',
            'jenis', 'kategori_problem', 'detail_proyek', 'on_process_at', 'finish_repair_at', 'on_trial_at', 'closed_at',
            'analisa_penyebab', 'tindakan_perbaikan', 'catatan_penggantian_sparepart', 'pic_names', 'rootcause', 'recovery',
        ]));

        // Hitung ulang durasi dengan rumus yang sama seperti saat repair di-close
        // (waktu pause tidak dihitung).
        $paused = (int) $h->total_paused_seconds;
        // Durasi repair berakhir di Finish Repair (data lama tanpa Finish Repair: di On Trial)
        $repairEnd = $h->finish_repair_at ?? $h->on_trial_at;
        $h->durasi_on_process_seconds = ($h->on_process_at && $repairEnd)
            ? max(0, (int) $h->on_process_at->diffInSeconds($repairEnd) - $paused)
            : null;
        $h->durasi_on_trial_seconds = $h->on_trial_at
            ? (int) $h->on_trial_at->diffInSeconds($h->closed_at)
            : null;
        $h->durasi_total_seconds = $h->on_process_at
            ? max(0, (int) $h->on_process_at->diffInSeconds($h->closed_at) - $paused)
            : null;

        $h->save();

        return response()->json(['success' => true, 'message' => 'History berhasil diupdate!', 'part_no' => $h->part_no]);
    }

    // ── Helper: format 1 sesi pause untuk output JSON (⬅️ baru) ──
    private function mapPauseSession(RequestRepairPause $p): array
    {
        return [
            'alasan'       => $p->alasan,
            'alasan_label' => $p->alasan_label,
            'paused_at'    => $p->paused_at?->format('d M Y, H:i'),
            'resumed_at'   => $p->resumed_at?->format('d M Y, H:i') ?? '-',
            'durasi'       => $p->durasi_paused_seconds !== null
                                ? RequestRepairHistory::formatDurasi($p->durasi_paused_seconds)
                                : '-',
            'paused_by_name' => $p->pausedBy->nama ?? '-',
        ];
    }

        public function print(RequestRepairHistory $historyRepair)
    {
        return view('history_repairs.print', ['history' => $historyRepair]);
    }
}