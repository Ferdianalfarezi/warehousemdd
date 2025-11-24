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
            // Check stock (untuk validasi saja, belum dikurangi)
            $part = Part::findOrFail($request->part_id);
            
            // Hitung total yang sudah dipakai (belum committed) + yang mau ditambah
            $totalUsed = CheckupPartReplacement::where('part_id', $request->part_id)
                ->where('is_committed', false)
                ->sum('quantity_used');
            
            $totalNeeded = $totalUsed + $request->quantity_used;
            
            if ($part->stock < $totalNeeded) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock tidak mencukupi! Tersedia: {$part->stock}, Dibutuhkan: {$totalNeeded}"
                ], 400);
            }

            // Create part replacement (temporary, belum committed)
            $partReplacement = CheckupPartReplacement::create([
                'general_checkup_id' => $request->general_checkup_id,
                'checkup_detail_id' => $request->checkup_detail_id,
                'part_id' => $request->part_id,
                'quantity_used' => $request->quantity_used,
                'catatan' => $request->catatan,
                'is_committed' => false, // Temporary
                'is_installed' => false, // Belum dipasang
            ]);

            // Update checkup detail status
            $detail = CheckupDetail::find($request->checkup_detail_id);
            $detail->ng_action_type = 'part';
            
            // Set status ke waiting_part_installation
            $detail->ng_action_status = 'waiting_part_installation';
            $detail->save();

            // Reload with relationships
            $partReplacement->load('part');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil ditambahkan! (Stock belum dikurangi)',
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
            
            // Check if already committed
            if ($partReplacement->is_committed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part sudah di-commit, tidak bisa dihapus!'
                ], 400);
            }
            
            $checkupDetailId = $partReplacement->checkup_detail_id;
            
            $partReplacement->delete();

            // Check jika masih ada part lain untuk detail ini
            $remainingParts = CheckupPartReplacement::where('checkup_detail_id', $checkupDetailId)->count();
            
            if ($remainingParts === 0) {
                // Jika tidak ada part lagi, reset ng_action
                $detail = CheckupDetail::find($checkupDetailId);
                if ($detail) {
                    $detail->ng_action_type = null;
                    $detail->ng_action_status = null;
                    $detail->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil dihapus! (Stock tidak terpengaruh karena belum di-commit)'
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

    /**
     * Close individual part (mark as installed)
     */
    public function close($id)
    {
        DB::beginTransaction();
        try {
            // $id adalah ID dari CheckupPartReplacement
            $partReplacement = CheckupPartReplacement::findOrFail($id);

            if ($partReplacement->is_installed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part sudah ditandai sebagai terpasang!'
                ], 400);
            }

            // Mark part as installed
            $partReplacement->is_installed = true;
            $partReplacement->save();

            // Check apakah semua part untuk detail ini sudah installed
            $detail = CheckupDetail::findOrFail($partReplacement->checkup_detail_id);
            $allPartsInstalled = $detail->partReplacements()
                ->where('is_installed', false)
                ->count() === 0;

            // Jika semua part sudah installed, update status detail ke closed
            if ($allPartsInstalled) {
                $detail->ng_action_status = 'closed';
                $detail->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil ditandai sebagai terpasang!',
                'all_installed' => $allPartsInstalled
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Part Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup part!'
            ], 500);
        }
    }

    /**
     * Close all parts for a specific checkup detail
     */
    public function closeAll($checkupDetailId)
    {
        DB::beginTransaction();
        try {
            $detail = CheckupDetail::findOrFail($checkupDetailId);

            if ($detail->ng_action_type !== 'part') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukan tindakan part replacement!'
                ], 400);
            }

            // Get all parts that are not installed yet
            $partsToClose = CheckupPartReplacement::where('checkup_detail_id', $checkupDetailId)
                ->where('is_installed', false)
                ->get();

            if ($partsToClose->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua part sudah terpasang!'
                ], 400);
            }

            // Mark all parts as installed
            foreach ($partsToClose as $part) {
                $part->is_installed = true;
                $part->save();
            }

            // Update detail status to closed
            $detail->ng_action_status = 'closed';
            $detail->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil! {$partsToClose->count()} part ditandai sebagai terpasang.",
                'closed_count' => $partsToClose->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close All Parts Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup semua part!'
            ], 500);
        }
    }
}