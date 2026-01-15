<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\Barang;
use App\Models\RequestPart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Single query untuk semua stats dasar
        $stats = [
            'total_users' => User::count(),
            'total_suppliers' => Supplier::count(),
            'total_parts' => Part::count(),
            'total_barangs' => Barang::count(),
            'low_stock_parts' => Part::where('stock', '>', 0)
                                    ->whereColumn('stock', '<=', 'min_stock')
                                    ->count(),
            'active_users' => User::where('status', 'aktif')->count(),
        ];

        // Low stock parts dengan limit
        $lowStockParts = Part::with('supplier:id,nama')
            ->whereColumn('stock', '<', 'min_stock')
            ->limit(5)
            ->get();

        // Part condition (default all time)
        $partCondition = $this->getPartCondition();

        // Restock Status
        $restockStatus = $this->getRestockStatus();

        // Top 20 most requested parts (default all time)
        $topRequestedParts = $this->getTopRequestedParts();

        // Generate month options for filter (last 12 months)
        $monthOptions = $this->getMonthOptions();

        return view('dashboard', compact('stats', 'lowStockParts', 'partCondition', 'restockStatus', 'topRequestedParts', 'monthOptions'));
    }

    /**
     * Get month options for filter (last 12 months + all time)
     */
    private function getMonthOptions()
    {
        $months = [];
        $now = Carbon::now();
        
        for ($i = 0; $i < 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $months[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y'),
            ];
        }
        
        return $months;
    }

    /**
     * API endpoint untuk filter part condition by month
     */
    public function getPartConditionByMonth(Request $request)
    {
        $month = $request->get('month'); // format: Y-m or 'all'
        $partCondition = $this->getPartCondition($month);
        
        return response()->json($partCondition);
    }

    /**
     * API endpoint untuk filter top requested parts by month
     */
    public function getTopRequestedPartsByMonth(Request $request)
    {
        $month = $request->get('month'); // format: Y-m or 'all'
        $topRequestedParts = $this->getTopRequestedParts($month);
        
        return response()->json([
            'data' => $topRequestedParts,
            'count' => $topRequestedParts->count()
        ]);
    }

    /**
     * Get part condition stats dengan single query
     * @param string|null $month format Y-m or 'all'
     */
    private function getPartCondition($month = null)
    {
        // Single query untuk hitung semua kondisi stock (ini selalu current state)
        $stats = Part::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock > 0 AND stock <= min_stock THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN stock > min_stock THEN 1 ELSE 0 END) as in_stock
        ")->first();

        // Query untuk on_request dengan filter bulan
        $onRequestQuery = DB::table('request_part_items')
            ->join('request_parts', 'request_part_items.request_part_id', '=', 'request_parts.id')
            ->whereIn('request_part_items.item_status', ['pending', 'on_request']);

        // Filter by month jika bukan 'all'
        if ($month && $month !== 'all') {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $onRequestQuery->whereBetween('request_parts.tanggal_request', [$startDate, $endDate]);
        }

        $onRequestCount = $onRequestQuery->distinct('request_part_items.part_id')->count('request_part_items.part_id');

        return [
            'total' => $stats->total ?? 0,
            'in_stock' => $stats->in_stock ?? 0,
            'low_stock' => $stats->low_stock ?? 0,
            'on_request' => $onRequestCount,
            'out_of_stock' => $stats->out_of_stock ?? 0,
        ];
    }

    /**
     * Get restock status dengan single query
     */
    private function getRestockStatus()
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);
        $fourWeeksAgo = $now->copy()->subDays(28);

        // Single query dengan CASE WHEN untuk kategorisasi
        $stats = RequestPart::whereIn('status', [
                'pending', 
                'approved_kadiv', 
                'approved_kagud', 
                'ready', 
                'completed'
            ])
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN tanggal_request >= ? THEN 1 ELSE 0 END) as one_week,
                SUM(CASE WHEN tanggal_request >= ? AND tanggal_request < ? THEN 1 ELSE 0 END) as two_weeks,
                SUM(CASE WHEN tanggal_request >= ? AND tanggal_request < ? THEN 1 ELSE 0 END) as four_weeks,
                SUM(CASE WHEN tanggal_request < ? THEN 1 ELSE 0 END) as over_four_weeks
            ", [
                $oneWeekAgo,      // one_week: >= 7 days ago
                $twoWeeksAgo,     // two_weeks: >= 14 days ago
                $oneWeekAgo,      // two_weeks: < 7 days ago
                $fourWeeksAgo,    // four_weeks: >= 28 days ago
                $twoWeeksAgo,     // four_weeks: < 14 days ago
                $fourWeeksAgo,    // over_four_weeks: < 28 days ago
            ])
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'one_week' => $stats->one_week ?? 0,
            'two_weeks' => $stats->two_weeks ?? 0,
            'four_weeks' => $stats->four_weeks ?? 0,
            'over_four_weeks' => $stats->over_four_weeks ?? 0,
        ];
    }

    /**
     * Get top 20 most requested parts
     * @param string|null $month format Y-m or 'all'
     */
    private function getTopRequestedParts($month = null)
    {
        $query = \App\Models\HistoryRequestPartItem::select(
                'part_id',
                'part_code',
                'part_name',
                DB::raw('SUM(quantity) as total_requested'),
                DB::raw('COUNT(*) as request_count')
            )
            ->whereNotNull('part_id');

        // Filter by month jika bukan 'all'
        if ($month && $month !== 'all') {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->groupBy('part_id', 'part_code', 'part_name')
            ->orderByDesc('total_requested')
            ->limit(20)
            ->get();
    }
}