<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\Line;
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
        return view('barangs.index', [
            'suppliers' => Supplier::all(),
            'parts'     => Part::all(),
            'lines'     => Line::orderBy('nama_line')->get(),
        ]);
    }

    // ==================== AJAX DATA ====================
    public function getData(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = $request->get('per_page', 20);
        $page    = (int) $request->get('page', 1);

        $query = Barang::with(['supplier:id,nama', 'line:id,nama_line,mesin'])
            ->withCount('diesDetails')
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('kode_barang', 'like', "%{$search}%")
                   ->orWhere('nama', 'like', "%{$search}%")
                   ->orWhere('cust', 'like', "%{$search}%")
                   ->orWhere('model', 'like', "%{$search}%")
                   ->orWhere('address', 'like', "%{$search}%")
                   ->orWhereHas('supplier', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"))
                   ->orWhereHas('line', fn ($lq) => $lq->where('nama_line', 'like', "%{$search}%")
                                                        ->orWhere('mesin', 'like', "%{$search}%"));
            }));

        $total = $query->count();

        if ($perPage === 'all') {
            $barangs    = $query->latest()->get();
            $perPageInt = $total ?: 1;
        } else {
            $perPageInt = (int) $perPage;
            $barangs    = $query->latest()->skip(($page - 1) * $perPageInt)->take($perPageInt)->get();
        }

        $totalPages = $perPage === 'all' ? 1 : (int) ceil($total / max($perPageInt, 1));

        $data = $barangs->values()->map(fn ($b, $i) => [
            'id'          => $b->id,
            'row_number'  => (($page - 1) * $perPageInt) + $i + 1,
            'kode_barang' => $b->kode_barang,
            'nama'        => $b->nama,
            'cust'        => $b->cust,
            'model'       => $b->model,
            'line_id'     => $b->line_id,
            'line'        => $b->line?->nama_line,
            'line_mesin'  => $b->line?->mesin,
            'address'     => $b->address,
            'gambar'      => $b->gambar,
            'gambar_url'  => $b->gambar ? asset('storage/barangs/' . $b->gambar) : null,
            'supplier'    => $b->supplier?->nama ?? '-',
            'dies_count'  => $b->dies_details_count,
        ]);

        return response()->json([
            'success'    => true,
            'data'       => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPageInt,
                'current_page' => $page,
                'total_pages'  => $totalPages,
                'from'         => $total > 0 ? (($page - 1) * $perPageInt) + 1 : 0,
                'to'           => min($page * $perPageInt, $total),
            ],
        ]);
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts', 'dies_details']);
            if ($request->hasFile('gambar')) {
                $data['gambar'] = $this->storeImage($request->file('gambar'));
            }

            $barang = Barang::create($data);
            $this->savePartsAndDies($barang, $request);

            DB::commit();
            $barang->load(['supplier', 'line', 'parts', 'diesDetails']);

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
        $barang->load(['supplier', 'line', 'parts', 'diesDetails']);
        return response()->json(['success' => true, 'data' => $barang]);
    }

    // ==================== UPDATE ====================
    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), $this->rules($barang->id));
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts', 'dies_details']);
            if ($request->hasFile('gambar')) {
                $this->deleteImage($barang->gambar);
                $data['gambar'] = $this->storeImage($request->file('gambar'));
            }

            $barang->update($data);
            DetailBarang::where('barang_id', $barang->id)->delete();
            DiesDetail::where('barang_id', $barang->id)->delete();
            $this->savePartsAndDies($barang, $request);

            DB::commit();
            $barang->load(['supplier', 'line', 'parts', 'diesDetails']);

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
            $this->deleteImage($barang->gambar);
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
            $sheet = IOFactory::load($request->file('excel_file')->getRealPath())->getActiveSheet();
            $rows  = $sheet->toArray(null, true, true, false);

            [$headerRow, $headerIdx] = $this->findHeaderRow($rows);
            if (!$headerRow) {
                return response()->json(['success' => false, 'message' => 'Header row tidak ditemukan!'], 422);
            }

            $colMap = $this->mapColumns($headerRow);
            if (!isset($colMap['delivery_part_code'])) {
                return response()->json(['success' => false, 'message' => 'Kolom DELIVERY PART CODE tidak ditemukan!'], 422);
            }

            $grouped  = $this->groupRows($rows, $headerIdx, $colMap);
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
                    DiesDetail::create(['barang_id' => $barang->id] + $detail);
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
    private function rules(?int $ignoreId = null): array
    {
        $unique = $ignoreId ? "unique:barangs,kode_barang,{$ignoreId}" : 'unique:barangs,kode_barang';

        return [
            'kode_barang'      => "required|string|max:255|{$unique}",
            'nama'             => 'required|string|max:255',
            'supplier_id'      => 'required|exists:suppliers,id',
            'address'          => 'nullable|string|max:255',
            'line_id'          => 'nullable|exists:lines,id',
            'cust'             => 'nullable|string|max:255',
            'model'            => 'nullable|string|max:255',
            'gambar'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parts'            => 'nullable|array',
            'parts.*.part_id'  => 'nullable|exists:parts,id',
            'parts.*.quantity' => 'nullable|integer|min:1',
        ];
    }

    private function storeImage($image): string
    {
        $imgName = time() . '_' . $image->getClientOriginalName();
        $path    = public_path('storage/barangs');
        if (!file_exists($path)) mkdir($path, 0777, true);
        $image->move($path, $imgName);
        return $imgName;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) return;
        $full = public_path('storage/barangs/' . $filename);
        if (file_exists($full)) unlink($full);
    }

    private function savePartsAndDies(Barang $barang, Request $request): void
    {
        foreach ($request->parts ?? [] as $partData) {
            if (empty($partData['part_id'])) continue;
            DetailBarang::create([
                'barang_id' => $barang->id,
                'part_id'   => $partData['part_id'],
                'quantity'  => $partData['quantity'] ?? 1,
            ]);
        }

        foreach ($request->dies_details ?? [] as $idx => $d) {
            if (empty($d['child_part_code']) && empty($d['part_name'])) continue;
            DiesDetail::create([
                'barang_id'       => $barang->id,
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

    private function findHeaderRow(array $rows): array
    {
        foreach ($rows as $idx => $row) {
            foreach ($row as $cell) {
                if ($cell && str_contains(strtoupper((string) $cell), 'DELIVERY PART CODE')) {
                    return [$row, $idx];
                }
            }
        }
        return [null, null];
    }

    private function mapColumns(array $headerRow): array
    {
        $needles = [
            'delivery_part_code' => 'DELIVERY PART',
            'child_part_code'    => 'CHILD PART',
            'part_name'          => 'PART NAME',
            'cust'               => 'CUSTOMER',
            'model'              => 'MODEL',
            'process_name'       => 'PROSES NAME',
            'process_no'         => 'PROSES NO',
        ];

        $colMap = [];
        foreach ($headerRow as $colIdx => $colName) {
            if (!$colName) continue;
            $clean = strtoupper(trim(str_replace(["\n", "\r"], ' ', (string) $colName)));
            foreach ($needles as $key => $needle) {
                if (str_contains($clean, $needle)) $colMap[$key] = $colIdx;
            }
        }
        return $colMap;
    }

    private function groupRows(array $rows, int $headerIdx, array $colMap): array
    {
        $get = fn ($row, $key) => isset($colMap[$key]) ? trim((string) ($row[$colMap[$key]] ?? '')) : '';

        $grouped = [];
        for ($i = $headerIdx + 1; $i < count($rows); $i++) {
            $row          = $rows[$i];
            $deliveryCode = $get($row, 'delivery_part_code');
            if (empty($deliveryCode)) continue;

            if (!isset($grouped[$deliveryCode])) {
                $grouped[$deliveryCode] = [
                    'part_name' => '',
                    'cust'      => $get($row, 'cust'),
                    'model'     => $get($row, 'model'),
                    'details'   => [],
                ];
            }

            if (empty($grouped[$deliveryCode]['part_name'])) {
                $pn = $get($row, 'part_name');
                if ($pn !== '') $grouped[$deliveryCode]['part_name'] = $pn;
            }

            $grouped[$deliveryCode]['details'][] = [
                'child_part_code' => $get($row, 'child_part_code'),
                'part_name'       => $get($row, 'part_name'),
                'cust'            => $get($row, 'cust'),
                'model'           => $get($row, 'model'),
                'process_name'    => $get($row, 'process_name'),
                'process_no'      => $get($row, 'process_no'),
                'sort_order'      => count($grouped[$deliveryCode]['details']),
            ];
        }
        return $grouped;
    }
}