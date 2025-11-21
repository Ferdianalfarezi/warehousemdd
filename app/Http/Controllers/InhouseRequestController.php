<?php
// app/Http/Controllers/InhouseRequestController.php

namespace App\Http\Controllers;

use App\Models\InhouseRequest;
use App\Models\CheckupDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InhouseRequestController extends Controller
{
    /**
     * Submit permintaan inhouse dari halaman process
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'general_checkup_id' => 'required|exists:general_checkups,id',
            'checkup_detail_id' => 'required|exists:checkup_details,id',
            'problem' => 'required|string',
            'proses_dilakukan' => 'required|string',
            'mesin' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create inhouse request
            $inhouseRequest = InhouseRequest::create([
                'general_checkup_id' => $request->general_checkup_id,
                'checkup_detail_id' => $request->checkup_detail_id,
                'problem' => $request->problem,
                'proses_dilakukan' => $request->proses_dilakukan,
                'mesin' => $request->mesin,
                'status' => 'pending',
            ]);

            // Update checkup detail
            $detail = CheckupDetail::find($request->checkup_detail_id);
            $detail->ng_action_type = 'inhouse';
            $detail->ng_action_status = 'waiting_pdd_confirm';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan inhouse berhasil diajukan!',
                'data' => $inhouseRequest
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan permintaan inhouse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close inhouse request (user yang mengajukan)
     */
        public function close($id)
    {
        DB::beginTransaction();
        try {
            // $id here is actually checkup_detail_id
            $detail = CheckupDetail::findOrFail($id);
            
            if ($detail->ng_action_type !== 'inhouse') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukan tindakan inhouse!'
                ], 400);
            }

            $inhouseRequest = $detail->inhouseRequest;
            
            if (!$inhouseRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request inhouse tidak ditemukan!'
                ], 400);
            }

            if ($inhouseRequest->status !== 'completed') {
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
                'message' => 'Permintaan inhouse berhasil ditutup!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Inhouse Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup permintaan: ' . $e->getMessage()
            ], 500);
        }
    }
}