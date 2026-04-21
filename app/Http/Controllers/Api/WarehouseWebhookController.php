<?php
// app/Http/Controllers/Api/WarehouseWebhookController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartRequest;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WarehouseWebhookController extends Controller
{
    /**
     * Receive status update from warehouse system
     * POST /api/webhooks/warehouse-order-status
     */
    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'status' => 'required|string',
            'approved_by' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::warning('Warehouse webhook validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $orderId = $request->order_id;
        $warehouseStatus = $request->status;

        // Find part request by warehouse order_id
        $partRequest = PartRequest::where('order_id', $orderId)->first();

        if (!$partRequest) {
            Log::warning('Part request not found for warehouse order', [
                'order_id' => $orderId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Part request not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $partRequest->status;
            $newStatus = $this->mapWarehouseStatus($warehouseStatus);

            // Update part request status
            $partRequest->update([
                'status' => $newStatus,
            ]);

            // Update all parts in this request (active_request_id remains same, status changes)
            // Status akan otomatis update karena kita ambil dari relationship
            
            DB::commit();

            Log::info('Part request status updated from warehouse webhook', [
                'request_id' => $partRequest->id,
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'warehouse_status' => $warehouseStatus,
                'affected_parts' => $partRequest->items->pluck('part_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => [
                    'request_id' => $partRequest->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating part request status from webhook', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map warehouse status to internal status
     */
    private function mapWarehouseStatus(string $warehouseStatus): string
    {
        return match($warehouseStatus) {
            'Pending' => 'pending',
            'Approved by Kadiv' => 'approved_kadiv',
            'Approved by Kagud' => 'approved_kagud',
            'Ready' => 'ready',
            'Completed' => 'completed',
            'Rejected' => 'rejected',
            default => 'pending'
        };
    }
}