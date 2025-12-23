<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;          // ✅ Pastikan ada
use Illuminate\Support\Facades\Log;         // 🔥 TAMBAHKAN INI
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartsImport;
use App\Exports\PartsTemplateExport;
use App\Services\WarehouseOrderService;     // ✅ Pastikan ada

class PartController extends Controller
{
    protected $warehouseOrderService;

    public function __construct(WarehouseOrderService $warehouseOrderService)
    {
        $this->warehouseOrderService = $warehouseOrderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parts = Part::with('supplier')->latest()->get();
        $suppliers = Supplier::all();
        
        // Hitung statistik stock berdasarkan status
        $totalParts = $parts->count();
        $stockAman = $parts->where('status', 'normal')->count();
        $hampirHabis = $parts->where('status', 'low')->count();
        $habis = $parts->where('status', 'habis')->count();
        
        return view('parts.index', compact('parts', 'suppliers', 'totalParts', 'stockAman', 'hampirHabis', 'habis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_part' => 'required|string|unique:parts,kode_part|max:255',
            'nama' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'id_pud' => 'nullable|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        // Handle image upload - save directly to public/storage/parts
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Ensure directory exists
            $path = public_path('storage/parts');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Move file to public/storage/parts
            $image->move($path, $imageName);
            $data['gambar'] = $imageName;
        }

        // Status akan otomatis di-calculate di model boot()
        $part = Part::create($data);
        $part->load('supplier');

        return response()->json([
            'success' => true,
            'message' => 'Part berhasil ditambahkan!',
            'data' => $part
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $part)
    {
        $part->load('supplier');
        return response()->json([
            'success' => true,
            'data' => $part
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Part $part)
    {
        $validator = Validator::make($request->all(), [
            'kode_part' => 'required|string|max:255|unique:parts,kode_part,' . $part->id,
            'nama' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'id_pud' => 'nullable|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image from public/storage/parts
            if ($part->gambar) {
                $oldImagePath = public_path('storage/parts/' . $part->gambar);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Ensure directory exists
            $path = public_path('storage/parts');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Move file to public/storage/parts
            $image->move($path, $imageName);
            $data['gambar'] = $imageName;
        }

        // Status akan otomatis di-calculate di model boot()
        $part->update($data);
        $part->load('supplier');

        return response()->json([
            'success' => true,
            'message' => 'Part berhasil diupdate!',
            'data' => $part
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $part)
    {
        try {
            // Delete image from public/storage/parts
            if ($part->gambar) {
                $imagePath = public_path('storage/parts/' . $part->gambar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $part->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Part berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus part! Mungkin masih digunakan di data barang.'
            ], 500);
        }
    }

    public function importForm()
    {
        $suppliers = Supplier::all();
        return view('parts.import', compact('suppliers'));
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(new PartsTemplateExport(), 'template_import_parts.xlsx');
    }

    /**
     * Process import Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $import = new PartsImport();
            Excel::import($import, $request->file('file'));
            
            $results = $import->getResults();
            
            return response()->json([
                'success' => true,
                'message' => 'Import completed!',
                'data' => [
                    'total' => $results['total'],
                    'success' => $results['success'],
                    'failed' => $results['failed'],
                    'errors' => $results['errors']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // app/Http/Controllers/PartController.php

/**
 * Request part ke warehouse system
 */
public function requestToWarehouse(Request $request, Part $part)
{
    $validator = Validator::make($request->all(), [
        'quantity' => 'required|integer|min:1',
        'keterangan' => 'nullable|string|max:500',
    ], [
        'quantity.required' => 'Jumlah harus diisi',
        'quantity.integer' => 'Jumlah harus berupa angka',
        'quantity.min' => 'Jumlah minimal 1',
        'keterangan.max' => 'Keterangan maksimal 500 karakter',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Validasi id_pud (barang_id warehouse)
    if (!$part->id_pud) {
        return response()->json([
            'success' => false,
            'message' => 'Part ini belum di-mapping ke warehouse system (ID PUD kosong).'
        ], 400);
    }

    // Get user
    $user = auth()->user();
    
    // 🔥 FIX: Fallback untuk requester name
    $requesterName = $user->name ?? $user->username ?? $user->email ?? 'MDD User';
    
    // Default department ID
    $departmentId = $user->department_id ?? 10;
    
    Log::info('Warehouse request - User & Department info', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_username' => $user->username ?? null,
        'user_email' => $user->email ?? null,
        'requester_name' => $requesterName,
        'user_department_id' => $user->department_id,
        'final_department_id' => $departmentId,
        'is_default' => !$user->department_id
    ]);

    try {
        DB::beginTransaction();

        // Prepare items untuk warehouse API
        $items = [
            [
                'barang_id' => (int) $part->id_pud,
                'quantity' => (int) $request->quantity,
                'keterangan' => $request->keterangan ?? "Request part: {$part->kode_part} - {$part->nama}"
            ]
        ];

        Log::info('Submitting warehouse request', [
            'part_id' => $part->id,
            'part_kode' => $part->kode_part,
            'part_nama' => $part->nama,
            'id_pud' => $part->id_pud,
            'department_id' => $departmentId,
            'quantity' => $request->quantity,
            'requester' => $requesterName
        ]);

        // Submit ke warehouse
        $response = $this->warehouseOrderService->submitOrder(
            items: $items,
            requesterName: $requesterName, // 🔥 Gunakan requesterName yang sudah ada fallback
            departmentId: (int) $departmentId,
            catatan: "Request dari MDD Warehouse - Part: {$part->kode_part}"
        );

        Log::info('Warehouse API response', [
            'success' => $response['success'],
            'status_code' => $response['status_code'] ?? null,
            'has_data' => isset($response['data'])
        ]);

        if (!$response['success']) {
            DB::rollBack();
            
            $errorMessage = 'Gagal mengirim request ke warehouse.';
            
            if (isset($response['data']['message'])) {
                $errorMessage .= ' ' . $response['data']['message'];
            } elseif (isset($response['error'])) {
                $errorMessage .= ' Error: ' . $response['error'];
            }

            Log::warning('Warehouse request failed', [
                'part_id' => $part->id,
                'error_message' => $errorMessage,
                'full_response' => $response
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);
        }

        DB::commit();

        $warehouseOrderId = $response['data']['data']['id'] ?? null;
        
        Log::info('Warehouse request successful', [
            'part_id' => $part->id,
            'user_id' => $user->id,
            'warehouse_order_id' => $warehouseOrderId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil dikirim ke warehouse!',
            'data' => [
                'warehouse_order_id' => $warehouseOrderId,
                'part_kode' => $part->kode_part,
                'quantity' => $request->quantity
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Error requesting part to warehouse', [
            'part_id' => $part->id,
            'user_id' => $user->id,
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ], 500);
    }
}
}