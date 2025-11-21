<?php

namespace App\Http\Controllers;

use App\Models\CheckupPartReplacement;
use App\Models\Part;
use App\Models\CheckupDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckupPartController extends Controller
{
    /**
     * Store a newly created part replacement
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'general_checkup_id' => 'required|exists:general_checkups,id',
            'checkup_detail_id' => 'required|exists:checkup_details,id',
            'part_id' => 'required|exists:parts,id',
            'quantity_used' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Check stock
            $part = Part::findOrFail($request->part_id);
            if ($part->stock < $request->quantity_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock tidak mencukupi!'
                ], 400);
            }

            // Create part replacement
            $partReplacement = CheckupPartReplacement::create([
                'general_checkup_id' => $request->general_checkup_id,
                'checkup_detail_id' => $request->checkup_detail_id,
                'part_id' => $request->part_id,
                'quantity_used' => $request->quantity_used,
                'catatan' => $request->catatan,
            ]);

            // Update checkup detail status
            $detail = CheckupDetail::find($request->checkup_detail_id);
            $detail->ng_action_type = 'part';
            $detail->ng_action_status = 'waiting_part_installation';
            $detail->save();

            // Reload with relationships
            $partReplacement->load('part');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil ditambahkan!',
                'data' => $partReplacement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Part Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan part!'
            ], 500);
        }
    }

    /**
     * Remove the specified part replacement
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $partReplacement = CheckupPartReplacement::findOrFail($id);
            $partReplacement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil dihapus dan stock dikembalikan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Part Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus part!'
            ], 500);
        }
    }

    /**
     * Get available parts for replacement
     */
    public function getAvailableParts()
    {
        $parts = Part::where('stock', '>', 0)
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $parts
        ]);
    }

    public function close($id)
    {
        DB::beginTransaction();
        try {
            $detail = CheckupDetail::findOrFail($id);

            if ($detail->ng_action_type !== 'part') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukan tindakan part replacement!'
                ], 400);
            }

            if ($detail->ng_action_status !== 'waiting_part_installation') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid untuk close!'
                ], 400);
            }

            // Update status to closed
            $detail->ng_action_status = 'closed';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part replacement berhasil ditutup!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Part Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup part replacement!'
            ], 500);
        }
    }
}