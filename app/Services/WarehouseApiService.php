<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WarehouseApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.warehouse.base_url');
        $this->timeout = config('services.warehouse.timeout', 30);
    }

    /**
     * Get all barang dengan UOM box
     */
    public function getBarangBoxOnly($perPage = null)
    {
        try {
            $url = $this->baseUrl . '/api/barang/box-only';
            
            $params = [];
            if ($perPage) {
                $params['per_page'] = $perPage;
            }

            // LANGSUNG REQUEST tanpa cache
            $response = Http::timeout($this->timeout)->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch barang data: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('WarehouseApiService Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal mengambil data barang dari warehouse',
                'data' => []
            ];
        }
    }

    /**
     * Search barang dengan UOM box
     */
    public function searchBarang($query = '')
    {
        try {
            $url = $this->baseUrl . '/api/barang/search';
            
            $response = Http::timeout($this->timeout)
                ->get($url, ['q' => $query]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to search barang: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('WarehouseApiService Search Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal mencari data barang',
                'data' => []
            ];
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        $keys = [
            'warehouse_barang_box_all',
            'warehouse_barang_box_10',
            'warehouse_barang_box_25',
            'warehouse_barang_box_50',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}