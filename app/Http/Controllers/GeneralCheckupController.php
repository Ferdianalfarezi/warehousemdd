<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\GeneralCheckup;
use App\Models\Schedule;
use App\Models\CheckIndicatorStandard;
use App\Models\CheckupDetail;
use App\Models\CheckupPartReplacement;
use App\Models\HistoryCheckup;
use App\Models\HistoryCheckupDetail;
use App\Models\HistoryCheckupPartReplacement;
use App\Models\InhouseRequest;
use App\Models\OuthouseRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GeneralCheckupController extends Controller
{
    private const ACTIVE_STATUS = ['pending', 'on_process'];

    /**
     * Display a listing of the resource (paginated)
     */
    public function index(Request $request)
    {
        // Auto populate otomatis tiap halaman dibuka (tanpa harus klik tombol).
        // Gagal populate tidak boleh bikin halaman error.
        try {
            $this->populateFromSchedules();
        } catch (\Exception $e) {
            Log::error('Auto Populate (index) Error: ' . $e->getMessage());
        }

        $perPage = max(1, (int) ($request->per_page ?: 20));

        $checkups = GeneralCheckup::with(['barang', 'schedule', 'starter:id,nama'])
            ->whereIn('status', self::ACTIVE_STATUS)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('kode_barang', 'like', "%{$search}%")
                       ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('line'), fn ($q) => $q->where('line', $request->line))
            ->when($request->filled('customer'), fn ($q) => $q->whereHas('barang', fn ($b) => $b->where('cust', $request->customer)))
            ->when(
                in_array($request->status, self::ACTIVE_STATUS, true),
                fn ($q) => $q->where('status', $request->status)
            )
            ->orderBy('tanggal_terjadwal', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Stats: 1 query, dihitung dari semua data aktif (bukan per halaman)
        $agg = GeneralCheckup::whereIn('status', self::ACTIVE_STATUS)
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(status = 'pending'), 0) as pending,
                COALESCE(SUM(status = 'on_process'), 0) as on_process,
                COALESCE(SUM(DATE(tanggal_terjadwal) = ?), 0) as today
            ", [today()->toDateString()])
            ->first();

        $stats = [
            'total'      => (int) $agg->total,
            'pending'    => (int) $agg->pending,
            'on_process' => (int) $agg->on_process,
            'today'      => (int) $agg->today,
        ];

        $lines = GeneralCheckup::whereIn('status', self::ACTIVE_STATUS)
            ->whereNotNull('line')
            ->distinct()
            ->orderBy('line')
            ->pluck('line');

        // Customer dari barang yang punya checkup aktif (sama seperti filter customer di Request Repair)
        $customers = Barang::whereIn('id', GeneralCheckup::whereIn('status', self::ACTIVE_STATUS)->select('barang_id'))
            ->whereNotNull('cust')
            ->where('cust', '!=', '')
            ->distinct()
            ->orderBy('cust')
            ->pluck('cust');

        // Pilihan PIC di modal edit (semua user aktif selain operator)
        $pics = User::where('role_id', '!=', 4)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return view('general-checkups.index', compact('checkups', 'stats', 'lines', 'customers', 'pics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('general-checkups.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Use auto-populate endpoint'
        ], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralCheckup $generalCheckup)
    {
        $generalCheckup->load(['barang', 'schedule', 'details.checkIndicatorStandard', 'partReplacements.part']);

        return response()->json([
            'success' => true,
            'data' => $generalCheckup
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralCheckup $generalCheckup)
    {
        return redirect()->route('general-checkups.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneralCheckup $generalCheckup)
    {
        if (!in_array($generalCheckup->status, self::ACTIVE_STATUS, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Checkup yang sudah selesai tidak bisa diedit!'
            ], 400);
        }

        $isOnProcess = $generalCheckup->status === 'on_process';

        // Mulai perbaikan & PIC hanya bisa diedit kalau checkup sudah on process
        $rules = ['tanggal_terjadwal' => 'required|date'];
        if ($isOnProcess) {
            $rules['mulai_perbaikan'] = 'required|date|before_or_equal:now';
            $rules['started_by']      = 'required|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'tanggal_terjadwal.required'      => 'Tanggal terjadwal wajib diisi.',
            'mulai_perbaikan.required'        => 'Mulai perbaikan wajib diisi.',
            'mulai_perbaikan.before_or_equal' => 'Mulai perbaikan tidak boleh lebih dari waktu sekarang.',
            'started_by.required'             => 'PIC wajib dipilih.',
            'started_by.exists'               => 'PIC tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $generalCheckup->tanggal_terjadwal = $request->tanggal_terjadwal;

            if ($isOnProcess) {
                $generalCheckup->mulai_perbaikan = $request->mulai_perbaikan;
                $generalCheckup->tanggal_checkup = $generalCheckup->mulai_perbaikan->toDateString();
                $generalCheckup->started_by      = $request->started_by;
            }

            $generalCheckup->save();

            return response()->json([
                'success' => true,
                'message' => 'Checkup berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            Log::error('Update Checkup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate checkup!'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralCheckup $generalCheckup)
    {
        DB::beginTransaction();
        try {
            $generalCheckup->details()->delete();
            $generalCheckup->partReplacements()->delete();
            $generalCheckup->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkup berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Checkup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus checkup!'
            ], 500);
        }
    }

    /**
     * Auto populate dari schedule yang sudah jatuh tempo (tombol manual)
     */
    public function autoPopulate()
    {
        try {
            $result = $this->populateFromSchedules();

            return response()->json([
                'success' => true,
                'message' => "Auto-populate selesai! Created: {$result['created']}, Skipped: {$result['skipped']}",
                'created' => $result['created'],
                'skipped' => $result['skipped']
            ]);
        } catch (\Exception $e) {
            Log::error('Auto Populate Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal auto-populate checkup!'
            ], 500);
        }
    }

    /**
     * Buat checkup pending untuk schedule yang sudah jatuh tempo (hari ini / terlambat)
     * dan belum punya checkup aktif. Dipanggil otomatis tiap index dibuka + tombol Auto Populate.
     *
     * Jatuh tempo dihitung dari tanggal service_berikutnya, bukan kolom status schedule
     * (kolom status cuma di-update saat schedule di-save, jadi bisa basi).
     */
    private function populateFromSchedules(): array
    {
        // Lock biar 2 user yang buka bersamaan tidak bikin checkup dobel
        $lock = Cache::lock('general-checkups:auto-populate', 30);
        if (!$lock->get()) {
            return ['created' => 0, 'skipped' => 0];
        }

        try {
            $dueQuery = Schedule::whereDate('service_berikutnya', '<=', today());

            $activeScheduleIds = GeneralCheckup::whereIn('status', self::ACTIVE_STATUS)
                ->whereNotNull('schedule_id')
                ->select('schedule_id');

            $schedules = (clone $dueQuery)
                ->whereNotIn('id', $activeScheduleIds)
                ->with('barang.line')
                ->get();

            DB::transaction(function () use ($schedules) {
                foreach ($schedules as $schedule) {
                    GeneralCheckup::create([
                        'schedule_id'       => $schedule->id,
                        'barang_id'         => $schedule->barang_id,
                        'kode_barang'       => $schedule->kode_barang,
                        'gambar'            => $schedule->gambar,
                        'nama'              => $schedule->nama,
                        'line'              => $schedule->barang->line->nama_line ?? null,
                        'tanggal_terjadwal' => $schedule->service_berikutnya,
                        'status'            => 'pending',
                    ]);
                }
            });

            $created = $schedules->count();

            return [
                'created' => $created,
                'skipped' => $dueQuery->count() - $created,
            ];
        } finally {
            $lock->release();
        }
    }

    /**
     * Start repair process
     */
    public function startRepair(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $checkup = GeneralCheckup::findOrFail($id);

            if ($checkup->mulai_perbaikan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Perbaikan sudah dimulai sebelumnya!'
                ], 400);
            }

            $checkup->mulai_perbaikan = now();
            $checkup->started_by = auth()->id(); // PIC yang memulai perbaikan
            $checkup->tanggal_checkup = $checkup->tanggal_checkup ?? today();
            $checkup->status = 'on_process';
            $checkup->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perbaikan dimulai!',
                'redirect' => route('general-checkups.process', $id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Start Repair Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai perbaikan!'
            ], 500);
        }
    }

    /**
     * Show process page
     */
    public function process($id)
    {
        $checkup = GeneralCheckup::with([
            'barang.checkIndicators.standards',
            'details.checkIndicatorStandard',
            'details.partReplacements.part',
            'schedule'
        ])->findOrFail($id);

        // Auto-start repair if not started yet (safety check)
        if (!$checkup->mulai_perbaikan) {
            $checkup->mulai_perbaikan = now();
            $checkup->started_by = auth()->id(); // PIC yang memulai perbaikan
            $checkup->tanggal_checkup = today();
            $checkup->status = 'on_process';
            $checkup->save();
        }

        // Pakai relasi yang sudah di-eager load, tanpa query ulang
        $indicators = $checkup->barang->checkIndicators;

        return view('general-checkups.process', compact('checkup', 'indicators'));
    }

    /**
     * Save checkup (temporary save - on_process)
     */
    public function saveCheckup(Request $request, $id)
    {
        $validator = $this->makeDetailValidator($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkup = GeneralCheckup::findOrFail($id);

            $detailId = $this->saveDetails($checkup, $request->details ?? []);

            $checkup->catatan_umum = $request->catatan_umum;
            $checkup->status = 'on_process';
            $checkup->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkup berhasil disimpan sementara!',
                'detail_id' => $detailId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Checkup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan checkup!'
            ], 500);
        }
    }

    /**
     * Finish checkup and move to history
     */
    public function finishCheckup(Request $request, $id)
    {
        $validator = $this->makeDetailValidator($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkup = GeneralCheckup::findOrFail($id);

            $this->saveDetails($checkup, $request->details ?? []);

            // Masih ada NG yang belum closed?
            $hasNGNotClosed = $checkup->details()
                ->where('status', 'ng')
                ->where(function ($query) {
                    $query->whereNull('ng_action_status')
                          ->orWhere('ng_action_status', '!=', 'closed');
                })
                ->exists();

            if ($hasNGNotClosed) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada status NG yang belum diselesaikan! Gunakan "Simpan Sementara" untuk menyimpan.'
                ], 400);
            }

            // Commit semua part - kurangi stock (save per model biar boot method jalan)
            $uncommittedParts = CheckupPartReplacement::where('general_checkup_id', $checkup->id)
                ->where('is_committed', false)
                ->get();

            foreach ($uncommittedParts as $part) {
                $part->is_committed = true;
                $part->save();
            }

            $checkup->tanggal_checkup = $checkup->tanggal_checkup ?? today();
            $checkup->mulai_perbaikan = $checkup->mulai_perbaikan ?? now();
            $checkup->waktu_selesai = now();
            $checkup->status = 'finish';
            $checkup->catatan_umum = $request->catatan_umum;
            $checkup->save();

            // Load sekali semua yang dibutuhkan moveToHistory
            $checkup->load([
                'details.checkIndicator',
                'details.checkIndicatorStandard',
                'partReplacements.part',
            ]);

            $this->moveToHistory($checkup);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkup selesai! Stock part telah dikurangi dan dipindahkan ke history.',
                'redirect' => route('general-checkups.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Finish Checkup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan checkup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validator untuk save & finish (details boleh kosong)
     */
    private function makeDetailValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'details' => 'nullable|array',
            'details.*.check_indicator_standard_id' => 'required_with:details|exists:check_indicator_standards,id',
            'details.*.status' => 'required_with:details|in:ok,ng',
            'details.*.catatan' => 'nullable|string',
            'catatan_umum' => 'nullable|string',
        ]);
    }

    /**
     * Update or create details, return id detail terakhir
     */
    private function saveDetails(GeneralCheckup $checkup, array $details): ?int
    {
        if (empty($details)) {
            return null;
        }

        // Ambil semua standard sekaligus: [standard_id => check_indicator_id]
        $standardMap = CheckIndicatorStandard::whereIn('id', array_column($details, 'check_indicator_standard_id'))
            ->pluck('check_indicator_id', 'id');

        $detailId = null;

        foreach ($details as $detailData) {
            $standardId = $detailData['check_indicator_standard_id'];

            $detail = CheckupDetail::updateOrCreate(
                [
                    'general_checkup_id' => $checkup->id,
                    'check_indicator_standard_id' => $standardId,
                ],
                [
                    'check_indicator_id' => $standardMap[$standardId] ?? null,
                    'status' => $detailData['status'],
                    'catatan' => $detailData['catatan'] ?? null,
                ]
            );

            $detailId = $detail->id;
        }

        return $detailId;
    }

    /**
     * Move checkup to history
     * Butuh: details.checkIndicator, details.checkIndicatorStandard, partReplacements.part sudah di-load
     */
    private function moveToHistory(GeneralCheckup $checkup)
    {
        Log::info('Moving to history, checkup ID: ' . $checkup->id);

        $history = HistoryCheckup::create([
            'schedule_id'       => $checkup->schedule_id,
            'barang_id'         => $checkup->barang_id,
            'kode_barang'       => $checkup->kode_barang,
            'gambar'            => $checkup->gambar,
            'nama'              => $checkup->nama,
            'line'              => $checkup->line,
            'tanggal_terjadwal' => $checkup->tanggal_terjadwal,
            'tanggal_checkup'   => $checkup->tanggal_checkup,
            'mulai_perbaikan'   => $checkup->mulai_perbaikan,
            'waktu_selesai'     => $checkup->waktu_selesai,
            'durasi_perbaikan'  => $checkup->calculateDuration(),
            'status'            => 'finish',
            'catatan_umum'      => $checkup->catatan_umum,
            'total_ok'          => $checkup->details->where('status', 'ok')->count(),
            'total_ng'          => $checkup->details->where('status', 'ng')->count(),
            'total_part_used'   => $checkup->partReplacements->sum('quantity_used'),
        ]);

        // Prefetch inhouse & outhouse sekali query, key by checkup_detail_id
        $inhouseRequests = InhouseRequest::where('general_checkup_id', $checkup->id)
            ->orderBy('id')
            ->get()
            ->unique('checkup_detail_id')
            ->keyBy('checkup_detail_id');

        $outhouseRequests = OuthouseRequest::where('general_checkup_id', $checkup->id)
            ->orderBy('id')
            ->get()
            ->unique('checkup_detail_id')
            ->keyBy('checkup_detail_id');

        $allParts = $checkup->partReplacements;

        foreach ($checkup->details as $detail) {
            $ngActionData = null;

            if ($detail->ng_action_type === 'inhouse' && isset($inhouseRequests[$detail->id])) {
                $req = $inhouseRequests[$detail->id];
                $ngActionData = [
                    'problem'          => $req->problem,
                    'proses_dilakukan' => $req->proses_dilakukan,
                    'mesin'            => $req->mesin,
                    'status'           => $req->status,
                ];
            } elseif ($detail->ng_action_type === 'outhouse' && isset($outhouseRequests[$detail->id])) {
                $req = $outhouseRequests[$detail->id];
                $ngActionData = [
                    'problem'  => $req->problem,
                    'mesin'    => $req->mesin,
                    'supplier' => $req->supplier,
                    'status'   => $req->status,
                ];
            }

            $historyDetail = HistoryCheckupDetail::create([
                'history_checkup_id'          => $history->id,
                'check_indicator_id'          => $detail->check_indicator_id,
                'check_indicator_standard_id' => $detail->check_indicator_standard_id,
                'nama_bagian'                 => $detail->checkIndicator->nama_bagian ?? '-',
                'poin'                        => $detail->checkIndicatorStandard->poin ?? '-',
                'status'                      => $detail->status,
                'catatan'                     => $detail->catatan,
                'ng_action_type'              => $detail->ng_action_type,
                'ng_action_status'            => $detail->ng_action_status,
                'ng_action_data'              => $ngActionData,
            ]);

            // Part yang terhubung ke detail ini (filter collection, tanpa query)
            foreach ($allParts->where('checkup_detail_id', $detail->id) as $partReplacement) {
                $this->createHistoryPart($history->id, $historyDetail->id, $partReplacement);
            }
        }

        // Part umum (tidak terhubung ke detail)
        foreach ($allParts->whereNull('checkup_detail_id') as $partReplacement) {
            $this->createHistoryPart($history->id, null, $partReplacement);
        }

        // Update schedule
        $schedule = Schedule::find($checkup->schedule_id);
        if ($schedule) {
            $schedule->terakhir_service = $checkup->waktu_selesai;
            $schedule->calculateNextService();
            $schedule->save();
        }

        // Hapus data checkup aktif
        InhouseRequest::where('general_checkup_id', $checkup->id)->delete();
        OuthouseRequest::where('general_checkup_id', $checkup->id)->delete();
        CheckupPartReplacement::where('general_checkup_id', $checkup->id)->delete();
        CheckupDetail::where('general_checkup_id', $checkup->id)->delete();
        $checkup->delete();

        Log::info('Successfully moved to history, history ID: ' . $history->id);

        return $history;
    }

    private function createHistoryPart(int $historyId, ?int $historyDetailId, $partReplacement): void
    {
        HistoryCheckupPartReplacement::create([
            'history_checkup_id'        => $historyId,
            'history_checkup_detail_id' => $historyDetailId,
            'part_id'                   => $partReplacement->part_id,
            'kode_part'                 => $partReplacement->part->kode_part ?? '-',
            'nama_part'                 => $partReplacement->part->nama ?? '-',
            'quantity_used'             => $partReplacement->quantity_used,
            'catatan'                   => $partReplacement->catatan,
        ]);
    }
}