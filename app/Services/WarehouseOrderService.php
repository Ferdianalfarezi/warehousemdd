<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CheckupSparepartRequest;

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
     * 
     * @param array $items Format: [
     *     ['barang_id' => 5, 'quantity' => 2, 'keterangan' => 'Deskripsi'],
     *     ...
     * ]
     * @param string $requesterName Nama yang request
     * @param int $departmentId ID department dari warehouse (harus sama mapping)
     * @param string $catatan Catatan general
     * 
     * @return array Response dari warehouse API
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

            $response = Http::timeout($this->timeout)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to get order status: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error getting order status from warehouse', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil status order',
                'data' => null
            ];
        }
    }
}