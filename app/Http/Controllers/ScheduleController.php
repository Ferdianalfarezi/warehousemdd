<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('barang')->latest()->get();
        
        // Count by status untuk stats cards
        $stats = [
            'total' => $schedules->count(),
            'terjadwal' => $schedules->where('status', 'terjadwal')->count(),
            'segera' => $schedules->where('status', 'segera')->count(),
            'hari_ini' => $schedules->where('status', 'hari_ini')->count(),
            'terlambat' => $schedules->where('status', 'terlambat')->count(),
        ];

        return view('schedules.index', compact('schedules', 'stats'));
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
            'periode' => 'required|in:harian,mingguan,bulanan,custom',
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
            
            // Auto calculate next service
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
            'periode' => 'required|in:harian,mingguan,bulanan,custom',
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
            
            // Recalculate next service
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

    public function getBarangsForSchedule()
    {
        try {
            $barangs = Barang::select('id', 'kode_barang', 'nama', 'gambar')->get();
            return response()->json($barangs);
        } catch (\Exception $e) {
            Log::error('Error getting barangs: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}