<?php

namespace App\Http\Controllers;

use App\Models\RequestRepair;
use App\Models\RequestRepairHistory;
use App\Models\RequestRepairAttempt;
use App\Models\Barang;
use App\Models\DiesDetail;
use App\Models\Line;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RequestRepairController extends Controller
{
    // ── Index ───────────────────────────────────────────────
    public function index()
    {
        $lines = auth()->user()->lines()->orderBy('nama_line')->get();

        if ($lines->isEmpty()) {
            // fallback untuk user yang gak punya line ter-assign (misal admin)
            $lines = Line::orderBy('nama_line')->get();
        }

        return view('request_repairs.index', compact('lines'));
    }

    // ── AJAX: table data ────────────────────────────────────
    public function getData(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = $request->get('per_page', 20);
        $page    = (int) $request->get('page', 1);

        $query = RequestRepair::with(['barang:id,kode_barang,nama', 'creator:id,nama', 'pics:id,nama'])
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

        $authUser   = auth()->user();
        $userRoleId = $authUser->role_id ?? null;

        $data = $items->map(function ($rr, $idx) use ($page, $perPageInt, $userRoleId, $authUser) {
            return [
                'id'                => $rr->id,
                'row_number'        => (($page - 1) * $perPageInt) + $idx + 1,
                'no'                => $rr->no,
                'tanggal_pengajuan' => $rr->tanggal_pengajuan?->format('d/m/Y'),
                'group'             => $rr->group,
                'shift'             => $rr->shift,
                'jumlah_stroke'     => $rr->jumlah_stroke,
                'line_id'           => $rr->line_id,
                'line_mesin'        => $rr->line_mesin,
                'part_no'           => $rr->part_no,
                'nama'              => $rr->nama,
                'process_no'        => $rr->process_no,
                'customer'          => $rr->customer,
                'jenis'             => $rr->jenis,
                'kekuatan_stock_fg' => $rr->kekuatan_stock_fg,
                'kategori_problem'  => $rr->kategori_problem,
                'detail_proyek'     => $rr->detail_proyek,
                'gambar_url'        => $rr->gambar_url,
                'created_by_name'   => $rr->creator->nama ?? '-',
                'pic_names'         => $rr->picNamesString(),
                'status'            => $rr->status,
                'ng_attempt_count'  => $rr->ng_attempt_count, // ⬅️ baru
                'can_edit'          => $rr->isEditable(),
                'can_delete'        => $rr->isEditable(),
                'can_to_process'    => $rr->canConfirmToProcess()
                                        && in_array($userRoleId, RequestRepair::ROLES_TO_ON_PROCESS),
                // ⬅️ diubah: bukan lagi cuma cek role, tapi cek PIC / admin override
                'can_to_on_trial'   => $rr->canUserConfirmToOnTrial($authUser),
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

    // ── AJAX: kandidat PIC (role_id 1 & 7) ───────────────────
    // PENTING: route ini HARUS didaftarkan di atas route resource
    // request-repairs/{request_repair}, kalau tidak "pic-candidates"
    // akan tertangkap sebagai parameter {request_repair}.
    public function picCandidates()
    {
        $users = User::whereIn('role_id', RequestRepair::ROLES_TO_ON_PROCESS)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nik']);

        $results = $users->map(fn($u) => [
            'id'   => $u->id,
            'text' => $u->nama . ($u->nik ? " ({$u->nik})" : ''),
        ]);

        return response()->json(['results' => $results]);
    }

    // ── Store ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pengajuan' => 'required|date',
            'group'             => 'required|in:A,B',
            'shift'             => 'required|in:Pagi,Malam',
            'jumlah_stroke'     => 'required|integer|min:0',
            'line_id'           => 'nullable|exists:lines,id',
            'barang_id'         => 'required|exists:barangs,id',
            'process_no'        => 'nullable|string|max:100',
            'jenis'             => 'required|in:Milik Sendiri,Eksternal',
            'kekuatan_stock_fg' => 'nullable|integer|min:0',
            'kategori_problem'  => 'required|in:Dies,Burry,Dimensi,Human Error,Accessories',
            'detail_proyek'     => 'nullable|string',
            'gambar'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);
            $line   = $request->line_id ? Line::find($request->line_id) : null;

            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store('request_repairs', 'public');
            }

            $rr = RequestRepair::create([
                'no'                => RequestRepair::generateNo(),
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'group'             => $request->group,
                'shift'             => $request->shift,
                'jumlah_stroke'     => $request->jumlah_stroke,
                'line_id'           => $line->id ?? null,
                'line_mesin'        => $line ? ($line->nama_line . ' - ' . $line->mesin) : null,
                'barang_id'         => $barang->id,
                'part_no'           => $barang->kode_barang,
                'nama'              => $barang->nama,
                'process_no'        => $request->process_no,
                'customer'          => $barang->cust,
                'jenis'             => $request->jenis,
                'kekuatan_stock_fg' => $request->kekuatan_stock_fg,
                'kategori_problem'  => $request->kategori_problem,
                'detail_proyek'     => $request->detail_proyek,
                'gambar'            => $gambarPath,
                'created_by'        => auth()->id(),
                'status'            => RequestRepair::STATUS_OPEN,
                // on_process_at TIDAK diisi di sini — diisi saat dikonfirmasi ke On Process
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
        $requestRepair->load(['barang:id,kode_barang,nama,cust', 'creator:id,nama', 'pics:id,nama,nik']);

        $data                    = $requestRepair->toArray();
        $data['gambar_url']      = $requestRepair->gambar_url;
        $data['created_by_name'] = $requestRepair->creator->nama ?? '-';
        $data['pic_names']       = $requestRepair->picNamesString();
        $data['pics']            = $requestRepair->pics->map(fn($u) => ['id' => $u->id, 'nama' => $u->nama]);
        $data['timeline']        = $requestRepair->getTimelineData();

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
            'shift'             => 'required|in:Pagi,Malam',
            'jumlah_stroke'     => 'required|integer|min:0',
            'line_id'           => 'nullable|exists:lines,id',
            'barang_id'         => 'required|exists:barangs,id',
            'process_no'        => 'nullable|string|max:100',
            'jenis'             => 'required|in:Milik Sendiri,Eksternal',
            'kekuatan_stock_fg' => 'nullable|integer|min:0',
            'kategori_problem'  => 'required|in:Dies,Burry,Dimensi,Human Error,Accessories',
            'detail_proyek'     => 'nullable|string',
            'gambar'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);
            $line   = $request->line_id ? Line::find($request->line_id) : null;

            $updateData = [
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'group'             => $request->group,
                'shift'             => $request->shift,
                'jumlah_stroke'     => $request->jumlah_stroke,
                'line_id'           => $line->id ?? null,
                'line_mesin'        => $line ? ($line->nama_line . ' - ' . $line->mesin) : null,
                'barang_id'         => $barang->id,
                'part_no'           => $barang->kode_barang,
                'nama'              => $barang->nama,
                'process_no'        => $request->process_no,
                'customer'          => $barang->cust,
                'jenis'             => $request->jenis,
                'kekuatan_stock_fg' => $request->kekuatan_stock_fg,
                'kategori_problem'  => $request->kategori_problem,
                'detail_proyek'     => $request->detail_proyek,
                // created_by TIDAK diubah di sini
            ];

            if ($request->hasFile('gambar')) {
                if ($requestRepair->gambar) {
                    Storage::disk('public')->delete($requestRepair->gambar);
                }
                $updateData['gambar'] = $request->file('gambar')->store('request_repairs', 'public');
            }

            $requestRepair->update($updateData);

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
        $authUser   = auth()->user();
        $userRoleId = $authUser->role_id ?? null;
        $newStatus  = $request->get('status');

        // ════════════════════════════════════════════════════
        // ON PROCESS — Open → On Process
        // ════════════════════════════════════════════════════
        if ($newStatus === RequestRepair::STATUS_ON_PROCESS) {

            if (!$requestRepair->canConfirmToProcess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak bisa diubah ke On Process dari status saat ini.',
                ], 422);
            }
            if (!in_array($userRoleId, RequestRepair::ROLES_TO_ON_PROCESS)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah status ke On Process.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'pic_user_ids'   => 'required|array|min:1',
                'pic_user_ids.*' => 'integer|exists:users,id',
            ], [
                'pic_user_ids.required' => 'Pilih minimal 1 PIC (diri sendiri atau tim).',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Pastikan hanya user dengan role yang berwenang yang bisa jadi PIC
            $validPicIds = User::whereIn('id', $request->pic_user_ids)
                ->whereIn('role_id', RequestRepair::ROLES_TO_ON_PROCESS)
                ->pluck('id')
                ->toArray();

            // Diri sendiri selalu ikut tercatat sebagai PIC, meski cuma pilih "Sendiri"
            $picIds = array_values(array_unique(array_merge([$authUser->id], $validPicIds)));

            DB::beginTransaction();
            try {
                $requestRepair->update([
                    'status'        => RequestRepair::STATUS_ON_PROCESS,
                    'on_process_at' => now(),
                ]);
                $requestRepair->pics()->sync($picIds);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Status berhasil diubah ke On Process!']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('RequestRepair On Process Error', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }

        // ════════════════════════════════════════════════════
        // ON TRIAL — hanya PIC yang tercatat, ATAU admin override (role 1)
        // ════════════════════════════════════════════════════
        } elseif ($newStatus === RequestRepair::STATUS_ON_TRIAL) {

            if (!$requestRepair->canConfirmToOnTrial()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak bisa diubah ke On Trial dari status saat ini.',
                ], 422);
            }

            $requestRepair->loadMissing('pics:id,nama');

            if (!$requestRepair->canUserConfirmToOnTrial($authUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya PIC (' . $requestRepair->picNamesString() . ') atau Admin yang bisa mengonfirmasi ke On Trial.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'analisa_penyebab'              => 'required|string|max:255',
                'tindakan_perbaikan'            => 'required|string|max:255',
                'catatan_penggantian_sparepart' => 'required|string|max:255',
                'item'            => 'nullable|string|max:255',
                'proses_grinding' => 'nullable|string|max:255',
                'shim_up'         => 'nullable|string|max:255',
                'status_burry'    => 'nullable|in:OK,NG',
                'standart_burry'  => 'nullable|in:OK,NG',
                'group_leader'    => 'nullable|string|max:255',
                'operator'        => 'nullable|string|max:255',
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
                'analisa_penyebab'              => $request->analisa_penyebab,
                'tindakan_perbaikan'            => $request->tindakan_perbaikan,
                'catatan_penggantian_sparepart' => $request->catatan_penggantian_sparepart,
                'item'                          => $request->item,
                'proses_grinding'               => $request->proses_grinding,
                'shim_up'                       => $request->shim_up,
                'status_burry'                  => $request->status_burry,
                'standart_burry'                => $request->standart_burry,
                'group_leader'                  => $request->group_leader,
                'operator'                      => $request->operator,
                'plan'                          => $request->plan,
                'actual'                        => $request->actual,
                'remark'                        => $request->remark,
                'judge'                         => $request->judge,
                'on_trial_at'                   => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Status berhasil diubah ke On Trial!']);

        // ════════════════════════════════════════════════════
        // CLOSED → tergantung Hasil Akhir (OK / NG)
        // Bebas role (1, 8, 4), TIDAK terikat PIC — ini approval/verifikasi
        //   - OK  → pindah ke history, hard delete dari request_repairs (flow lama)
        //   - NG  → snapshot ke request_repair_attempts, reset & balik ke Open (⬅️ baru)
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
                'hasil_akhir'       => 'required|in:OK,NG', // ⬅️ baru — trigger keputusan flow OK/NG
                'tanggal_cek'       => 'nullable|date',
                'lot_prod'          => 'nullable|string|max:255',
                'awal'              => 'nullable|in:OK,NG',
                'tengah'            => 'nullable|in:OK,NG',
                'akhir'             => 'nullable|in:OK,NG',
                'qty'               => 'nullable|in:OK,NG',
                'remark_monitoring' => 'nullable|string|max:255',
                'judge_monitoring'  => 'nullable|in:OK,NG',
                'plan_permanen'     => 'nullable|string|max:255',
                'actual_permanen'   => 'nullable|string|max:255',
                'rootcause'         => 'nullable|string|max:255',
                'recovery'          => 'nullable|string|max:255',
                'assy_trial_check'  => 'nullable|string|max:255',
                'judge_permanen'    => 'nullable|in:OK,NG',
            ], [
                'hasil_akhir.required' => 'Pilih Hasil Akhir (OK / NG) terlebih dahulu.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Snapshot nama PIC sebelum kemungkinan di-detach / record dihapus
            $requestRepair->loadMissing('pics:id,nama');
            $picNamesSnapshot = $requestRepair->picNamesString();
            $now = now();

            // ────────────────────────────────────────────
            // HASIL AKHIR = NG → simpan attempt log, reset, balik ke Open
            // ────────────────────────────────────────────
            if ($request->hasil_akhir === 'NG') {

                DB::beginTransaction();
                try {
                    $durasiOnProcessSeconds = null;
                    $durasiOnTrialSeconds   = null;

                    if ($requestRepair->on_process_at && $requestRepair->on_trial_at) {
                        $durasiOnProcessSeconds = (int) $requestRepair->on_process_at
                            ->diffInSeconds($requestRepair->on_trial_at);
                    }
                    if ($requestRepair->on_trial_at) {
                        $durasiOnTrialSeconds = (int) $requestRepair->on_trial_at
                            ->diffInSeconds($now);
                    }

                    RequestRepairAttempt::create([
                        'request_repair_id'             => $requestRepair->id,
                        'no'                             => $requestRepair->no,
                        'attempt_number'                 => $requestRepair->ng_attempt_count + 1,
                        'part_no'                        => $requestRepair->part_no,
                        'nama'                           => $requestRepair->nama,
                        'pic_names'                      => $picNamesSnapshot,
                        'analisa_penyebab'               => $requestRepair->analisa_penyebab,
                        'tindakan_perbaikan'             => $requestRepair->tindakan_perbaikan,
                        'catatan_penggantian_sparepart'  => $requestRepair->catatan_penggantian_sparepart,
                        'item'                           => $requestRepair->item,
                        'proses_grinding'                => $requestRepair->proses_grinding,
                        'shim_up'                        => $requestRepair->shim_up,
                        'status_burry'                   => $requestRepair->status_burry,
                        'standart_burry'                 => $requestRepair->standart_burry,
                        'group_leader'                   => $requestRepair->group_leader,
                        'operator'                       => $requestRepair->operator,
                        'plan'                           => $requestRepair->plan,
                        'actual'                         => $requestRepair->actual,
                        'remark'                         => $requestRepair->remark,
                        'judge'                          => $requestRepair->judge,
                        'tanggal_cek'                    => $request->tanggal_cek,
                        'lot_prod'                       => $request->lot_prod,
                        'awal'                           => $request->awal,
                        'tengah'                         => $request->tengah,
                        'akhir'                          => $request->akhir,
                        'qty'                            => $request->qty,
                        'remark_monitoring'              => $request->remark_monitoring,
                        'judge_monitoring'               => $request->judge_monitoring,
                        'plan_permanen'                  => $request->plan_permanen,
                        'actual_permanen'                => $request->actual_permanen,
                        'rootcause'                      => $request->rootcause,
                        'recovery'                       => $request->recovery,
                        'assy_trial_check'                => $request->assy_trial_check,
                        'judge_permanen'                 => $request->judge_permanen,
                        'on_process_at'                  => $requestRepair->on_process_at,
                        'on_trial_at'                    => $requestRepair->on_trial_at,
                        'ng_judged_at'                    => $now,
                        'durasi_on_process_seconds'      => $durasiOnProcessSeconds,
                        'durasi_on_trial_seconds'        => $durasiOnTrialSeconds,
                        'judged_by'                      => $authUser->id,
                    ]);

                    // Reset request_repair ke kondisi awal biar bisa dikerjakan ulang dari Open
                    $requestRepair->update([
                        'status'                        => RequestRepair::STATUS_OPEN,
                        'ng_attempt_count'               => $requestRepair->ng_attempt_count + 1,
                        'on_process_at'                  => null,
                        'on_trial_at'                    => null,
                        'analisa_penyebab'               => null,
                        'tindakan_perbaikan'             => null,
                        'catatan_penggantian_sparepart'  => null,
                        'item'                           => null,
                        'proses_grinding'                => null,
                        'shim_up'                        => null,
                        'status_burry'                   => null,
                        'standart_burry'                 => null,
                        'group_leader'                   => null,
                        'operator'                       => null,
                        'plan'                           => null,
                        'actual'                         => null,
                        'remark'                         => null,
                        'judge'                          => null,
                    ]);

                    // PIC lama sudah kesimpen di snapshot attempt (pic_names),
                    // di-detach biar mekanik lain bisa ambil dari awal via Select PIC modal
                    $requestRepair->pics()->detach();

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Hasil dinyatakan NG. Data dikembalikan ke status Open untuk ditindaklanjuti ulang.',
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('RequestRepair NG Error', ['error' => $e->getMessage()]);
                    return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
                }
            }

            // ────────────────────────────────────────────
            // HASIL AKHIR = OK → flow lama, pindah ke history, hard delete
            // ────────────────────────────────────────────
            DB::beginTransaction();
            try {
                $closedAt = $now;

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

                $repairCount = RequestRepairHistory::where('part_no', $requestRepair->part_no)->count() + 1;

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
                    'kekuatan_stock_fg'             => $requestRepair->kekuatan_stock_fg,
                    'kategori_problem'              => $requestRepair->kategori_problem,
                    'detail_proyek'                 => $requestRepair->detail_proyek,
                    'gambar'                        => $requestRepair->gambar,
                    'created_by'                    => $requestRepair->created_by,
                    'analisa_penyebab'              => $requestRepair->analisa_penyebab,
                    'tindakan_perbaikan'            => $requestRepair->tindakan_perbaikan,
                    'catatan_penggantian_sparepart' => $requestRepair->catatan_penggantian_sparepart,
                    'item'                          => $requestRepair->item,
                    'proses_grinding'               => $requestRepair->proses_grinding,
                    'shim_up'                       => $requestRepair->shim_up,
                    'status_burry'                  => $requestRepair->status_burry,
                    'standart_burry'                => $requestRepair->standart_burry,
                    'group_leader'                  => $requestRepair->group_leader,
                    'operator'                      => $requestRepair->operator,
                    'pic_names'                     => $picNamesSnapshot,
                    'plan'                          => $requestRepair->plan,
                    'actual'                        => $requestRepair->actual,
                    'remark'                        => $requestRepair->remark,
                    'judge'                         => $requestRepair->judge,
                    'tanggal_cek'                   => $request->tanggal_cek,
                    'lot_prod'                      => $request->lot_prod,
                    'awal'                          => $request->awal,
                    'tengah'                        => $request->tengah,
                    'akhir'                         => $request->akhir,
                    'qty'                           => $request->qty,
                    'remark_monitoring'             => $request->remark_monitoring,
                    'judge_monitoring'              => $request->judge_monitoring,
                    'plan_permanen'                 => $request->plan_permanen,
                    'actual_permanen'               => $request->actual_permanen,
                    'rootcause'                     => $request->rootcause,
                    'recovery'                      => $request->recovery,
                    'assy_trial_check'              => $request->assy_trial_check,
                    'judge_permanen'                => $request->judge_permanen,
                    'on_process_at'                 => $requestRepair->on_process_at,
                    'on_trial_at'                   => $requestRepair->on_trial_at,
                    'closed_at'                     => $closedAt,
                    'durasi_on_process_seconds'     => $durasiOnProcessSeconds,
                    'durasi_on_trial_seconds'       => $durasiOnTrialSeconds,
                    'durasi_total_seconds'          => $durasiTotalSeconds,
                    'repair_count'                  => $repairCount,
                    'ng_attempt_count'               => $requestRepair->ng_attempt_count, // ⬅️ baru — bawa histori jumlah NG sebelum akhirnya OK
                ]);

                $requestRepair->delete(); // pivot pics ikut kehapus otomatis lewat cascadeOnDelete di migration

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
            if ($requestRepair->gambar) {
                Storage::disk('public')->delete($requestRepair->gambar);
            }
            $requestRepair->delete();
            return response()->json(['success' => true, 'message' => 'Request Repair berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus!'], 500);
        }
    }
}