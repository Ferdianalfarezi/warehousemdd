<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportItem;
use App\Models\User;
use App\Services\DailyReportSync;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DailyReportController extends Controller
{
    /** Role yang aktivitasnya direkam. */
    public const ROLE_MEMBER = 7;

    /** Role yang boleh lihat daily report semua orang. */
    public const ROLES_VIEW_ALL = [1, 2];

    protected DailyReportSync $sync;

    public function __construct(DailyReportSync $sync)
    {
        $this->sync = $sync;
    }

    // ── Index ───────────────────────────────────────────────
    public function index()
    {
        $authUser = auth()->user();
        $isAdmin  = in_array($authUser->role_id, self::ROLES_VIEW_ALL);

        $members = $isAdmin
            ? User::where('role_id', self::ROLE_MEMBER)->orderBy('nama')->get(['id', 'nama', 'nik'])
            : collect();

        return view('daily_reports.index', [
            'members'        => $members,
            'isAdmin'        => $isAdmin,
            'kategoriManual' => DailyReportItem::KATEGORI_MANUAL,
            'kategoriLabels' => DailyReportItem::KATEGORI_LABELS,
        ]);
    }

    // ── AJAX: data 1 hari ───────────────────────────────────
    public function getData(Request $request)
    {
        $authUser = auth()->user();

        $tanggal = $request->get('tanggal')
            ? Carbon::parse($request->get('tanggal'))->startOfDay()
            : DailyReportSync::tanggalKerja(now());

        $targetUser = $this->resolveTargetUser($request, $authUser);
        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses ke daily report user tersebut.',
            ], 403);
        }

        try {
            $report = $this->sync->sync($targetUser, $tanggal);
        } catch (\Exception $e) {
            Log::error('DailyReport Sync Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->presentReport($report, $targetUser),
        ]);
    }

    // ── AJAX: tambah baris manual ───────────────────────────
    public function storeItem(Request $request)
    {
        $authUser = auth()->user();

        $validator = Validator::make($request->all(), [
            'tanggal'    => 'required|date',
            'user_id'    => 'nullable|integer|exists:users,id',
            'mulai'      => 'required|date_format:H:i',
            'selesai'    => 'required|date_format:H:i',
            'kategori'   => 'required|in:' . implode(',', DailyReportItem::KATEGORI_MANUAL),
            'keterangan' => 'nullable|string|max:500',
        ], [
            'mulai.required'    => 'Jam mulai wajib diisi.',
            'selesai.required'  => 'Jam selesai wajib diisi.',
            'kategori.required' => 'Pilih kategori terlebih dahulu.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $targetUser = $this->resolveTargetUser($request, $authUser);
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'Tidak punya akses.'], 403);
        }

        $tanggal = Carbon::parse($request->tanggal)->startOfDay();
        [$mulaiAt, $selesaiAt] = $this->buildRange($tanggal, $request->mulai, $request->selesai);

        if ($selesaiAt->lte($mulaiAt)) {
            return response()->json([
                'success' => false,
                'errors'  => ['selesai' => ['Jam selesai harus setelah jam mulai.']],
            ], 422);
        }

        $report = DailyReport::firstOrCreate(
            ['user_id' => $targetUser->id, 'tanggal_kerja' => $tanggal->toDateString()],
            ['shift' => 1]
        );

        DailyReportItem::create([
            'daily_report_id' => $report->id,
            'user_id'         => $targetUser->id,
            'mulai_at'        => $mulaiAt,
            'selesai_at'      => $selesaiAt,
            'durasi_seconds'  => (int) $mulaiAt->diffInSeconds($selesaiAt),
            'kategori'        => $request->kategori,
            'source_type'     => DailyReportItem::SRC_MANUAL,
            'source_id'       => null,
            'cycle_number'    => 0,
            'segment_index'   => 0,
            'keterangan'      => $request->keterangan,
            'is_auto'         => false,
        ]);

        $report = $this->sync->recalculate($report->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil ditambahkan!',
            'data'    => $this->presentReport($report, $targetUser),
        ]);
    }

    // ── AJAX: update baris ──────────────────────────────────
    public function updateItem(Request $request, DailyReportItem $item)
    {
        $authUser = auth()->user();
        if (!$this->canManage($authUser, $item->user_id)) {
            return response()->json(['success' => false, 'message' => 'Tidak punya akses.'], 403);
        }

        // Baris auto: cuma keterangan yang boleh diubah
        if ($item->isLocked()) {
            $validator = Validator::make($request->all(), [
                'keterangan' => 'nullable|string|max:500',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $item->update(['keterangan' => $request->keterangan]);
            $report = $this->sync->recalculate($item->report);

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil disimpan!',
                'data'    => $this->presentReport($report, $item->report->user),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'mulai'      => 'required|date_format:H:i',
            'selesai'    => 'required|date_format:H:i',
            'kategori'   => 'required|in:' . implode(',', DailyReportItem::KATEGORI_MANUAL),
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $report  = $item->report;
        $tanggal = Carbon::parse($report->tanggal_kerja)->startOfDay();
        [$mulaiAt, $selesaiAt] = $this->buildRange($tanggal, $request->mulai, $request->selesai);

        if ($selesaiAt->lte($mulaiAt)) {
            return response()->json([
                'success' => false,
                'errors'  => ['selesai' => ['Jam selesai harus setelah jam mulai.']],
            ], 422);
        }

        $item->update([
            'mulai_at'       => $mulaiAt,
            'selesai_at'     => $selesaiAt,
            'durasi_seconds' => (int) $mulaiAt->diffInSeconds($selesaiAt),
            'kategori'       => $request->kategori,
            'keterangan'     => $request->keterangan,
        ]);

        $report = $this->sync->recalculate($report->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil diupdate!',
            'data'    => $this->presentReport($report, $report->user),
        ]);
    }

    // ── AJAX: hapus baris ───────────────────────────────────
    public function destroyItem(DailyReportItem $item)
    {
        $authUser = auth()->user();
        if (!$this->canManage($authUser, $item->user_id)) {
            return response()->json(['success' => false, 'message' => 'Tidak punya akses.'], 403);
        }

        if ($item->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Baris otomatis tidak bisa dihapus. Ubah datanya lewat menu Request Repair / General Checkup.',
            ], 422);
        }

        $report = $item->report;
        $item->delete();
        $report = $this->sync->recalculate($report->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil dihapus!',
            'data'    => $this->presentReport($report, $report->user),
        ]);
    }

    // ── AJAX: catatan harian ────────────────────────────────
    public function updateCatatan(Request $request, DailyReport $dailyReport)
    {
        $authUser = auth()->user();
        if (!$this->canManage($authUser, $dailyReport->user_id)) {
            return response()->json(['success' => false, 'message' => 'Tidak punya akses.'], 403);
        }

        $dailyReport->update(['catatan' => $request->get('catatan')]);

        return response()->json(['success' => true, 'message' => 'Catatan tersimpan!']);
    }

    // ════════════════════════════════════════════════════════
    // HELPER
    // ════════════════════════════════════════════════════════

    protected function resolveTargetUser(Request $request, User $authUser): ?User
    {
        $requestedId = $request->get('user_id');

        if (!$requestedId || (int) $requestedId === (int) $authUser->id) {
            return $authUser;
        }

        if (!in_array($authUser->role_id, self::ROLES_VIEW_ALL)) {
            return null;
        }

        return User::find($requestedId);
    }

    protected function canManage(User $authUser, int $ownerId): bool
    {
        return (int) $authUser->id === $ownerId
            || in_array($authUser->role_id, self::ROLES_VIEW_ALL);
    }

    /**
     * Bikin datetime dari tanggal kerja + jam.
     * Jam < 06:00 otomatis dianggap dini hari BESOKNYA (shift 2 nyebrang).
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function buildRange(Carbon $tanggalKerja, string $mulai, string $selesai): array
    {
        $mk = $this->applyCutoff($tanggalKerja, $mulai);
        $sk = $this->applyCutoff($tanggalKerja, $selesai);

        if ($sk->lte($mk)) $sk->addDay();

        return [$mk, $sk];
    }

    protected function applyCutoff(Carbon $tanggalKerja, string $jam): Carbon
    {
        [$h, $m] = array_map('intval', explode(':', $jam));
        $dt = $tanggalKerja->copy()->startOfDay()->setTime($h, $m);
        if ($h < DailyReportSync::CUTOFF_HOUR) $dt->addDay();
        return $dt;
    }

    protected function presentReport(DailyReport $report, User $user): array
    {
        $now   = now();
        $items = $report->items()->orderBy('mulai_at')->orderBy('id')->get();

        $shiftSeconds = $report->shiftDurationSeconds();
        $coverage     = $shiftSeconds > 0
            ? min(100, round(($report->total_bersih_seconds / $shiftSeconds) * 100))
            : 0;

        return [
            'report' => [
                'id'                   => $report->id,
                'user_id'              => $user->id,
                'user_nama'            => $user->nama,
                'tanggal_kerja'        => $report->tanggal_kerja->toDateString(),
                'tanggal_label'        => $report->tanggal_kerja->locale('id')->isoFormat('dddd, D MMMM Y'),
                'shift'                => $report->shift,
                'shift_label'          => $report->shiftLabel(),
                'shift_start_at'       => $report->shift_start_at?->toISOString(),
                'shift_end_at'         => $report->shift_end_at?->toISOString(),
                'total_durasi'         => DailyReport::formatDurasi($report->total_durasi_seconds),
                'total_bersih'         => DailyReport::formatDurasi($report->total_bersih_seconds),
                'total_bersih_seconds' => $report->total_bersih_seconds,
                'overtime'             => DailyReport::formatDurasi($report->overtime_seconds),
                'overtime_seconds'     => $report->overtime_seconds,
                'coverage'             => $coverage,
                'catatan'              => $report->catatan,
                'last_synced_at'       => $report->last_synced_at?->toISOString(),
            ],
            'items' => $items->map(function (DailyReportItem $it) use ($now, $report) {
                $end   = $it->selesai_at ?: $now;
                $isOt  = $report->shift_start_at && $report->shift_end_at
                    && ($it->mulai_at->lt($report->shift_start_at) || $end->gt($report->shift_end_at));
                $warna = DailyReportItem::KATEGORI_WARNA[$it->kategori]
                    ?? DailyReportItem::KATEGORI_WARNA['lain'];

                return [
                    'id'             => $it->id,
                    'mulai'          => $it->mulai_at->format('H:i'),
                    'selesai'        => $it->selesai_at ? $it->selesai_at->format('H:i') : null,
                    'mulai_at'       => $it->mulai_at->toISOString(),
                    'selesai_at'     => $it->selesai_at?->toISOString(),
                    'durasi'         => DailyReport::formatDurasi($it->durasi_seconds),
                    'durasi_seconds' => $it->durasi_seconds,
                    'kategori'       => $it->kategori,
                    'kategori_label' => $it->kategoriLabel(),
                    'warna'          => $warna,
                    'ref_no'         => $it->ref_no,
                    'ref_part_no'    => $it->ref_part_no,
                    'ref_nama'       => $it->ref_nama,
                    'keterangan'     => $it->keterangan,
                    'is_auto'        => (bool) $it->is_auto,
                    'is_overlap'     => (bool) $it->is_overlap,
                    'is_running'     => (bool) $it->is_running,
                    'is_overtime'    => $isOt,
                    'source_type'    => $it->source_type,
                ];
            })->values(),
            'gaps' => collect($this->sync->buildGaps($report))->map(fn ($g) => [
                'mulai'      => $g['mulai_at']->format('H:i'),
                'selesai'    => $g['selesai_at']->format('H:i'),
                'mulai_at'   => $g['mulai_at']->toISOString(),
                'selesai_at' => $g['selesai_at']->toISOString(),
                'durasi'     => DailyReport::formatDurasi(
                    (int) $g['mulai_at']->diffInSeconds($g['selesai_at'])
                ),
            ])->values(),
        ];
    }
}