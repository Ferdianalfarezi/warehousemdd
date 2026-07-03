<?php

namespace App\Http\Controllers;

use App\Models\RequestRepair;
use App\Models\RequestRepairHistory;
use App\Models\Barang;
use App\Models\DiesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestRepairController extends Controller
{
    // ── Index ───────────────────────────────────────────────
    public function index()
    {
        return view('request_repairs.index');
    }

    // ── AJAX: table data ────────────────────────────────────
    public function getData(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = $request->get('per_page', 20);
        $page    = (int) $request->get('page', 1);

        $query = RequestRepair::with(['barang:id,kode_barang,nama'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('no',                'like', "%{$search}%")
                       ->orWhere('part_no',         'like', "%{$search}%")
                       ->orWhere('nama',            'like', "%{$search}%")
                       ->orWhere('customer',        'like', "%{$search}%")
                       ->orWhere('line_mesin',      'like', "%{$search}%")
                       ->orWhere('process_no',      'like', "%{$search}%")
                       ->orWhere('kategori_problem', 'like', "%{$search}%")
                       ->orWhere('status',          'like', "%{$search}%");
                });
            });

        $total = $query->count();

        if ($perPage === 'all') {
            $items      = $query->latest()->get();
            $perPageInt = $total ?: 1;
        } else {
            $perPageInt = (int) $perPage;
            $items      = $query->latest()->skip(($page - 1) * $perPageInt)->take($perPageInt)->get();
        }

        $totalPages = $perPage === 'all' ? 1 : (int) ceil($total / max($perPageInt, 1));
        $from       = $total > 0 ? (($page - 1) * $perPageInt) + 1 : 0;
        $to         = min($page * $perPageInt, $total);

        $userRoleId = auth()->user()->role_id ?? null;

        $data = $items->map(function ($rr, $idx) use ($page, $perPageInt, $userRoleId) {
            return [
                'id'                => $rr->id,
                'row_number'        => (($page - 1) * $perPageInt) + $idx + 1,
                'no'                => $rr->no,
                'tanggal_pengajuan' => $rr->tanggal_pengajuan?->format('d/m/Y'),
                'group'             => $rr->group,
                'shift'             => $rr->shift,
                'jumlah_stroke'     => $rr->jumlah_stroke,
                'line_mesin'        => $rr->line_mesin,
                'part_no'           => $rr->part_no,
                'nama'              => $rr->nama,
                'process_no'        => $rr->process_no,
                'customer'          => $rr->customer,
                'jenis'             => $rr->jenis,
                'target_selesai'    => $rr->target_selesai?->format('d/m/Y'),
                'kategori_problem'  => $rr->kategori_problem,
                'detail_proyek'     => $rr->detail_proyek,
                'status'            => $rr->status,
                'can_edit'          => $rr->isEditable(),
                'can_delete'        => $rr->isEditable(),
                'can_to_on_trial'   => $rr->canConfirmToOnTrial()
                                        && in_array($userRoleId, RequestRepair::ROLES_TO_ON_TRIAL),
                'can_to_closed'     => $rr->canConfirmToClosed()
                                        && in_array($userRoleId, RequestRepair::ROLES_TO_CLOSED),
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

    // ── AJAX: search barang ─────────────────────────────────
    public function searchBarang(Request $request)
    {
        $q = $request->get('q', '');

        $barangs = Barang::when($q, function ($query) use ($q) {
                $query->where('kode_barang', 'like', "%{$q}%")
                      ->orWhere('nama',       'like', "%{$q}%");
            })
            ->select('id', 'kode_barang', 'nama', 'cust')
            ->limit(30)
            ->get();

        $results = $barangs->map(fn($b) => [
            'id'       => $b->id,
            'text'     => "{$b->kode_barang} — {$b->nama}",
            'kode'     => $b->kode_barang,
            'nama'     => $b->nama,
            'customer' => $b->cust ?? '',
        ]);

        return response()->json(['results' => $results]);
    }

    // ── AJAX: get process_no list by barang ─────────────────
    public function getProcessNos(Request $request)
    {
        $barangId = $request->get('barang_id');
        if (!$barangId) return response()->json(['data' => []]);

        $processNos = DiesDetail::where('barang_id', $barangId)
            ->whereNotNull('process_no')
            ->where('process_no', '!=', '')
            ->orderBy('sort_order')
            ->pluck('process_no')
            ->unique()
            ->values();

        return response()->json(['data' => $processNos]);
    }

    // ── AJAX: get durasi realtime ────────────────────────────
    public function getDurasi(RequestRepair $requestRepair)
    {
        $start   = $requestRepair->on_process_at ?? $requestRepair->created_at;
        $seconds = $start ? (int) $start->diffInSeconds(now()) : 0;

        return response()->json([
            'success'          => true,
            'seconds'          => $seconds,
            'durasi_formatted' => RequestRepair::formatDurasi($seconds),
            'started_at'       => $start?->toISOString(),
        ]);
    }

    // ── Store ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pengajuan' => 'required|date',
            'group'             => 'required|in:A,B',
            'shift'             => 'required|in:Pagi,Siang,Malam',
            'jumlah_stroke'     => 'required|integer|min:0',
            'line_mesin'        => 'nullable|string|max:100',
            'barang_id'         => 'required|exists:barangs,id',
            'process_no'        => 'nullable|string|max:100',
            'jenis'             => 'required|in:Milik Sendiri,Eksternal',
            'target_selesai'    => 'nullable|date',
            'kategori_problem'  => 'required|in:Dies,Burry,Dimensi,Human Error,Accessories',
            'detail_proyek'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);

            $rr = RequestRepair::create([
                'no'                => RequestRepair::generateNo(),
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'group'             => $request->group,
                'shift'             => $request->shift,
                'jumlah_stroke'     => $request->jumlah_stroke,
                'line_mesin'        => $request->line_mesin,
                'barang_id'         => $barang->id,
                'part_no'           => $barang->kode_barang,
                'nama'              => $barang->nama,
                'process_no'        => $request->process_no,
                'customer'          => $barang->cust,
                'jenis'             => $request->jenis,
                'target_selesai'    => $request->target_selesai,
                'kategori_problem'  => $request->kategori_problem,
                'detail_proyek'     => $request->detail_proyek,
                'status'            => RequestRepair::STATUS_ON_PROCESS,
                'on_process_at'     => now(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Request Repair berhasil ditambahkan!', 'data' => $rr]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RequestRepair Store Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── Show ────────────────────────────────────────────────
    public function show(RequestRepair $requestRepair)
    {
        $requestRepair->load('barang:id,kode_barang,nama,cust');

        $data             = $requestRepair->toArray();
        $data['timeline'] = $requestRepair->getTimelineData();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── Update ──────────────────────────────────────────────
    public function update(Request $request, RequestRepair $requestRepair)
    {
        if (!$requestRepair->isEditable()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat diedit karena status sudah ' . $requestRepair->status . '.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_pengajuan' => 'required|date',
            'group'             => 'required|in:A,B',
            'shift'             => 'required|in:Pagi,Siang,Malam',
            'jumlah_stroke'     => 'required|integer|min:0',
            'line_mesin'        => 'nullable|string|max:100',
            'barang_id'         => 'required|exists:barangs,id',
            'process_no'        => 'nullable|string|max:100',
            'jenis'             => 'required|in:Milik Sendiri,Eksternal',
            'target_selesai'    => 'nullable|date',
            'kategori_problem'  => 'required|in:Dies,Burry,Dimensi,Human Error,Accessories',
            'detail_proyek'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);

            $requestRepair->update([
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'group'             => $request->group,
                'shift'             => $request->shift,
                'jumlah_stroke'     => $request->jumlah_stroke,
                'line_mesin'        => $request->line_mesin,
                'barang_id'         => $barang->id,
                'part_no'           => $barang->kode_barang,
                'nama'              => $barang->nama,
                'process_no'        => $request->process_no,
                'customer'          => $barang->cust,
                'jenis'             => $request->jenis,
                'target_selesai'    => $request->target_selesai,
                'kategori_problem'  => $request->kategori_problem,
                'detail_proyek'     => $request->detail_proyek,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Request Repair berhasil diupdate!', 'data' => $requestRepair]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── Update Status ───────────────────────────────────────
    public function updateStatus(Request $request, RequestRepair $requestRepair)
    {
        $userRoleId = auth()->user()->role_id ?? null;
        $newStatus  = $request->get('status');

        // ════════════════════════════════════════════════════
        // ON TRIAL
        // ════════════════════════════════════════════════════
        if ($newStatus === RequestRepair::STATUS_ON_TRIAL) {

            if (!$requestRepair->canConfirmToOnTrial()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak bisa diubah ke On Trial dari status saat ini.',
                ], 422);
            }
            if (!in_array($userRoleId, RequestRepair::ROLES_TO_ON_TRIAL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah status ke On Trial.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                // Section 1 — Tindakan Perbaikan (required)
                'analisa_penyebab'              => 'required|string|max:255',
                'tindakan_perbaikan'            => 'required|string|max:255',
                'catatan_penggantian_sparepart' => 'required|string|max:255',
                // Section 2 — Penanganan Problem Burry (nullable)
                'item'            => 'nullable|string|max:255',
                'proses_grinding' => 'nullable|string|max:255',
                'shim_up'         => 'nullable|string|max:255',
                'status_burry'    => 'nullable|in:OK,NG',
                'standart_burry'  => 'nullable|in:OK,NG',
                'group_leader'    => 'nullable|string|max:255',
                'operator'        => 'nullable|string|max:255',
                // Section 3 — Target Trial After Repair (nullable)
                'plan'   => 'nullable|string|max:255',
                'actual' => 'nullable|string|max:255',
                'remark' => 'nullable|string|max:255',
                'judge'  => 'nullable|in:OK,NG',
            ], [
                'analisa_penyebab.required'              => 'Analisa penyebab wajib diisi.',
                'tindakan_perbaikan.required'            => 'Tindakan perbaikan wajib diisi.',
                'catatan_penggantian_sparepart.required' => 'Catatan penggantian sparepart wajib diisi.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $requestRepair->update([
                'status'                        => RequestRepair::STATUS_ON_TRIAL,
                // Section 1
                'analisa_penyebab'              => $request->analisa_penyebab,
                'tindakan_perbaikan'            => $request->tindakan_perbaikan,
                'catatan_penggantian_sparepart' => $request->catatan_penggantian_sparepart,
                // Section 2
                'item'                          => $request->item,
                'proses_grinding'               => $request->proses_grinding,
                'shim_up'                       => $request->shim_up,
                'status_burry'                  => $request->status_burry,
                'standart_burry'                => $request->standart_burry,
                'group_leader'                  => $request->group_leader,
                'operator'                      => $request->operator,
                // Section 3
                'plan'                          => $request->plan,
                'actual'                        => $request->actual,
                'remark'                        => $request->remark,
                'judge'                         => $request->judge,
                'on_trial_at'                   => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Status berhasil diubah ke On Trial!']);

        // ════════════════════════════════════════════════════
        // CLOSED → pindah ke history, hard delete dari request_repairs
        // ════════════════════════════════════════════════════
        } elseif ($newStatus === RequestRepair::STATUS_CLOSED) {

            if (!$requestRepair->canConfirmToClosed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak bisa diubah ke Closed dari status saat ini.',
                ], 422);
            }
            if (!in_array($userRoleId, RequestRepair::ROLES_TO_CLOSED)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah status ke Closed.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                // Section 1 — Monitoring Dies Temporary (nullable)
                'tanggal_cek'       => 'nullable|date',
                'lot_prod'          => 'nullable|string|max:255',
                'awal'              => 'nullable|in:OK,NG',
                'tengah'            => 'nullable|in:OK,NG',
                'akhir'             => 'nullable|in:OK,NG',
                'qty'               => 'nullable|in:OK,NG',
                'remark_monitoring' => 'nullable|string|max:255',
                'judge_monitoring'  => 'nullable|in:OK,NG',
                // Section 2 — Target Permanen Action (nullable)
                'plan_permanen'     => 'nullable|string|max:255',
                'actual_permanen'   => 'nullable|string|max:255',
                'rootcause'         => 'nullable|string|max:255',
                'recovery'          => 'nullable|string|max:255',
                'assy_trial_check'  => 'nullable|string|max:255',
                'judge_permanen'    => 'nullable|in:OK,NG',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            try {
                $closedAt = now();

                // ── Hitung durasi tiap fase (detik)
                $durasiOnProcessSeconds = null;
                $durasiOnTrialSeconds   = null;
                $durasiTotalSeconds     = null;

                if ($requestRepair->on_process_at && $requestRepair->on_trial_at) {
                    $durasiOnProcessSeconds = (int) $requestRepair->on_process_at
                        ->diffInSeconds($requestRepair->on_trial_at);
                }
                if ($requestRepair->on_trial_at) {
                    $durasiOnTrialSeconds = (int) $requestRepair->on_trial_at
                        ->diffInSeconds($closedAt);
                }
                if ($requestRepair->on_process_at) {
                    $durasiTotalSeconds = (int) $requestRepair->on_process_at
                        ->diffInSeconds($closedAt);
                }

                // ── repair_count: berapa kali part_no ini sudah di-history + 1
                $repairCount = RequestRepairHistory::where('part_no', $requestRepair->part_no)->count() + 1;

                // ── Salin ke tabel history
                RequestRepairHistory::create([
                    'barang_id'                     => $requestRepair->barang_id,
                    'no'                            => $requestRepair->no,
                    'tanggal_pengajuan'             => $requestRepair->tanggal_pengajuan,
                    'group'                         => $requestRepair->group,
                    'shift'                         => $requestRepair->shift,
                    'jumlah_stroke'                 => $requestRepair->jumlah_stroke,
                    'line_mesin'                    => $requestRepair->line_mesin,
                    'part_no'                       => $requestRepair->part_no,
                    'nama'                          => $requestRepair->nama,
                    'process_no'                    => $requestRepair->process_no,
                    'customer'                      => $requestRepair->customer,
                    'jenis'                         => $requestRepair->jenis,
                    'target_selesai'                => $requestRepair->target_selesai,
                    'kategori_problem'              => $requestRepair->kategori_problem,
                    'detail_proyek'                 => $requestRepair->detail_proyek,
                    // On Trial — Section 1
                    'analisa_penyebab'              => $requestRepair->analisa_penyebab,
                    'tindakan_perbaikan'            => $requestRepair->tindakan_perbaikan,
                    'catatan_penggantian_sparepart' => $requestRepair->catatan_penggantian_sparepart,
                    // On Trial — Section 2
                    'item'                          => $requestRepair->item,
                    'proses_grinding'               => $requestRepair->proses_grinding,
                    'shim_up'                       => $requestRepair->shim_up,
                    'status_burry'                  => $requestRepair->status_burry,
                    'standart_burry'                => $requestRepair->standart_burry,
                    'group_leader'                  => $requestRepair->group_leader,
                    'operator'                      => $requestRepair->operator,
                    // On Trial — Section 3
                    'plan'                          => $requestRepair->plan,
                    'actual'                        => $requestRepair->actual,
                    'remark'                        => $requestRepair->remark,
                    'judge'                         => $requestRepair->judge,
                    // Closed — Section 1: Monitoring Dies Temporary
                    'tanggal_cek'                   => $request->tanggal_cek,
                    'lot_prod'                      => $request->lot_prod,
                    'awal'                          => $request->awal,
                    'tengah'                        => $request->tengah,
                    'akhir'                         => $request->akhir,
                    'qty'                           => $request->qty,
                    'remark_monitoring'             => $request->remark_monitoring,
                    'judge_monitoring'              => $request->judge_monitoring,
                    // Closed — Section 2: Target Permanen Action
                    'plan_permanen'                 => $request->plan_permanen,
                    'actual_permanen'               => $request->actual_permanen,
                    'rootcause'                     => $request->rootcause,
                    'recovery'                      => $request->recovery,
                    'assy_trial_check'              => $request->assy_trial_check,
                    'judge_permanen'                => $request->judge_permanen,
                    // Timestamps & durasi
                    'on_process_at'                 => $requestRepair->on_process_at,
                    'on_trial_at'                   => $requestRepair->on_trial_at,
                    'closed_at'                     => $closedAt,
                    'durasi_on_process_seconds'     => $durasiOnProcessSeconds,
                    'durasi_on_trial_seconds'       => $durasiOnTrialSeconds,
                    'durasi_total_seconds'          => $durasiTotalSeconds,
                    'repair_count'                  => $repairCount,
                ]);

                // ── Hard delete dari request_repairs
                $requestRepair->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diubah ke Closed! Data dipindahkan ke History.',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('RequestRepair Close Error', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }

        } else {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 422);
        }
    }

    // ── Destroy ─────────────────────────────────────────────
    public function destroy(RequestRepair $requestRepair)
    {
        if (!$requestRepair->isEditable()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena status sudah ' . $requestRepair->status . '.',
            ], 403);
        }

        try {
            $requestRepair->delete();
            return response()->json(['success' => true, 'message' => 'Request Repair berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus!'], 500);
        }
    }
}