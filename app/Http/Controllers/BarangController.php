<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\DetailBarang;
use App\Models\DiesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BarangController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        $parts     = Part::all();
        return view('barangs.index', compact('suppliers', 'parts'));
    }

    // ==================== AJAX DATA ====================
    public function getData(Request $request)
{
    $search  = $request->get('search', '');
    $perPage = $request->get('per_page', 20);
    $page    = (int) $request->get('page', 1);
 
    $query = Barang::with(['supplier:id,nama'])  // select minimal kolom supplier
        ->withCount('diesDetails')               // GANTI: count saja, tidak load seluruh collection
        ->when($search, function ($q) use ($search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('kode_barang', 'like', "%{$search}%")
                   ->orWhere('nama', 'like', "%{$search}%")
                   ->orWhere('cust', 'like', "%{$search}%")
                   ->orWhere('model', 'like', "%{$search}%")
                   ->orWhere('address', 'like', "%{$search}%")
                   ->orWhereHas('supplier', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"));
            });
        });
 
    $total = $query->count();
 
    if ($perPage === 'all') {
        $barangs    = $query->latest()->get();
        $perPageInt = $total ?: 1;
    } else {
        $perPageInt = (int) $perPage;
        $barangs    = $query->latest()->skip(($page - 1) * $perPageInt)->take($perPageInt)->get();
    }
 
    $totalPages = $perPage === 'all' ? 1 : (int) ceil($total / max($perPageInt, 1));
    $from       = $total > 0 ? (($page - 1) * $perPageInt) + 1 : 0;
    $to         = min($page * $perPageInt, $total);
 
    $data = $barangs->map(function ($barang, $idx) use ($page, $perPageInt) {
        return [
            'id'          => $barang->id,
            'row_number'  => (($page - 1) * $perPageInt) + $idx + 1,
            'kode_barang' => $barang->kode_barang,
            'nama'        => $barang->nama,
            'cust'        => $barang->cust,
            'model'       => $barang->model,
            'address'     => $barang->address,
            'line'        => $barang->line,
            'gambar'      => $barang->gambar,
            'gambar_url'  => $barang->gambar ? asset('storage/barangs/' . $barang->gambar) : null,
            'supplier'    => $barang->supplier?->nama ?? '-',
            'dies_count'  => $barang->dies_details_count,  // pakai hasil withCount langsung
        ];
    });
 
    return response()->json([
        'success' => true,
        'data'    => $data,
        'pagination' => [
            'total'        => $total,
            'per_page'     => $perPageInt,
            'current_page' => $page,
            'total_pages'  => $totalPages,
            'from'         => $from,
            'to'           => $to,
        ],
    ]);
}

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang'      => 'required|string|unique:barangs,kode_barang|max:255',
            'nama'             => 'required|string|max:255',
            'supplier_id'      => 'required|exists:suppliers,id',
            'address'          => 'nullable|string|max:255',
            'line'             => 'nullable|string|max:255',
            'cust'             => 'nullable|string|max:255',
            'model'            => 'nullable|string|max:255',
            'gambar'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parts'            => 'required|array|min:1',
            'parts.*.part_id'  => 'required|exists:parts,id',
            'parts.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts', 'dies_details']);

            if ($request->hasFile('gambar')) {
                $image   = $request->file('gambar');
                $imgName = time() . '_' . $image->getClientOriginalName();
                $path    = public_path('storage/barangs');
                if (!file_exists($path)) mkdir($path, 0777, true);
                $image->move($path, $imgName);
                $data['gambar'] = $imgName;
            }

            $barang = Barang::create($data);

            foreach ($request->parts as $partData) {
                DetailBarang::create([
                    'barang_id' => $barang->id,
                    'part_id'   => $partData['part_id'],
                    'quantity'  => $partData['quantity'],
                ]);
            }

            if ($request->has('dies_details')) {
                $this->saveDiesDetails($barang->id, $request->dies_details);
            }

            DB::commit();
            $barang->load(['supplier', 'parts', 'diesDetails']);

            return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan!', 'data' => $barang]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Barang Store Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ==================== SHOW ====================
    public function show(Barang $barang)
    {
        $barang->load(['supplier', 'parts', 'diesDetails']);
        return response()->json(['success' => true, 'data' => $barang]);
    }

    // ==================== UPDATE ====================
    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang'      => 'required|string|max:255|unique:barangs,kode_barang,' . $barang->id,
            'nama'             => 'required|string|max:255',
            'supplier_id'      => 'required|exists:suppliers,id',
            'address'          => 'nullable|string|max:255',
            'line'             => 'nullable|string|max:255',
            'cust'             => 'nullable|string|max:255',
            'model'            => 'nullable|string|max:255',
            'gambar'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parts'            => 'required|array|min:1',
            'parts.*.part_id'  => 'required|exists:parts,id',
            'parts.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts', 'dies_details']);

            if ($request->hasFile('gambar')) {
                if ($barang->gambar) {
                    $old = public_path('storage/barangs/' . $barang->gambar);
                    if (file_exists($old)) unlink($old);
                }
                $image   = $request->file('gambar');
                $imgName = time() . '_' . $image->getClientOriginalName();
                $path    = public_path('storage/barangs');
                if (!file_exists($path)) mkdir($path, 0777, true);
                $image->move($path, $imgName);
                $data['gambar'] = $imgName;
            }

            $barang->update($data);

            DetailBarang::where('barang_id', $barang->id)->delete();
            foreach ($request->parts as $partData) {
                DetailBarang::create([
                    'barang_id' => $barang->id,
                    'part_id'   => $partData['part_id'],
                    'quantity'  => $partData['quantity'],
                ]);
            }

            DiesDetail::where('barang_id', $barang->id)->delete();
            if ($request->has('dies_details')) {
                $this->saveDiesDetails($barang->id, $request->dies_details);
            }

            DB::commit();
            $barang->load(['supplier', 'parts', 'diesDetails']);

            return response()->json(['success' => true, 'message' => 'Barang berhasil diupdate!', 'data' => $barang]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ==================== DESTROY ====================
    public function destroy(Barang $barang)
    {
        DB::beginTransaction();
        try {
            if ($barang->gambar) {
                $img = public_path('storage/barangs/' . $barang->gambar);
                if (file_exists($img)) unlink($img);
            }

            DiesDetail::where('barang_id', $barang->id)->delete();
            DetailBarang::where('barang_id', $barang->id)->delete();
            $barang->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus barang!'], 500);
        }
    }

    // ==================== IMPORT EXCEL ====================
    public function importExcel(Request $request)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls|max:10240']);

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            $headerRow = null;
            $headerIdx = null;
            foreach ($rows as $idx => $row) {
                foreach ($row as $cell) {
                    if ($cell && str_contains(strtoupper((string)$cell), 'DELIVERY PART CODE')) {
                        $headerRow = $row;
                        $headerIdx = $idx;
                        break 2;
                    }
                }
            }

            if (!$headerRow) {
                return response()->json(['success' => false, 'message' => 'Header row tidak ditemukan!'], 422);
            }

            $colMap = [];
            foreach ($headerRow as $colIdx => $colName) {
                if (!$colName) continue;
                $clean = strtoupper(trim(str_replace(["\n", "\r"], ' ', (string)$colName)));
                if (str_contains($clean, 'DELIVERY PART'))  $colMap['delivery_part_code'] = $colIdx;
                if (str_contains($clean, 'CHILD PART'))     $colMap['child_part_code']    = $colIdx;
                if (str_contains($clean, 'PART NAME'))      $colMap['part_name']           = $colIdx;
                if (str_contains($clean, 'CUSTOMER'))       $colMap['cust']                = $colIdx;
                if (str_contains($clean, 'MODEL'))          $colMap['model']               = $colIdx;
                if (str_contains($clean, 'PROSES NAME'))    $colMap['process_name']        = $colIdx;
                if (str_contains($clean, 'PROSES NO'))      $colMap['process_no']          = $colIdx;
            }

            if (!isset($colMap['delivery_part_code'])) {
                return response()->json(['success' => false, 'message' => 'Kolom DELIVERY PART CODE tidak ditemukan!'], 422);
            }

            $grouped = [];
            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row          = $rows[$i];
                $deliveryCode = trim((string)($row[$colMap['delivery_part_code']] ?? ''));
                if (empty($deliveryCode)) continue;

                if (!isset($grouped[$deliveryCode])) {
                    $grouped[$deliveryCode] = [
                        'part_name' => '',
                        'cust'      => isset($colMap['cust'])  ? trim((string)($row[$colMap['cust']]  ?? '')) : '',
                        'model'     => isset($colMap['model']) ? trim((string)($row[$colMap['model']] ?? '')) : '',
                        'details'   => [],
                    ];
                }

                // Ambil part_name dari baris manapun yang non-empty dalam group (first-wins)
                if (empty($grouped[$deliveryCode]['part_name']) && isset($colMap['part_name'])) {
                    $pn = trim((string)($row[$colMap['part_name']] ?? ''));
                    if ($pn !== '') {
                        $grouped[$deliveryCode]['part_name'] = $pn;
                    }
                }

                $grouped[$deliveryCode]['details'][] = [
                    'child_part_code' => isset($colMap['child_part_code']) ? trim((string)($row[$colMap['child_part_code']] ?? '')) : '',
                    'part_name'       => isset($colMap['part_name'])       ? trim((string)($row[$colMap['part_name']] ?? ''))       : '',
                    'cust'            => isset($colMap['cust'])             ? trim((string)($row[$colMap['cust']] ?? ''))             : '',
                    'model'           => isset($colMap['model'])            ? trim((string)($row[$colMap['model']] ?? ''))            : '',
                    'process_name'    => isset($colMap['process_name'])    ? trim((string)($row[$colMap['process_name']] ?? ''))    : '',
                    'process_no'      => isset($colMap['process_no'])      ? trim((string)($row[$colMap['process_no']] ?? ''))      : '',
                    'sort_order'      => count($grouped[$deliveryCode]['details']),
                ];
            }

            $imported = 0;
            $updated  = 0;

            DB::beginTransaction();
            foreach ($grouped as $deliveryCode => $data) {
                $barang = Barang::where('kode_barang', $deliveryCode)->first();

                if (!$barang) {
                    $barang = Barang::create([
                        'kode_barang' => $deliveryCode,
                        'nama'        => $data['part_name'] ?: '-',
                        'cust'        => $data['cust'],
                        'model'       => $data['model'],
                        'supplier_id' => null,
                    ]);
                    $imported++;
                } else {
                    $barang->update([
                        'cust'  => $data['cust']  ?: $barang->cust,
                        'model' => $data['model'] ?: $barang->model,
                    ]);
                    $updated++;
                }

                DiesDetail::where('barang_id', $barang->id)->delete();
                foreach ($data['details'] as $detail) {
                    DiesDetail::create([
                        'barang_id'       => $barang->id,
                        'child_part_code' => $detail['child_part_code'],
                        'part_name'       => $detail['part_name'],
                        'cust'            => $detail['cust'],
                        'model'           => $detail['model'],
                        'process_name'    => $detail['process_name'],
                        'process_no'      => $detail['process_no'],
                        'sort_order'      => $detail['sort_order'],
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import selesai! {$imported} barang baru, {$updated} barang diupdate.",
                'stats'   => ['imported' => $imported, 'updated' => $updated, 'total' => count($grouped)],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Excel Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    // ==================== HELPERS ====================
    private function saveDiesDetails(int $barangId, array $details): void
    {
        foreach ($details as $idx => $d) {
            if (empty($d['child_part_code']) && empty($d['part_name'])) continue;
            DiesDetail::create([
                'barang_id'       => $barangId,
                'child_part_code' => $d['child_part_code'] ?? null,
                'part_name'       => $d['part_name']       ?? null,
                'cust'            => $d['cust']             ?? null,
                'model'           => $d['model']            ?? null,
                'process_name'    => $d['process_name']     ?? null,
                'process_no'      => $d['process_no']       ?? null,
                'sort_order'      => $idx,
            ]);
        }
    }
}