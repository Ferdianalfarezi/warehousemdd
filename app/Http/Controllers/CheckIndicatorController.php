<?php

namespace App\Http\Controllers;

use App\Models\CheckIndicator;
use App\Models\CheckIndicatorStandard;
use App\Models\Barang;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIndicatorController extends Controller
{
    public function index()
    {
        $checkIndicators = CheckIndicator::with(['barang.supplier', 'barang.parts', 'part', 'standards'])
            ->latest()
            ->get();
        
        $barangs = Barang::with(['supplier', 'parts'])->get();
        $parts = Part::all();
        
        return view('check-indicators.index', compact('checkIndicators', 'barangs', 'parts'));
    }

    public function store(Request $request)
    {
        Log::info('CheckIndicator Store Request', $request->all());

        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'part_id' => 'nullable|exists:parts,id',
            'bagian' => 'required|array|min:1',
            'bagian.*.nama_bagian' => 'required|string|max:255',
            'bagian.*.standards' => 'required|array|min:1',
            'bagian.*.standards.*.poin' => 'required|string|max:255',
            'bagian.*.standards.*.metode' => 'required|string|max:255',
            'bagian.*.standards.*.standar' => 'required|string',
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
            foreach ($request->bagian as $bagianData) {
                $checkIndicator = CheckIndicator::create([
                    'barang_id' => $request->barang_id,
                    'part_id' => $request->part_id,
                    'nama_bagian' => $bagianData['nama_bagian'],
                ]);

                foreach ($bagianData['standards'] as $standardData) {
                    CheckIndicatorStandard::create([
                        'check_indicator_id' => $checkIndicator->id,
                        'poin' => $standardData['poin'],
                        'metode' => $standardData['metode'],
                        'standar' => $standardData['standar'],
                    ]);
                }
            }

            DB::commit();

            Log::info('CheckIndicator Created Successfully');

            return response()->json([
                'success' => true,
                'message' => 'Check Indicator berhasil ditambahkan!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CheckIndicator Store Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(CheckIndicator $checkIndicator)
    {
        $checkIndicator->load(['barang.supplier', 'barang.parts', 'part', 'standards']);
        return response()->json([
            'success' => true,
            'data' => $checkIndicator
        ]);
    }

    public function getBarangDetails($barangId)
    {
        $barang = Barang::with(['supplier', 'parts'])->findOrFail($barangId);
        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function destroy(CheckIndicator $checkIndicator)
    {
        DB::beginTransaction();
        try {
            CheckIndicatorStandard::where('check_indicator_id', $checkIndicator->id)->delete();
            $checkIndicator->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check Indicator berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus check indicator!'
            ], 500);
        }
    }

    public function edit(CheckIndicator $checkIndicator)
    {
        $checkIndicator->load(['barang.supplier', 'barang.parts', 'part', 'standards']);
        $barangs = Barang::with(['supplier', 'parts'])->get();
        $parts = Part::all();
        
        return response()->json([
            'success' => true,
            'data' => $checkIndicator,
            'barangs' => $barangs,
            'parts' => $parts
        ]);
    }

    public function update(Request $request, CheckIndicator $checkIndicator)
    {
        Log::info('CheckIndicator Update Request', $request->all());

        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'part_id' => 'nullable|exists:parts,id',
            'bagian' => 'required|array|min:1',
            'bagian.*.nama_bagian' => 'required|string|max:255',
            'bagian.*.standards' => 'required|array|min:1',
            'bagian.*.standards.*.poin' => 'required|string|max:255',
            'bagian.*.standards.*.metode' => 'required|string|max:255',
            'bagian.*.standards.*.standar' => 'required|string',
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
            // Update check indicator
            $checkIndicator->update([
                'barang_id' => $request->barang_id,
                'part_id' => $request->part_id,
                'nama_bagian' => $request->bagian[0]['nama_bagian'], // Update to first bagian name
            ]);

            // Delete existing standards
            CheckIndicatorStandard::where('check_indicator_id', $checkIndicator->id)->delete();

            // Add new standards from first bagian
            foreach ($request->bagian[0]['standards'] as $standardData) {
                CheckIndicatorStandard::create([
                    'check_indicator_id' => $checkIndicator->id,
                    'poin' => $standardData['poin'],
                    'metode' => $standardData['metode'],
                    'standar' => $standardData['standar'],
                ]);
            }

            DB::commit();

            Log::info('CheckIndicator Updated Successfully');

            return response()->json([
                'success' => true,
                'message' => 'Check Indicator berhasil diupdate!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CheckIndicator Update Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}