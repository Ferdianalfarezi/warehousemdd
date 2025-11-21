<?php
// app/Http/Controllers/AndonInhouseController.php

namespace App\Http\Controllers;

use App\Models\InhouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AndonInhouseController extends Controller
{
    /**
     * Halaman andon inhouse
     */
    public function index()
    {
        $requests = InhouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy',
            'completedBy'
        ])
        ->whereIn('status', ['on_process', 'completed'])
        ->orderBy('confirmed_at', 'desc')
        ->get();

        $stats = [
            'on_process' => InhouseRequest::where('status', 'on_process')->count(),
            'completed' => InhouseRequest::where('status', 'completed')->count(),
        ];

        return view('andon.inhouse', compact('requests', 'stats'));
    }

    /**
     * Get detail
     */
    public function show($id)
    {
        $request = InhouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy',
            'completedBy'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    /**
     * Tandai selesai
     */
    public function complete($id)
    {
        // HAPUS PENGECEKAN INI:
        // if (!in_array(Auth::user()->role, ['pdd', 'superadmin'])) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized'
        //     ], 403);
        // }

        DB::beginTransaction();
        try {
            $inhouseRequest = InhouseRequest::findOrFail($id);

            if ($inhouseRequest->status !== 'on_process') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum dalam status On Proses!'
                ], 400);
            }

            // Update status
            $inhouseRequest->status = 'completed';
            $inhouseRequest->completed_by = Auth::id();
            $inhouseRequest->completed_at = now();
            $inhouseRequest->save();

            // Update checkup detail
            $detail = $inhouseRequest->checkupDetail;
            $detail->ng_action_status = 'inhouse_completed';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil diselesaikan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan pekerjaan: ' . $e->getMessage()
            ], 500);
        }
    }
}