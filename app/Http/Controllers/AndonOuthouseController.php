<?php

namespace App\Http\Controllers;

use App\Models\OuthouseRequest;
use Illuminate\Http\Request;

class AndonOuthouseController extends Controller
{
    /**
     * Halaman andon outhouse (display only - on_process only)
     */
    public function index()
    {
        $requests = OuthouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy'
        ])
        ->where('status', 'on_process') // 🔥 HANYA ON_PROCESS
        ->orderBy('confirmed_at', 'desc')
        ->get();

        $stats = [
            'on_process' => OuthouseRequest::where('status', 'on_process')->count(),
        ];

        return view('andon.outhouse', compact('requests', 'stats'));
    }

    /**
     * Get detail (untuk modal)
     */
    public function show($id)
    {
        $request = OuthouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }
}