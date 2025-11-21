<?php
// app/Http/Controllers/AndonOuthouseController.php

namespace App\Http\Controllers;

use App\Models\OuthouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AndonOuthouseController extends Controller
{
    /**
     * Halaman andon outhouse
     */
    public function index()
    {
        $requests = OuthouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy',
            'completedBy'
        ])
        ->whereIn('status', ['on_process', 'completed'])
        ->orderBy('confirmed_at', 'desc')
        ->get();

        $stats = [
            'on_process' => OuthouseRequest::where('status', 'on_process')->count(),
            'completed' => OuthouseRequest::where('status', 'completed')->count(),
        ];

        return view('andon.outhouse', compact('requests', 'stats'));
    }

    /**
     * Get detail
     */
    public function show($id)
    {
        $request = OuthouseRequest::with([
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
        // if (!in_array(Auth::user()->role, ['subcont', 'superadmin'])) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized'
        //     ], 403);
        // }

        DB::beginTransaction();
        try {
            $outhouseRequest = OuthouseRequest::findOrFail($id);

            if ($outhouseRequest->status !== 'on_process') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum dalam status On Proses!'
                ], 400);
            }

            // Update status
            $outhouseRequest->status = 'completed';
            $outhouseRequest->completed_by = Auth::id();
            $outhouseRequest->completed_at = now();
            $outhouseRequest->save();

            // Update checkup detail
            $detail = $outhouseRequest->checkupDetail;
            $detail->ng_action_status = 'outhouse_completed';
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