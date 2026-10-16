<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Barang;
use App\Services\SchedulePreventiveParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScheduleController extends Controller
{
    /**
     * Batas hari untuk status "segera".
     * SAMAKAN dengan logika accessor status di model Schedule.
     */
    private const SEGERA_HARI = 7;

    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request)
    {
        $query = Schedule::query();
        $this->applyFilters($query, $request);

        $perPageInput = $request->input('per_page', 20);
        if ($perPageInput === 'all') {
            $perPage = max((clone $query)->count(), 1);
        } else {
            $perPage = in_array((int) $perPageInput, self::PER_PAGE_OPTIONS) ? (int) $perPageInput : 20;
        }

        $schedules = $query->latest()->paginate($perPage)->withQueryString();

        // Kalau halaman kosong (misal habis delete data terakhir di halaman itu), lempar ke halaman terakhir
        if ($schedules->isEmpty() && $schedules->currentPage() > 1) {
            return redirect($schedules->url($schedules->lastPage()));
        }

        $stats = [
            'total'     => Schedule::count(),
            'segera'    => $this->applyStatus(Schedule::query(), 'segera')->count(),
            'hari_ini'  => $this->applyStatus(Schedule::query(), 'hari_ini')->count(),
            'terlambat' => $this->applyStatus(Schedule::query(), 'terlambat')->count(),
        ];

        $filters = [
            'search'   => $request->input('search', ''),
            'status'   => $request->input('status', 'all'),
            'periode'  => $request->input('periode', 'all'),
            'per_page' => $perPageInput === 'all' ? 'all' : (string) $perPage,
        ];

        return view('schedules.index', compact('schedules', 'stats', 'filters'));
    }

    private function applyFilters($query, Request $request)
    {
        // Cari per kata: tiap kata harus ada di kode atau nama.
        // Banyak nama barang mengandung spasi ganda (tampil sebagai 1 spasi di browser),
        // jadi pencarian frasa utuh "GUIDE RADIATOR" tidak akan ketemu "GUIDE  RADIATOR".
        $words = preg_split('/\s+/', trim($request->input('search', '')), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $word) {
            $query->where(function ($q) use ($word) {
                $q->where('kode_barang', 'like', "%{$word}%")
                  ->orWhere('nama', 'like', "%{$word}%");
            });
        }

        $periode = $request->input('periode');
        if ($periode && $periode !== 'all') {
            $query->where('periode', $periode);
        }

        $status = $request->input('status');
        if ($status && $status !== 'all') {
            $this->applyStatus($query, $status);
        }

        return $query;
    }

    private function applyStatus($query, $status)
    {
        $today = now()->toDateString();
        $batasSegera = now()->addDays(self::SEGERA_HARI)->toDateString();

        switch ($status) {
            case 'terlambat':
                $query->whereDate('service_berikutnya', '<', $today);
                break;
            case 'hari_ini':
                $query->whereDate('service_berikutnya', '=', $today);
                break;
            case 'segera':
                $query->whereDate('service_berikutnya', '>', $today)
                      ->whereDate('service_berikutnya', '<=', $batasSegera);
                break;
            case 'terjadwal':
                $query->whereDate('service_berikutnya', '>', $batasSegera);
                break;
        }

        return $query;
    }

    public function create()
    {
        return redirect()->route('schedules.index');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id|unique:schedules,barang_id',
            'mulai_service' => 'required|date',
            'periode' => 'required|in:harian,mingguan,bulanan,tahunan,custom',
            'interval_value' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);

            $schedule = new Schedule();
            $schedule->barang_id = $request->barang_id;
            $schedule->gambar = $barang->gambar;
            $schedule->kode_barang = $barang->kode_barang;
            $schedule->nama = $barang->nama;
            $schedule->mulai_service = $request->mulai_service;
            $schedule->periode = $request->periode;
            $schedule->interval_value = $request->interval_value;

            $schedule->calculateNextService();

            $schedule->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule berhasil dibuat!',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule Store Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $schedule = Schedule::with('barang')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $schedule->id,
                    'barang_id' => $schedule->barang_id,
                    'kode_barang' => $schedule->barang->kode_barang,
                    'nama' => $schedule->barang->nama,
                    'mulai_service' => \Carbon\Carbon::parse($schedule->mulai_service)->format('Y-m-d'),
                    'periode' => $schedule->periode,
                    'interval_value' => $schedule->interval_value,
                    'service_berikutnya' => $schedule->service_berikutnya,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule tidak ditemukan'
            ], 404);
        }
    }

    public function edit(Schedule $schedule)
    {
        return redirect()->route('schedules.index');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validator = \Validator::make($request->all(), [
            'mulai_service' => 'required|date',
            'periode' => 'required|in:harian,mingguan,bulanan,tahunan,custom',
            'interval_value' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $schedule->mulai_service = $request->mulai_service;
            $schedule->periode = $request->periode;
            $schedule->interval_value = $request->interval_value;

            $schedule->calculateNextService();
            $schedule->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule berhasil diupdate!',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Schedule $schedule)
    {
        DB::beginTransaction();
        try {
            $schedule->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus schedule!'
            ], 500);
        }
    }

    /**
     * Hanya kirim barang yang BELUM punya schedule
     * (dulu disaring di JS pakai $schedules, sekarang tidak bisa karena sudah dipaginate)
     */
    public function getBarangsForSchedule()
    {
        try {
            $barangs = Barang::select('id', 'kode_barang', 'nama', 'gambar')
                ->whereNotIn('id', Schedule::select('barang_id'))
                ->orderBy('kode_barang')
                ->get();

            return response()->json($barangs);
        } catch (\Exception $e) {
            Log::error('Error getting barangs: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        $path = $request->file('excel_file')->getRealPath();

        try {
            $parsedRows = (new SchedulePreventiveParser())->parse($path);
        } catch (Throwable $e) {
            Log::error('Schedule Import Parse Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
            ], 422);
        }

        $imported = [];
        $skippedNoBarang = [];
        $skippedNoMarks = [];

        DB::beginTransaction();
        try {
            foreach ($parsedRows as $row) {
                if ($row['status'] === 'no_marks') {
                    $skippedNoMarks[] = $row['part_no'];
                    continue;
                }

                $barang = Barang::where('kode_barang', $row['part_no'])->first();

                if (!$barang) {
                    $skippedNoBarang[] = $row['part_no'];
                    continue;
                }

                $schedule = Schedule::firstOrNew(['barang_id' => $barang->id]);
                $schedule->barang_id = $barang->id;
                $schedule->gambar = $barang->gambar;
                $schedule->kode_barang = $barang->kode_barang;
                $schedule->nama = $barang->nama;
                $schedule->mulai_service = $row['mulai_service'];
                $schedule->periode = $row['periode'];
                $schedule->interval_value = $row['interval_value'];
                $schedule->terakhir_service = null;
                $schedule->save();

                $imported[] = $row['part_no'];
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Schedule Import Save Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Import selesai.',
            'summary' => [
                'imported_count' => count($imported),
                'skipped_no_barang_count' => count($skippedNoBarang),
                'skipped_no_marks_count' => count($skippedNoMarks),
                'skipped_no_barang_list' => $skippedNoBarang,
                'skipped_no_marks_list' => $skippedNoMarks,
            ],
        ]);
    }
}