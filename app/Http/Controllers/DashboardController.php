<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\Barang;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_suppliers' => Supplier::count(),
            'total_parts' => Part::count(),
            'total_barangs' => Barang::count(),
            'low_stock_parts' => Part::whereColumn('stock', '<', 'min_stock')->count(),
            'active_users' => User::where('status', 'aktif')->count(),
        ];

        $lowStockParts = Part::with('supplier')
            ->whereColumn('stock', '<', 'min_stock')
            ->limit(5)
            ->get();

        $recentBarangs = Barang::with(['supplier', 'parts'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'lowStockParts', 'recentBarangs'));
    }
}