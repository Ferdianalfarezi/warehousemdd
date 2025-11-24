<?php

namespace App\Http\Controllers;

use App\Models\OuthouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubcontConfirmController extends Controller
{
    /**
     * Halaman konfirmasi Subcont
     */
    public function index()
    {
        $requests = OuthouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard',
            'confirmedBy',
            'completedBy'
        ])
        ->whereIn('status', ['pending', 'on_process'])
        ->orderBy('created_at', 'desc')
        ->get();

        $stats = [
            'pending' => OuthouseRequest::pending()->count(),
            'on_process' => OuthouseRequest::onProcess()->count(),
        ];

        return view('subcont.confirm', compact('requests', 'stats'));
    }

    /**
     * Get detail permintaan
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
     * Approve permintaan outhouse (konfirmasi pertama)
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $outhouseRequest = OuthouseRequest::findOrFail($id);

            if ($outhouseRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan sudah diproses sebelumnya!'
                ], 400);
            }

            // Update ke on_process
            $outhouseRequest->status = 'on_process';
            $outhouseRequest->confirmed_by = Auth::id();
            $outhouseRequest->confirmed_at = now();
            $outhouseRequest->save();

            // Update checkup detail
            $detail = $outhouseRequest->checkupDetail;
            $detail->ng_action_status = 'outhouse_on_process';
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
     * Complete permintaan outhouse (konfirmasi kedua)
     */
    public function complete($id)
    {
        DB::beginTransaction();
        try {
            $outhouseRequest = OuthouseRequest::findOrFail($id);

            if ($outhouseRequest->status !== 'on_process') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum dalam status On Process!'
                ], 400);
            }

            // Update ke completed
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