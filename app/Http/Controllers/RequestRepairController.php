<?php

namespace App\Http\Controllers;

use App\Models\RequestRepair;
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

    // ── AJAX: get durasi realtime (untuk timer di modal) ────
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
                'on_process_at'     => now(), // ← catat waktu mulai
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

        $data           = $requestRepair->toArray();
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

        if ($newStatus === RequestRepair::STATUS_ON_TRIAL) {
            // Validasi transisi & role
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

            // Validasi additional info (wajib untuk on_trial)
            $validator = Validator::make($request->all(), [
                'penyebab_vc'     => 'required|string|max:500',
                'tindakan_repair' => 'required|in:Pertama,Berulang',
            ], [
                'penyebab_vc.required'     => 'Penyebab (VC) wajib diisi.',
                'tindakan_repair.required' => 'Tindakan wajib dipilih.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $requestRepair->update([
                'status'          => RequestRepair::STATUS_ON_TRIAL,
                'penyebab_vc'     => $request->penyebab_vc,
                'tindakan_repair' => $request->tindakan_repair,
                'on_trial_at'     => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Status berhasil diubah ke On Trial!']);

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

            // Validasi closed info (wajib untuk closed)
            $validator = Validator::make($request->all(), [
                'status_after_trial'      => 'required|in:OK,NG',
                'point_verifikasi'        => 'required|string|max:1000',
                'approval_section_chief'  => 'required|string|max:255',
            ], [
                'status_after_trial.required'     => 'Status after trial wajib dipilih.',
                'point_verifikasi.required'       => 'Point verifikasi wajib diisi.',
                'approval_section_chief.required' => 'Approval section chief wajib diisi.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $requestRepair->update([
                'status'                 => RequestRepair::STATUS_CLOSED,
                'status_after_trial'     => $request->status_after_trial,
                'point_verifikasi'       => $request->point_verifikasi,
                'approval_section_chief' => $request->approval_section_chief,
                'closed_at'              => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Status berhasil diubah ke Closed!']);

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