<?php

namespace App\Http\Controllers;

use App\Models\CheckIndicator;
use App\Models\CheckIndicatorStandard;
use App\Models\Barang;
use App\Services\CheckIndicatorSheetParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckIndicatorController extends Controller
{
    public function index()
    {
        $checkIndicators = CheckIndicator::with(['barang', 'standards'])
            ->latest()
            ->get();

        $barangs = Barang::all();

        return view('check-indicators.index', compact('checkIndicators', 'barangs'));
    }

    public function store(Request $request)
    {
        Log::info('CheckIndicator Store Request', $request->all());

        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'bagian' => 'required|array|min:1',
            'bagian.*.nama_bagian' => 'required|string|max:255',
            'bagian.*.standards' => 'required|array|min:1',
            'bagian.*.standards.*.poin' => 'required|string|max:255',
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
                    'nama_bagian' => $bagianData['nama_bagian'],
                ]);

                foreach ($bagianData['standards'] as $standardData) {
                    CheckIndicatorStandard::create([
                        'check_indicator_id' => $checkIndicator->id,
                        'poin' => $standardData['poin'],
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

        } catch (Throwable $e) {
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
        $checkIndicator->load(['barang', 'standards']);
        return response()->json([
            'success' => true,
            'data' => $checkIndicator
        ]);
    }

    public function getBarangDetails($barangId)
    {
        $barang = Barang::findOrFail($barangId);
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
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus check indicator!'
            ], 500);
        }
    }

    public function edit(CheckIndicator $checkIndicator)
    {
        $checkIndicator->load(['barang', 'standards']);
        $barangs = Barang::all();

        return response()->json([
            'success' => true,
            'data' => $checkIndicator,
            'barangs' => $barangs,
        ]);
    }

    public function update(Request $request, CheckIndicator $checkIndicator)
    {
        Log::info('CheckIndicator Update Request', $request->all());

        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'bagian' => 'required|array|min:1',
            'bagian.*.nama_bagian' => 'required|string|max:255',
            'bagian.*.standards' => 'required|array|min:1',
            'bagian.*.standards.*.poin' => 'required|string|max:255',
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
            $checkIndicator->update([
                'barang_id' => $request->barang_id,
                'nama_bagian' => $request->bagian[0]['nama_bagian'],
            ]);

            CheckIndicatorStandard::where('check_indicator_id', $checkIndicator->id)->delete();

            foreach ($request->bagian[0]['standards'] as $standardData) {
                CheckIndicatorStandard::create([
                    'check_indicator_id' => $checkIndicator->id,
                    'poin' => $standardData['poin'],
                    'standar' => $standardData['standar'],
                ]);
            }

            DB::commit();

            Log::info('CheckIndicator Updated Successfully');

            return response()->json([
                'success' => true,
                'message' => 'Check Indicator berhasil diupdate!',
            ]);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('CheckIndicator Update Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $path = $request->file('excel_file')->getRealPath();

        try {
            $parsed = (new CheckIndicatorSheetParser())->parse($path);
        } catch (Throwable $e) {
            Log::error('CheckIndicator Import Parse Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
            ], 422);
        }

        $barang = Barang::where('kode_barang', $parsed['part_no'])->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => "Barang dengan kode '{$parsed['part_no']}' belum terdaftar. Silakan daftarkan barang ini terlebih dahulu di menu Barang sebelum import.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldIndicatorIds = CheckIndicator::where('barang_id', $barang->id)->pluck('id');
            CheckIndicatorStandard::whereIn('check_indicator_id', $oldIndicatorIds)->delete();
            CheckIndicator::where('barang_id', $barang->id)->delete();

            foreach ($parsed['bagian'] as $bagianData) {
                $checkIndicator = CheckIndicator::create([
                    'barang_id' => $barang->id,
                    'nama_bagian' => $bagianData['nama_bagian'],
                ]);

                foreach ($bagianData['standards'] as $standardData) {
                    CheckIndicatorStandard::create([
                        'check_indicator_id' => $checkIndicator->id,
                        'poin' => $standardData['poin'],
                        'standar' => $standardData['standar'],
                    ]);
                }
            }

            DB::commit();

            Log::info('CheckIndicator Imported Successfully', [
                'part_no' => $parsed['part_no'],
                'bagian_count' => count($parsed['bagian']),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Check Indicator untuk part '{$parsed['part_no']}' berhasil diimport (" . count($parsed['bagian']) . " bagian).",
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('CheckIndicator Import Save Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }
}