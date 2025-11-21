<?php
// app/Http/Controllers/OuthouseRequestController.php

namespace App\Http\Controllers;

use App\Models\OuthouseRequest;
use App\Models\CheckupDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OuthouseRequestController extends Controller
{
    /**
     * Submit permintaan outhouse dari halaman process
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'general_checkup_id' => 'required|exists:general_checkups,id',
            'checkup_detail_id' => 'required|exists:checkup_details,id',
            'problem' => 'required|string',
            'mesin' => 'required|string',
            'supplier' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create outhouse request
            $outhouseRequest = OuthouseRequest::create([
                'general_checkup_id' => $request->general_checkup_id,
                'checkup_detail_id' => $request->checkup_detail_id,
                'problem' => $request->problem,
                'mesin' => $request->mesin,
                'supplier' => $request->supplier,
                'status' => 'pending',
            ]);

            // Update checkup detail
            $detail = CheckupDetail::find($request->checkup_detail_id);
            $detail->ng_action_type = 'outhouse';
            $detail->ng_action_status = 'waiting_subcont_confirm';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan outhouse berhasil diajukan!',
                'data' => $outhouseRequest
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan permintaan outhouse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close outhouse request (user yang mengajukan)
     */
    public function close($id)
    {
        DB::beginTransaction();
        try {
            // $id here is actually checkup_detail_id
            $detail = CheckupDetail::findOrFail($id);
            
            if ($detail->ng_action_type !== 'outhouse') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukan tindakan outhouse!'
                ], 400);
            }

            $outhouseRequest = $detail->outhouseRequest;
            
            if (!$outhouseRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request outhouse tidak ditemukan!'
                ], 400);
            }

            if ($outhouseRequest->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya permintaan dengan status selesai yang bisa ditutup!'
                ], 400);
            }

            // Update checkup detail status
            $detail->ng_action_status = 'closed';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan outhouse berhasil ditutup!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Outhouse Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup permintaan: ' . $e->getMessage()
            ], 500);
        }
    }
}