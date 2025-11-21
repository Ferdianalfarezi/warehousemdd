<?php
// app/Http/Controllers/SubcontConfirmController.php

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
        // HAPUS PENGECEKAN INI:
        // if (!in_array(Auth::user()->role, ['subcont', 'superadmin'])) {
        //     abort(403, 'Unauthorized');
        // }

        $requests = OuthouseRequest::with([
            'generalCheckup.barang',
            'checkupDetail.checkIndicatorStandard'
        ])
        ->pending()
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
            'checkupDetail.checkIndicatorStandard'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    /**
     * Approve permintaan outhouse
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

            // Update status
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
}