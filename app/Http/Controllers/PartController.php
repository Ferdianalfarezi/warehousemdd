<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartsImport;
use App\Exports\PartsTemplateExport;
use App\Services\WarehouseOrderService;    
use App\Models\RequestPart;
use App\Models\HistoryRequestPart;
use App\Models\RequestPartItem;

class PartController extends Controller
{
    protected $warehouseOrderService;

    public function __construct(WarehouseOrderService $warehouseOrderService)
    {
        $this->warehouseOrderService = $warehouseOrderService;
    }

    public function index(Request $request)
    {
        $suppliers = Supplier::all();

        $stats = Part::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as habis,
            SUM(CASE WHEN stock > 0 AND stock <= min_stock THEN 1 ELSE 0 END) as hampir_habis,
            SUM(CASE WHEN stock > min_stock THEN 1 ELSE 0 END) as stock_aman
        ")->first();

        $onRequest = Part::whereHas('requestPartItems', function($q) {
            $q->whereIn('item_status', ['pending', 'on_request']);
        })->count();

        return view('parts.index', [
            'suppliers' => $suppliers,
            'totalParts' => $stats->total ?? 0,
            'stockAman' => $stats->stock_aman ?? 0,
            'hampirHabis' => $stats->hampir_habis ?? 0,
            'habis' => $stats->habis ?? 0,
            'onRequest' => $onRequest,
        ]);
    }

    public function getData(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $showAll = $perPage === 'all' || $perPage === '-1';

        $query = Part::with('supplier:id,nama');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kode_part', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('stock_condition')) {
            $condition = $request->stock_condition;

            switch ($condition) {
                case 'normal':
                    $query->whereColumn('stock', '>', 'min_stock');
                    break;
                case 'low':
                    $query->where('stock', '>', 0)
                          ->whereColumn('stock', '<=', 'min_stock');
                    break;
                case 'out':
                    $query->where('stock', '=', 0);
                    break;
                case 'on_request':
                    $onRequestPartIds = DB::table('request_part_items')
                        ->whereIn('item_status', ['pending', 'on_request'])
                        ->pluck('part_id')
                        ->unique()
                        ->toArray();
                    $query->whereIn('id', $onRequestPartIds);
                    break;
            }
        }

        if (!empty($status)) {
            switch ($status) {
                case 'habis':
                    $query->where('stock', 0);
                    break;
                case 'low':
                    $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock');
                    break;
                case 'normal':
                    $query->whereColumn('stock', '>', 'min_stock');
                    break;
            }
        }

        $total = $query->count();

        if ($showAll) {
            $parts = $query->latest()->get();
            $perPage = $total;
            $page = 1;
        } else {
            $perPage = (int) $perPage;
            $parts = $query->latest()
                           ->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();
        }

        $data = $parts->map(function($part, $index) use ($page, $perPage, $showAll) {
            $rowNumber = $showAll ? $index + 1 : (($page - 1) * $perPage) + $index + 1;
            return [
                'id' => $part->id,
                'row_number' => $rowNumber,
                'kode_part' => $part->kode_part,
                'nama' => $part->nama,
                'stock' => $part->stock,
                'min_stock' => $part->min_stock,
                'max_stock' => $part->max_stock,
                'satuan' => $part->satuan,
                'status' => $part->status,
                'status_label' => $part->status_label,
                'status_badge_class' => $part->status_badge_class,
                'supplier_nama' => $part->supplier->nama ?? '-',
                'address' => $part->address ?? 'N/A',
                'id_pud' => $part->id_pud,
                'gambar' => $part->gambar,
                'image_path' => $part->image_path,
                'is_below_min' => $part->isBelowMinStock(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => $showAll ? 'all' : (int) $perPage,
                'total' => $total,
                'total_pages' => $showAll ? 1 : ceil($total / $perPage),
                'from' => $total > 0 ? (($page - 1) * ($showAll ? $total : $perPage)) + 1 : 0,
                'to' => min($total, $page * ($showAll ? $total : $perPage)),
            ]
        ]);
    }

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
            'supplier_id' => 'required|exists:suppliers,id',
            'id_pud' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();

            $path = public_path('storage/parts');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $image->move($path, $imageName);
            $data['gambar'] = $imageName;
        }

        $part = Part::create($data);
        $part->load('supplier');

        return response()->json([
            'success' => true,
            'message' => 'Part berhasil ditambahkan!',
            'data' => $part
        ]);
    }

    public function show(Part $part)
    {
        $part->load('supplier');
        return response()->json([
            'success' => true,
            'data' => $part
        ]);
    }

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
            'supplier_id' => 'required|exists:suppliers,id',
            'id_pud' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            if ($part->gambar) {
                $oldImagePath = public_path('storage/parts/' . $part->gambar);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();

            $path = public_path('storage/parts');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $image->move($path, $imageName);
            $data['gambar'] = $imageName;
        }

        $part->update($data);
        $part->load('supplier');

        return response()->json([
            'success' => true,
            'message' => 'Part berhasil diupdate!',
            'data' => $part
        ]);
    }

    public function destroy(Part $part)
    {
        try {
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

    public function downloadTemplate()
    {
        return Excel::download(new PartsTemplateExport(), 'template_import_parts.xlsx');
    }

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

        if (!$part->id_pud) {
            return response()->json([
                'success' => false,
                'message' => 'Part ini belum di-mapping ke warehouse system (ID PUD kosong).'
            ], 400);
        }

        $user = auth()->user();
        $requesterName = $user->name ?? $user->username ?? $user->email ?? 'MDD User';
        $departmentId = $user->department_id ?? 10;

        try {
            DB::beginTransaction();

            $requestPart = RequestPart::create([
                'user_id' => $user->id,
                'requester_name' => $requesterName,
                'department_id' => $departmentId,
                'status' => 'pending',
                'catatan' => "Single request - Part: {$part->kode_part}",
                'tanggal_request' => now(),
            ]);

            RequestPartItem::create([
                'request_part_id' => $requestPart->id,
                'part_id' => $part->id,
                'quantity' => $request->quantity,
                'keterangan' => $request->keterangan,
                'item_status' => 'pending',
            ]);

            $items = [
                [
                    'barang_id' => $part->id_pud,
                    'quantity' => (int) $request->quantity,
                    'keterangan' => $request->keterangan ?? "Request part: {$part->kode_part} - {$part->nama}"
                ]
            ];

            $response = $this->warehouseOrderService->submitOrder(
                items: $items,
                requesterName: $requesterName,
                departmentId: (int) $departmentId,
                catatan: "Request dari MDD - {$requestPart->request_number}"
            );

            if (!$response['success']) {
                DB::rollBack();

                $errorMessage = 'Gagal mengirim request ke warehouse.';
                if (isset($response['data']['message'])) {
                    $errorMessage .= ' ' . $response['data']['message'];
                } elseif (isset($response['error'])) {
                    $errorMessage .= ' Error: ' . $response['error'];
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            $warehouseOrderId = $response['data']['data']['order_id']
                ?? $response['data']['data']['id']
                ?? $response['data']['order_id']
                ?? null;

            if ($warehouseOrderId) {
                $requestPart->update(['warehouse_order_id' => $warehouseOrderId]);
            }

            $requestPart->items()->update(['item_status' => 'on_request']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request berhasil dikirim! No. Request: {$requestPart->request_number}",
                'data' => [
                    'request_id' => $requestPart->id,
                    'request_number' => $requestPart->request_number,
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
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRequestWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_ids' => 'required|array|min:1',
            'part_ids.*' => 'required|exists:parts,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $requesterName = $user->name ?? $user->username ?? $user->email ?? 'MDD User';
        $departmentId = $user->department_id ?? 10;

        try {
            DB::beginTransaction();

            $requestPart = RequestPart::create([
                'user_id' => $user->id,
                'requester_name' => $requesterName,
                'department_id' => $departmentId,
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            $warehouseItems = [];

            foreach ($request->part_ids as $index => $partId) {
                $part = Part::findOrFail($partId);

                if (!$part->id_pud) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Part {$part->kode_part} belum di-mapping ke warehouse (ID PUD kosong)"
                    ], 400);
                }

                $quantity = $request->quantities[$index];

                RequestPartItem::create([
                    'request_part_id' => $requestPart->id,
                    'part_id' => $part->id,
                    'quantity' => $quantity,
                    'item_status' => 'pending',
                ]);

                $warehouseItems[] = [
                    'barang_id' => $part->id_pud,
                    'quantity' => (int) $quantity,
                    'keterangan' => "Part: {$part->kode_part} - {$part->nama}"
                ];
            }

            $response = $this->warehouseOrderService->submitOrder(
                items: $warehouseItems,
                requesterName: $requesterName,
                departmentId: (int) $departmentId,
                catatan: "Bulk request dari MDD Warehouse - {$requestPart->request_number}"
            );

            if (!$response['success']) {
                DB::rollBack();

                $errorMessage = 'Gagal mengirim request ke warehouse.';
                if (isset($response['data']['message'])) {
                    $errorMessage .= ' ' . $response['data']['message'];
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            $warehouseOrderId = $response['data']['data']['order_id']
                ?? $response['data']['data']['id']
                ?? $response['data']['order_id']
                ?? null;

            if ($warehouseOrderId) {
                $requestPart->update(['warehouse_order_id' => $warehouseOrderId]);
            }

            $requestPart->items()->update(['item_status' => 'on_request']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request berhasil dikirim!",
                'data' => [
                    'request_id' => $requestPart->id,
                    'request_number' => $requestPart->request_number,
                    'warehouse_order_id' => $warehouseOrderId
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error bulk requesting parts', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}