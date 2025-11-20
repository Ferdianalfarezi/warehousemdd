<?php

namespace App\Http\Controllers;

use App\Models\HistoryCheckup;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryCheckupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HistoryCheckup::with(['barang']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_checkup', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filter by line
        if ($request->filled('line')) {
            $query->where('line', $request->line);
        }

        // Filter by barang
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        $histories = $query->orderBy('tanggal_checkup', 'desc')->get();

        // Stats
        $stats = [
            'total' => $histories->count(),
            'this_month' => HistoryCheckup::thisMonth()->count(),
            'avg_duration' => round($histories->avg('durasi_perbaikan'), 2),
            'total_parts_used' => $histories->sum('total_part_used'),
            'success_rate' => $histories->count() > 0 
                ? round(($histories->sum('total_ok') / ($histories->sum('total_ok') + $histories->sum('total_ng'))) * 100, 2)
                : 0
        ];

        // Get unique lines and barangs for filter
        $lines = Barang::distinct()->pluck('line')->filter()->sort()->values();
        $barangs = Barang::select('id', 'kode_barang', 'nama')->orderBy('nama')->get();

        return view('history-checkups.index', compact('histories', 'stats', 'lines', 'barangs'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $history = HistoryCheckup::with([
            'details',
            'partReplacements',
            'barang'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $history = HistoryCheckup::findOrFail($id);

            // Delete related data
            $history->details()->delete();
            $history->partReplacements()->delete();
            $history->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'History berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history!'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $today = HistoryCheckup::today()->count();
        $thisWeek = HistoryCheckup::thisWeek()->count();
        $thisMonth = HistoryCheckup::thisMonth()->count();

        $avgDuration = HistoryCheckup::thisMonth()->avg('durasi_perbaikan');
        $totalPartsUsed = HistoryCheckup::thisMonth()->sum('total_part_used');

        $allHistory = HistoryCheckup::thisMonth()->get();
        $totalOK = $allHistory->sum('total_ok');
        $totalNG = $allHistory->sum('total_ng');
        $successRate = ($totalOK + $totalNG) > 0 
            ? round(($totalOK / ($totalOK + $totalNG)) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
                'avg_duration' => round($avgDuration, 2),
                'total_parts_used' => $totalPartsUsed,
                'success_rate' => $successRate,
            ]
        ]);
    }
}