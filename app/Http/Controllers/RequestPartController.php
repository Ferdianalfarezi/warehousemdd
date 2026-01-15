<?php
// app/Http/Controllers/RequestPartController.php

namespace App\Http\Controllers;

use App\Models\RequestPart;
use App\Models\RequestPartItem;
use App\Models\HistoryRequestPart;
use App\Models\HistoryRequestPartItem;
use App\Services\WarehouseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestPartController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseOrderService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display listing of active request parts
     * Route: GET /request-parts
     * View: request-parts.index
     */
    public function index()
    {
        $requests = RequestPart::with(['items.part', 'user'])
            ->whereIn('status', ['pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed'])
            ->latest('tanggal_request')
            ->get();
        
        return view('request-parts.index', compact('requests'));
    }

    /**
     * Show single request part detail (AJAX)
     * Route: GET /request-parts/{requestPart}
     * Returns: JSON
     */
    public function show(RequestPart $requestPart)
    {
        $requestPart->load([
            'items.part', 
            'user', 
            'approvedByKadiv', 
            'approvedByKagud'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $requestPart
        ]);
    }

    /**
     * Verify completed request and add stock to parts
     * Route: POST /request-parts/{requestPart}/verify
     * Requirements: Status must be 'completed'
     */
    public function verify(Request $request, RequestPart $requestPart)
    {
        // Validasi status
        if ($requestPart->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya request dengan status "Completed" yang bisa diverifikasi'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update status request
            $requestPart->update([
                'status' => 'verified',
                'tanggal_verified' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Update item status dan tambahkan stock ke part
            foreach ($requestPart->items as $item) {
                // Update item status
                $item->update(['item_status' => 'verified']);
                
                // Tambahkan stock ke part
                $part = $item->part;
                $quantityToAdd = $item->quantity_approved ?? $item->quantity;
                $part->increment('stock', $quantityToAdd);

                Log::info('Stock added to part', [
                    'part_id' => $part->id,
                    'part_code' => $part->kode_part,
                    'quantity_added' => $quantityToAdd,
                    'new_stock' => $part->fresh()->stock
                ]);
            }

            // Pindahkan ke history
            $this->moveToHistory($requestPart);

            DB::commit();

            Log::info('Request part verified successfully', [
                'request_id' => $requestPart->id,
                'request_number' => $requestPart->request_number,
                'verified_by' => auth()->id(),
                'total_items' => $requestPart->items->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil diverifikasi dan stock telah ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error verifying request part', [
                'request_id' => $requestPart->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit request to warehouse system
     * Route: POST /request-parts/{requestPart}/submit-to-warehouse
     * Action: Send request to warehouse and save warehouse_order_id
     */
    public function submitToWarehouse(RequestPart $requestPart)
    {
        try {
            // Cek apakah sudah punya warehouse_order_id
            if ($requestPart->warehouse_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request ini sudah dikirim ke warehouse sebelumnya'
                ], 400);
            }

            // Prepare items untuk dikirim
            $items = $requestPart->items->map(function ($item) {
                return [
                    'barang_id' => $item->part_id, // Map part_id ke barang_id
                    'quantity' => $item->quantity,
                    'keterangan' => $item->keterangan ?? 'Request from MDD'
                ];
            })->toArray();

            // Submit ke warehouse
            $result = $this->warehouseService->submitOrder(
                items: $items,
                requesterName: $requestPart->requester_name,
                departmentId: $requestPart->department_id,
                catatan: $requestPart->catatan
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim ke warehouse: ' . ($result['data']['message'] ?? 'Unknown error')
                ], 500);
            }

            // Update warehouse_order_id
            $warehouseOrderId = $result['data']['data']['order_id'] ?? null;
            
            if ($warehouseOrderId) {
                $requestPart->update([
                    'warehouse_order_id' => $warehouseOrderId
                ]);
            }

            Log::info('Request submitted to warehouse', [
                'request_id' => $requestPart->id,
                'warehouse_order_id' => $warehouseOrderId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil dikirim ke warehouse',
                'data' => [
                    'warehouse_order_id' => $warehouseOrderId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting to warehouse', [
                'request_id' => $requestPart->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync status from warehouse system
     * Route: POST /request-parts/{requestPart}/sync-status
     * Action: Fetch latest status from warehouse and update local status
     */
    public function syncWarehouseStatus(RequestPart $requestPart)
{
    try {
        // Cek apakah request ini punya warehouse_order_id
        if (!$requestPart->warehouse_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Request ini belum dikirim ke warehouse system',
                'action' => 'submit', // Hint untuk frontend
                'hint' => 'Klik tombol "Kirim ke Warehouse" terlebih dahulu'
            ], 400);
        }

        Log::info('Starting warehouse status sync', [
            'request_id' => $requestPart->id,
            'request_number' => $requestPart->request_number,
            'warehouse_order_id' => $requestPart->warehouse_order_id,
            'current_status' => $requestPart->status
        ]);

        // 🔥 FIX: Panggil method yang benar untuk RequestPart
        $result = $this->warehouseService->syncRequestPartStatus($requestPart);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal sync status dari warehouse'
            ], 500);
        }

        // Refresh model untuk get updated data
        $requestPart->refresh();

        Log::info('Warehouse status synced successfully', [
            'request_id' => $requestPart->id,
            'old_status' => $result['old_status'],
            'new_status' => $result['new_status'],
            'warehouse_status' => $result['warehouse_status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil disinkronkan dari warehouse',
            'data' => [
                'old_status' => $result['old_status'],
                'new_status' => $result['new_status'],
                'warehouse_status' => $result['warehouse_status'],
                'status_changed' => $result['old_status'] !== $result['new_status']
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error syncing warehouse status', [
            'request_id' => $requestPart->id,
            'warehouse_order_id' => $requestPart->warehouse_order_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat sync status: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Move verified request to history
     * Private helper method
     */
    private function moveToHistory(RequestPart $requestPart)
    {
        try {
            // Create history header
            $history = HistoryRequestPart::create([
                'request_part_id' => $requestPart->id,
                'request_number' => $requestPart->request_number,
                'user_id' => $requestPart->user_id,
                'requester_name' => $requestPart->requester_name,
                'department_id' => $requestPart->department_id,
                'status' => $requestPart->status,
                'warehouse_order_id' => $requestPart->warehouse_order_id,
                'catatan' => $requestPart->catatan,
                'tanggal_request' => $requestPart->tanggal_request,
                'tanggal_completed' => $requestPart->updated_at,
                'tanggal_verified' => $requestPart->tanggal_verified,
                'verified_by' => $requestPart->verified_by,
                'approved_by_kadiv' => $requestPart->approved_by_kadiv,
                'approved_by_kagud' => $requestPart->approved_by_kagud,
                'tanggal_approved_kadiv' => $requestPart->tanggal_approved_kadiv,
                'tanggal_approved_kagud' => $requestPart->tanggal_approved_kagud,
            ]);

            // Create history items
            foreach ($requestPart->items as $item) {
                HistoryRequestPartItem::create([
                    'history_request_part_id' => $history->id,
                    'part_id' => $item->part_id,
                    'part_code' => $item->part->kode_part,
                    'part_name' => $item->part->nama,
                    'quantity' => $item->quantity,
                    'quantity_approved' => $item->quantity_approved,
                    'keterangan' => $item->keterangan,
                    'item_status' => $item->item_status,
                ]);
            }

            // Soft delete original request (optional)
            // $requestPart->delete();

            Log::info('Request moved to history', [
                'request_id' => $requestPart->id,
                'history_id' => $history->id,
                'items_count' => $requestPart->items->count()
            ]);

            return $history;

        } catch (\Exception $e) {
            Log::error('Error moving request to history', [
                'request_id' => $requestPart->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
 * Check for updates (Real-time polling endpoint)
 * Auto sync status dari warehouse untuk active requests
 * 
 * Route: GET /request-parts/check-updates
 */
public function checkUpdates(Request $request)
{
    $since = $request->get('since');
    
    try {
        $sinceTime = $since ? \Carbon\Carbon::parse($since) : now()->subMinutes(5);
    } catch (\Exception $e) {
        $sinceTime = now()->subMinutes(5);
    }
    
    $changedRequests = collect();
    $syncErrors = 0;
    
    // Get active requests yang perlu di-sync (punya warehouse_order_id & belum completed/verified)
    $activeRequests = RequestPart::whereNotNull('warehouse_order_id')
        ->whereIn('status', ['pending', 'approved_kadiv', 'approved_kagud', 'ready'])
        ->orderBy('updated_at', 'asc')
        ->limit(10)
        ->get();
    
    // Auto sync each request dari warehouse
    foreach ($activeRequests as $requestPart) {
        try {
            $oldStatus = $requestPart->status;
            
            // Sync dari warehouse
            $result = $this->warehouseService->syncRequestPartStatus($requestPart);
            
            // Kalau berhasil dan status berubah
            if ($result['success'] && $result['old_status'] !== $result['new_status']) {
                $requestPart->refresh();
                $changedRequests->push($requestPart);
            }
        } catch (\Exception $e) {
            $syncErrors++;
            Log::warning('Auto sync failed', [
                'request_id' => $requestPart->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // Juga cek perubahan lokal (verify, reject, dll)
    $localChanges = RequestPart::where('updated_at', '>', $sinceTime)
        ->whereIn('status', ['pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed', 'verified'])
        ->whereNotIn('id', $changedRequests->pluck('id')->toArray())
        ->get();
    
    $allChanges = $changedRequests->merge($localChanges);
    
    // Get stats
    $allRequests = RequestPart::whereIn('status', ['pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed', 'rejected'])
        ->get();
    
    return response()->json([
        'has_changes' => $allChanges->isNotEmpty(),
        'timestamp' => now()->toISOString(),
        'synced_count' => $activeRequests->count(),
        'sync_errors' => $syncErrors,
        'stats' => [
            'pending' => $allRequests->where('status', 'pending')->count(),
            'approved_kadiv' => $allRequests->where('status', 'approved_kadiv')->count(),
            'approved_kagud' => $allRequests->where('status', 'approved_kagud')->count(),
            'completed' => $allRequests->whereIn('status', ['ready', 'completed'])->count(),
            'rejected' => $allRequests->where('status', 'rejected')->count(),
        ],
        'changed_requests' => $allChanges->map(fn($r) => [
            'id' => $r->id,
            'request_number' => $r->request_number,
            'status' => $r->status,
            'keterangan' => $r->keterangan,
            'updated_at' => $r->updated_at->toISOString(),
        ])
    ]);
}
}