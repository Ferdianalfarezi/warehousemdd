<?php

namespace App\Http\Controllers;

use App\Models\InhouseRequest;
use Illuminate\Http\Request;

class AndonInhouseController extends Controller
{
    /**
     * Halaman andon inhouse (display only - on_process only)
     */
    public function index()
    {
        $requests = InhouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy'
        ])
        ->where('status', 'on_process') // 🔥 HANYA ON_PROCESS
        ->orderBy('confirmed_at', 'desc')
        ->get();

        $stats = [
            'on_process' => InhouseRequest::where('status', 'on_process')->count(),
        ];

        return view('andon.inhouse', compact('requests', 'stats'));
    }

    /**
     * Get detail (untuk modal)
     */
    public function show($id)
    {
        $request = InhouseRequest::with([
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