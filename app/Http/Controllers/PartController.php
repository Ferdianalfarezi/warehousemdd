<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartsImport;
use App\Exports\PartsTemplateExport;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parts = Part::with('supplier')->latest()->get();
        $suppliers = Supplier::all();
        return view('parts.index', compact('parts', 'suppliers'));
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
}