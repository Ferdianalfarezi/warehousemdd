<?php

namespace App\Http\Controllers;

use App\Models\InhouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PddConfirmController extends Controller
{
    /**
     * Halaman konfirmasi PDD
     */
    public function index()
    {
        $requests = InhouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy',
            'completedBy'
        ])
        ->whereIn('status', ['pending', 'on_process'])
        ->orderBy('created_at', 'desc')
        ->get();

        $stats = [
            'pending' => InhouseRequest::pending()->count(),
            'on_process' => InhouseRequest::onProcess()->count(),
        ];

        return view('pdd.confirm', compact('requests', 'stats'));
    }

    /**
     * Get detail permintaan
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
     * Approve permintaan inhouse (konfirmasi pertama)
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $inhouseRequest = InhouseRequest::findOrFail($id);

            if ($inhouseRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan sudah diproses sebelumnya!'
                ], 400);
            }

            // Update ke on_process
            $inhouseRequest->status = 'on_process';
            $inhouseRequest->confirmed_by = Auth::id();
            $inhouseRequest->confirmed_at = now();
            $inhouseRequest->save();

            // Update checkup detail
            $detail = $inhouseRequest->checkupDetail;
            $detail->ng_action_status = 'inhouse_on_process';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dikonfirmasi!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi permintaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete permintaan inhouse (konfirmasi kedua)
     */
    public function complete($id)
    {
        DB::beginTransaction();
        try {
            $inhouseRequest = InhouseRequest::findOrFail($id);

            if ($inhouseRequest->status !== 'on_process') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum dalam status On Process!'
                ], 400);
            }

            // Update ke completed
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
                'message' => 'Perbaikan berhasil diselesaikan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan perbaikan: ' . $e->getMessage()
            ], 500);
        }
    }
}