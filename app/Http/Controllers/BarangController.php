<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['supplier', 'parts'])->latest()->get();
        $suppliers = Supplier::all();
        $parts = Part::all();
        return view('barangs.index', compact('barangs', 'suppliers', 'parts'));
    }

    public function store(Request $request)
    {
        // Log request untuk debugging
        Log::info('Barang Store Request', $request->all());

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|unique:barangs,kode_barang|max:255',
            'nama' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'address' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parts' => 'required|array|min:1',
            'parts.*.part_id' => 'required|exists:parts,id',
            'parts.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts']);

            // Handle image upload
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = public_path('storage/barangs');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $image->move($path, $imageName);
                $data['gambar'] = $imageName;
            }

            $barang = Barang::create($data);

            // Save parts
            foreach ($request->parts as $partData) {
                DetailBarang::create([
                    'barang_id' => $barang->id,
                    'part_id' => $partData['part_id'],
                    'quantity' => $partData['quantity'],
                ]);
            }

            DB::commit();

            $barang->load(['supplier', 'parts']);

            Log::info('Barang Created Successfully', ['id' => $barang->id]);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan!',
                'data' => $barang
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Barang Store Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Barang $barang)
    {
        $barang->load(['supplier', 'parts']);
        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:255|unique:barangs,kode_barang,' . $barang->id,
            'nama' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'address' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parts' => 'required|array|min:1',
            'parts.*.part_id' => 'required|exists:parts,id',
            'parts.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['gambar', 'parts']);

            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($barang->gambar) {
                    $oldImagePath = public_path('storage/barangs/' . $barang->gambar);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = public_path('storage/barangs');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $image->move($path, $imageName);
                $data['gambar'] = $imageName;
            }

            $barang->update($data);

            // Delete existing parts and add new ones
            DetailBarang::where('barang_id', $barang->id)->delete();
            
            foreach ($request->parts as $partData) {
                DetailBarang::create([
                    'barang_id' => $barang->id,
                    'part_id' => $partData['part_id'],
                    'quantity' => $partData['quantity'],
                ]);
            }

            DB::commit();

            $barang->load(['supplier', 'parts']);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diupdate!',
                'data' => $barang
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Barang $barang)
    {
        DB::beginTransaction();
        try {
            // Delete image if exists
            if ($barang->gambar) {
                $imagePath = public_path('storage/barangs/' . $barang->gambar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete related parts first
            DetailBarang::where('barang_id', $barang->id)->delete();
            
            // Then delete the barang
            $barang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang!'
            ], 500);
        }
    }
}