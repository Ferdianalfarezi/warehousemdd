<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CheckupSparepartRequest;
use App\Models\RequestPart; // 🔥 TAMBAHKAN INI

class WarehouseOrderService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.warehouse.base_url');
        $this->timeout = config('services.warehouse.timeout', 30);
    }

    /**
     * Submit permintaan sparepart ke warehouse
     */
    public function submitOrder(array $items, string $requesterName, int $departmentId, ?string $catatan = null)
    {
        try {
            $url = $this->baseUrl . '/api/orders';

            $payload = [
                'requester_name' => $requesterName,
                'department_id' => $departmentId,
                'items' => $items,
                'catatan' => $catatan,
                'external_source' => 'mddwarehouse'
            ];

            Log::info('Submitting order to warehouse', [
                'url' => $url,
                'payload' => $payload
            ]);

            $response = Http::timeout($this->timeout)
                ->post($url, $payload);

            $data = $response->json();

            Log::info('Warehouse order response', [
                'status_code' => $response->status(),
                'response' => $data
            ]);

            return [
                'success' => $response->successful() && ($data['success'] ?? false),
                'status_code' => $response->status(),
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Error submitting order to warehouse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status_code' => 0,
                'error' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get status order dari warehouse
     */
    public function getOrderStatus($warehouseOrderId)
    {
        try {
            $url = $this->baseUrl . '/api/orders/' . $warehouseOrderId;

            Log::info('Fetching order status from warehouse', [
                'url' => $url,
                'order_id' => $warehouseOrderId
            ]);

            $response = Http::timeout($this->timeout)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Warehouse order status retrieved', [
                    'order_id' => $warehouseOrderId,
                    'status' => $data['data']['status'] ?? 'unknown'
                ]);

                return $data;
            }

            throw new \Exception('Failed to get order status: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error getting order status from warehouse', [
                'order_id' => $warehouseOrderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil status order',
                'data' => null
            ];
        }
    }

    /**
     * Sync status dari warehouse untuk RequestPart
     * 🔥 FIX: Ubah type hint dari CheckupSparepartRequest ke RequestPart
     */
    public function syncRequestPartStatus(RequestPart $request)
    {
        try {
            if (!$request->warehouse_order_id) {
                return [
                    'success' => false,
                    'message' => 'Request ini belum dikirim ke warehouse'
                ];
            }

            Log::info('Fetching warehouse status', [
                'request_id' => $request->id,
                'warehouse_order_id' => $request->warehouse_order_id,
                'current_local_status' => $request->status
            ]);

            $orderStatus = $this->getOrderStatus($request->warehouse_order_id);

            if (!$orderStatus['success']) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengambil status dari warehouse'
                ];
            }

            // 🔥 DEBUG: Log full response
            Log::info('Warehouse full response', [
                'full_response' => $orderStatus
            ]);

            $warehouseStatus = $orderStatus['data']['status'] ?? null;
            
            if (!$warehouseStatus) {
                return [
                    'success' => false,
                    'message' => 'Status tidak ditemukan di response warehouse'
                ];
            }

            // 🔥 DEBUG: Log before mapping
            Log::info('Before status mapping', [
                'warehouse_status_raw' => $warehouseStatus,
                'warehouse_status_type' => gettype($warehouseStatus),
            ]);

            // Map warehouse status ke local status
            $localStatus = $this->mapWarehouseStatusToLocal($warehouseStatus);

            $oldStatus = $request->status;
            
            // 🔥 DEBUG: Log mapping result
            Log::info('Status mapping result', [
                'warehouse_status' => $warehouseStatus,
                'mapped_local_status' => $localStatus,
                'old_local_status' => $oldStatus,
                'will_change' => $oldStatus !== $localStatus
            ]);

            // 🔥 Update status dan keterangan
            $request->update([
                'status' => $localStatus,
                // Keterangan akan otomatis ter-update via model boot event
            ]);

            // Refresh to get updated data
            $request->refresh();

            Log::info('RequestPart status synced', [
                'request_id' => $request->id,
                'warehouse_order_id' => $request->warehouse_order_id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'warehouse_status' => $warehouseStatus,
                'status_changed' => $oldStatus !== $request->status
            ]);

            return [
                'success' => true,
                'message' => 'Status berhasil disinkronkan',
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'warehouse_status' => $warehouseStatus
            ];

        } catch (\Exception $e) {
            Log::error('Error syncing RequestPart status', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal sync status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync status dari warehouse untuk CheckupSparepartRequest
     * 🔥 KEEP: Method original untuk CheckupSparepartRequest
     */
    public function syncOrderStatus(CheckupSparepartRequest $request)
    {
        try {
            if (!$request->warehouse_order_id) {
                return [
                    'success' => false,
                    'message' => 'Request ini belum dikirim ke warehouse'
                ];
            }

            $orderStatus = $this->getOrderStatus($request->warehouse_order_id);

            if (!$orderStatus['success']) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengambil status dari warehouse'
                ];
            }

            $warehouseStatus = $orderStatus['data']['status'] ?? null;
            
            if (!$warehouseStatus) {
                return [
                    'success' => false,
                    'message' => 'Status tidak ditemukan di response warehouse'
                ];
            }

            // Map warehouse status ke local status
            $localStatus = $this->mapWarehouseStatusToLocal($warehouseStatus);

            $oldStatus = $request->status;
            $request->status = $localStatus;
            $request->save();

            Log::info('CheckupSparepartRequest status synced', [
                'request_id' => $request->id,
                'warehouse_order_id' => $request->warehouse_order_id,
                'old_status' => $oldStatus,
                'new_status' => $localStatus,
                'warehouse_status' => $warehouseStatus
            ]);

            return [
                'success' => true,
                'message' => 'Status berhasil disinkronkan',
                'old_status' => $oldStatus,
                'new_status' => $localStatus,
                'warehouse_status' => $warehouseStatus
            ];

        } catch (\Exception $e) {
            Log::error('Error syncing CheckupSparepartRequest status', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal sync status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Map status warehouse ke status sistem local
     * 
     * Warehouse Status:
     * - Pending (menunggu approval kadiv)
     * - Approved_Kadiv (approved kadiv, menunggu kagud)
     * - Approved_Kagud (approved kagud, sedang diproses warehouse)
     * - Ready (barang siap diambil)
     * - Completed (barang sudah diambil)
     * - Rejected (ditolak)
     */
    protected function mapWarehouseStatusToLocal($warehouseStatus)
    {
        // 🔥 FIX: Handle various status format
        // Normalize: trim, lowercase, remove spaces/underscores
        $normalized = strtolower(trim($warehouseStatus));
        $normalized = str_replace([' ', '_', '-'], '', $normalized);
        
        Log::info('Status normalization', [
            'original' => $warehouseStatus,
            'normalized' => $normalized
        ]);
        
        // Map berdasarkan normalized string
        $statusMap = [
            'pending' => 'pending',
            'approvedkadiv' => 'approved_kadiv',
            'approved_kadiv' => 'approved_kadiv',
            'approvedbykadiv' => 'approved_kadiv',  // 🔥 FIX: Tambah variant "by"
            'approvedkagud' => 'approved_kagud',
            'approved_kagud' => 'approved_kagud',
            'approvedbykagud' => 'approved_kagud',  // 🔥 FIX: Tambah variant "by"
            'ready' => 'ready',
            'siapdiambil' => 'ready',
            'completed' => 'completed',
            'selesai' => 'completed',
            'rejected' => 'rejected',
            'ditolak' => 'rejected',
            'verified' => 'verified',
            'terverifikasi' => 'verified',
        ];
        
        $mappedStatus = $statusMap[$normalized] ?? null;
        
        // Fallback: Try ucfirst version
        if (!$mappedStatus) {
            $ucfirstNormalized = strtolower($warehouseStatus);
            $ucfirstNormalized = str_replace(' ', '_', $ucfirstNormalized);
            $mappedStatus = $statusMap[$ucfirstNormalized] ?? 'pending';
        }
        
        Log::info('Status mapping complete', [
            'warehouse_status' => $warehouseStatus,
            'normalized' => $normalized,
            'mapped_to' => $mappedStatus
        ]);
        
        return $mappedStatus;
    }
}