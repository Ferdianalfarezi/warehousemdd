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
            'checkup_detail_id' => 'nullable|exists:checkup_details,id',
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
            $part = Part::findOrFail($request->part_id);

            // Check stock availability
            if ($part->stock < $request->quantity_used) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock tidak mencukupi! Available: {$part->stock}, Needed: {$request->quantity_used}"
                ], 400);
            }

            // Create part replacement (stock will be reduced automatically in model boot)
            $partReplacement = CheckupPartReplacement::create([
                'general_checkup_id' => $request->general_checkup_id,
                'checkup_detail_id' => $request->checkup_detail_id,
                'part_id' => $request->part_id,
                'quantity_used' => $request->quantity_used,
                'catatan' => $request->catatan,
            ]);

            // Reload with part data
            $partReplacement->load('part');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil ditambahkan!',
                'data' => $partReplacement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Part Replacement Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan part: ' . $e->getMessage()
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
            
            // Stock will be restored automatically in model boot
            $partReplacement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil dihapus dan stock dikembalikan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Part Replacement Error: ' . $e->getMessage());
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
        try {
            $parts = Part::where('stock', '>', 0)
                ->select('id', 'kode_part', 'nama', 'stock', 'satuan')
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $parts
            ]);

        } catch (\Exception $e) {
            Log::error('Get Available Parts Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data parts!'
            ], 500);
        }
    }
}