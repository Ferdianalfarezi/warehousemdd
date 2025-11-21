<?php
// app/Http/Controllers/PddConfirmController.php

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
        // HAPUS PENGECEKAN INI:
        // if (!in_array(Auth::user()->role, ['pdd', 'superadmin'])) {
        //     abort(403, 'Unauthorized');
        // }

        $requests = InhouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard'
        ])
        ->pending()
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
            'checkupDetail.checkIndicatorStandard'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    /**
     * Approve permintaan inhouse
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

            // Update status
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
}