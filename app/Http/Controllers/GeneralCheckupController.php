<?php

namespace App\Http\Controllers;

use App\Models\GeneralCheckup;
use App\Models\Schedule;
use App\Models\Barang;
use App\Models\CheckupDetail;
use App\Models\CheckupPartReplacement;
use App\Models\HistoryCheckup;
use App\Models\HistoryCheckupDetail;
use App\Models\HistoryCheckupPartReplacement;
use Illuminate\Http\Request;
use App\Models\InhouseRequest;
use App\Models\OuthouseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeneralCheckupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only show pending and on_process checkups (exclude finish)
        $checkups = GeneralCheckup::with(['barang', 'schedule'])
            ->whereIn('status', ['pending', 'on_process']) // ✅ Filter hanya pending & on_process
            ->orderBy('tanggal_terjadwal', 'asc')
            ->get();

        // Stats
        $stats = [
            'total' => $checkups->count(),
            'pending' => $checkups->where('status', 'pending')->count(),
            'on_process' => $checkups->where('status', 'on_process')->count(),
            'today' => $checkups->where('tanggal_terjadwal', Carbon::today())->count(),
        ];

        // Get unique lines for filter
        $lines = Barang::distinct()->pluck('line')->filter()->sort()->values();

        return view('general-checkups.index', compact('checkups', 'stats', 'lines'));
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
        // This will be used by auto-populate
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
        // Not used for now
        return response()->json([
            'success' => false,
            'message' => 'Update not allowed'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralCheckup $generalCheckup)
    {
        DB::beginTransaction();
        try {
            // Delete related data first
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
     * Auto-populate checkups from schedules
     */
    public function autoPopulate()
    {
        DB::beginTransaction();
        try {
            // Get schedules with status "hari_ini" only (exclude "segera")
            $schedules = Schedule::with('barang')
                ->where('status', 'hari_ini')
                ->get();

            $created = 0;
            $skipped = 0;

            foreach ($schedules as $schedule) {
                // Check if already exists
                $exists = GeneralCheckup::where('schedule_id', $schedule->id)
                    ->whereIn('status', ['pending', 'on_process'])
                    ->exists();

                if (!$exists) {
                    GeneralCheckup::create([
                        'schedule_id' => $schedule->id,
                        'barang_id' => $schedule->barang_id,
                        'kode_barang' => $schedule->kode_barang,
                        'gambar' => $schedule->gambar,
                        'nama' => $schedule->nama,
                        'line' => $schedule->barang->line ?? null,
                        'tanggal_terjadwal' => $schedule->service_berikutnya,
                        'status' => 'pending',
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Auto-populate selesai! Created: {$created}, Skipped: {$skipped}",
                'created' => $created,
                'skipped' => $skipped
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto Populate Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal auto-populate checkup!'
            ], 500);
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

            // Validation
            if ($checkup->mulai_perbaikan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perbaikan sudah dimulai sebelumnya!'
                ], 400);
            }

            // Set start time
            $checkup->mulai_perbaikan = now();
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
            $checkup->tanggal_checkup = today();
            $checkup->status = 'on_process';
            $checkup->save();
        }

        // Get all check indicators for this barang
        $indicators = $checkup->barang->checkIndicators()
            ->with('standards')
            ->get();

        return view('general-checkups.process', compact('checkup', 'indicators'));
    }

    /**
     * Save checkup (temporary save - on_process)
     */
    public function saveCheckup(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'details' => 'required|array',
            'details.*.check_indicator_standard_id' => 'required|exists:check_indicator_standards,id',
            'details.*.status' => 'required|in:ok,ng',
            'details.*.catatan' => 'nullable|string',
            'catatan_umum' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkup = GeneralCheckup::findOrFail($id);
            $detailId = null;

            // Update or create details
            foreach ($request->details as $detailData) {
                $standard = \App\Models\CheckIndicatorStandard::findOrFail($detailData['check_indicator_standard_id']);
                
                $detail = CheckupDetail::updateOrCreate(
                    [
                        'general_checkup_id' => $checkup->id,
                        'check_indicator_standard_id' => $detailData['check_indicator_standard_id']
                    ],
                    [
                        'check_indicator_id' => $standard->check_indicator_id,
                        'status' => $detailData['status'],
                        'catatan' => $detailData['catatan'] ?? null,
                    ]
                );

                // Store the detail_id to return (for the last one or you can modify logic)
                $detailId = $detail->id;
            }

            // Update catatan umum
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
    $validator = \Validator::make($request->all(), [
        'details' => 'required|array',
        'details.*.check_indicator_standard_id' => 'required|exists:check_indicator_standards,id',
        'details.*.status' => 'required|in:ok,ng',
        'details.*.catatan' => 'nullable|string',
        'catatan_umum' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        $checkup = GeneralCheckup::with(['details', 'partReplacements'])->findOrFail($id);

        // Update or create details
        foreach ($request->details as $detailData) {
            $standard = \App\Models\CheckIndicatorStandard::findOrFail($detailData['check_indicator_standard_id']);
            
            CheckupDetail::updateOrCreate(
                [
                    'general_checkup_id' => $checkup->id,
                    'check_indicator_standard_id' => $detailData['check_indicator_standard_id']
                ],
                [
                    'check_indicator_id' => $standard->check_indicator_id,
                    'status' => $detailData['status'],
                    'catatan' => $detailData['catatan'] ?? null,
                ]
            );
        }

        // Reload checkup details after update
        $checkup->load('details');

        // Check if there's any NG that is NOT closed yet
        $hasNGNotClosed = $checkup->details()
            ->where('status', 'ng')
            ->where(function($query) {
                $query->whereNull('ng_action_status')
                      ->orWhereNotIn('ng_action_status', ['closed']);
            })
            ->exists();

        if ($hasNGNotClosed) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Masih ada status NG yang belum diselesaikan! Gunakan "Simpan Sementara" untuk menyimpan.'
            ], 400);
        }

        // Set finish time and ensure all timestamps are filled
        $checkup->tanggal_checkup = $checkup->tanggal_checkup ?? today();
        $checkup->mulai_perbaikan = $checkup->mulai_perbaikan ?? now();
        $checkup->waktu_selesai = now();
        $checkup->status = 'finish';
        $checkup->catatan_umum = $request->catatan_umum;
        $checkup->save();

        // Reload to get updated details
        $checkup->load(['details', 'partReplacements']);

        // Move to history
        $this->moveToHistory($checkup);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Checkup selesai dan telah dipindahkan ke history!',
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
     * Move checkup to history
     */
    /**
 * Move checkup to history
 */
/**
 * Move checkup to history
 */
private function moveToHistory(GeneralCheckup $checkup)
{
    Log::info('Moving to history, checkup ID: ' . $checkup->id);
    
    // Create history checkup
    $history = HistoryCheckup::create([
        'schedule_id' => $checkup->schedule_id,
        'barang_id' => $checkup->barang_id,
        'kode_barang' => $checkup->kode_barang,
        'gambar' => $checkup->gambar,
        'nama' => $checkup->nama,
        'line' => $checkup->line,
        'tanggal_terjadwal' => $checkup->tanggal_terjadwal,
        'tanggal_checkup' => $checkup->tanggal_checkup,
        'mulai_perbaikan' => $checkup->mulai_perbaikan,
        'waktu_selesai' => $checkup->waktu_selesai,
        'durasi_perbaikan' => $checkup->calculateDuration(),
        'status' => 'finish',
        'catatan_umum' => $checkup->catatan_umum,
        'total_ok' => $checkup->details->where('status', 'ok')->count(),
        'total_ng' => $checkup->details->where('status', 'ng')->count(),
        'total_part_used' => $checkup->partReplacements->sum('quantity_used'),
    ]);

    Log::info('History created, ID: ' . $history->id);

    // Copy details to history with NG action data
    foreach ($checkup->details as $detail) {
        $ngActionData = null;

        // Get NG action data based on type by querying directly
        if ($detail->ng_action_type === 'inhouse') {
            $inhouseRequest = InhouseRequest::where('checkup_detail_id', $detail->id)
                ->where('general_checkup_id', $checkup->id)
                ->first();
            
            if ($inhouseRequest) {
                Log::info('Processing inhouse request for detail: ' . $detail->id);
                $ngActionData = [
                    'problem' => $inhouseRequest->problem,
                    'proses_dilakukan' => $inhouseRequest->proses_dilakukan,
                    'mesin' => $inhouseRequest->mesin,
                    'status' => $inhouseRequest->status,
                ];
            }
        } elseif ($detail->ng_action_type === 'outhouse') {
            $outhouseRequest = OuthouseRequest::where('checkup_detail_id', $detail->id)
                ->where('general_checkup_id', $checkup->id)
                ->first();
            
            if ($outhouseRequest) {
                Log::info('Processing outhouse request for detail: ' . $detail->id);
                $ngActionData = [
                    'problem' => $outhouseRequest->problem,
                    'mesin' => $outhouseRequest->mesin,
                    'supplier' => $outhouseRequest->supplier,
                    'status' => $outhouseRequest->status,
                ];
            }
        }

        $historyDetail = HistoryCheckupDetail::create([
            'history_checkup_id' => $history->id,
            'check_indicator_id' => $detail->check_indicator_id,
            'check_indicator_standard_id' => $detail->check_indicator_standard_id,
            'nama_bagian' => $detail->checkIndicator->nama_bagian ?? '-',
            'poin' => $detail->checkIndicatorStandard->poin ?? '-',
            'status' => $detail->status,
            'catatan' => $detail->catatan,
            'ng_action_type' => $detail->ng_action_type,
            'ng_action_status' => $detail->ng_action_status,
            'ng_action_data' => $ngActionData,
        ]);

        Log::info('History detail created, ID: ' . $historyDetail->id . ', NG Action Type: ' . $detail->ng_action_type . ', NG Action Data: ' . json_encode($ngActionData));

        // Copy part replacements linked to this detail
        $detailParts = CheckupPartReplacement::where('checkup_detail_id', $detail->id)
            ->where('general_checkup_id', $checkup->id)
            ->get();
            
        foreach ($detailParts as $partReplacement) {
            HistoryCheckupPartReplacement::create([
                'history_checkup_id' => $history->id,
                'history_checkup_detail_id' => $historyDetail->id,
                'part_id' => $partReplacement->part_id,
                'kode_part' => $partReplacement->part->kode_part ?? '-',
                'nama_part' => $partReplacement->part->nama ?? '-',
                'quantity_used' => $partReplacement->quantity_used,
                'catatan' => $partReplacement->catatan,
            ]);
        }
    }

    // Copy general part replacements (not linked to specific detail)
    $generalParts = $checkup->partReplacements()->whereNull('checkup_detail_id')->get();
    foreach ($generalParts as $partReplacement) {
        HistoryCheckupPartReplacement::create([
            'history_checkup_id' => $history->id,
            'history_checkup_detail_id' => null,
            'part_id' => $partReplacement->part_id,
            'kode_part' => $partReplacement->part->kode_part ?? '-',
            'nama_part' => $partReplacement->part->nama ?? '-',
            'quantity_used' => $partReplacement->quantity_used,
            'catatan' => $partReplacement->catatan,
        ]);
    }

    // Update schedule
    $schedule = Schedule::find($checkup->schedule_id);
    if ($schedule) {
        $schedule->terakhir_service = $checkup->waktu_selesai;
        $schedule->calculateNextService();
        $schedule->save();
    }

    Log::info('Deleting general checkup and related data');

    // Delete inhouse and outhouse requests first
    InhouseRequest::where('general_checkup_id', $checkup->id)->delete();
    OuthouseRequest::where('general_checkup_id', $checkup->id)->delete();
    
    // Delete checkup part replacements
    CheckupPartReplacement::where('general_checkup_id', $checkup->id)->delete();
    
    // Delete checkup details
    CheckupDetail::where('general_checkup_id', $checkup->id)->delete();
    
    // Finally delete the checkup
    $checkup->delete();

    Log::info('Successfully moved to history');

    return $history;
}
}