<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Part;
use App\Models\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportBarangController extends Controller
{
    /**
     * Show import form
     */
    public function index()
    {
        return view('barangs.import');
    }

    /**
     * Handle Excel import dari MASTER_Dies.xlsx
     *
     * Struktur Excel (header di row 5, index 4):
     * - Col 0: NO.
     * - Col 1: DELIVERY PART CODE  → kode_barang di barangs
     * - Col 2: CHILD PART CODE     → kode_part di parts (lookup)
     * - Col 3: PART NAME           → nama di barangs
     * - Col 4: CUSTOMER
     * - Col 5: MODEL
     * - Col 6: PROSES NAME         → process_name di detail_barangs
     * - Col 7: PROSES NO           → process_no di detail_barangs
     *
     * Relasi:
     * - 1 DELIVERY PART CODE = 1 Barang
     * - 1 Barang bisa punya multiple baris (multiple proses)
     * - CHILD PART CODE = kode_part yang di-lookup dari tabel parts → detail_barangs
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            // Header ada di row 5 (index 4, tapi PhpSpreadsheet 1-based → row 5)
            // Data mulai dari row 7 (skip header row 5 dan blank row 6)
            $highestRow = $sheet->getHighestRow();

            $imported   = 0;
            $skipped    = 0;
            $notFound   = []; // Part codes yang tidak ditemukan di DB
            $errors     = [];

            // Cache parts untuk performa (kode_part => id)
            $partsCache = Part::pluck('id', 'kode_part')->toArray();

            DB::beginTransaction();

            for ($row = 7; $row <= $highestRow; $row++) {
                $deliveryPartCode = $this->getCellValue($sheet, $row, 2); // Col B
                $childPartCode    = $this->getCellValue($sheet, $row, 3); // Col C
                $partName         = $this->getCellValue($sheet, $row, 4); // Col D
                $customer         = $this->getCellValue($sheet, $row, 5); // Col E
                $model            = $this->getCellValue($sheet, $row, 6); // Col F
                $processName      = $this->getCellValue($sheet, $row, 7); // Col G
                $processNo        = $this->getCellValue($sheet, $row, 8); // Col H

                // Skip baris kosong
                if (empty($deliveryPartCode)) {
                    continue;
                }

                // Cari atau buat Barang berdasarkan kode_barang
                $barang = Barang::firstOrCreate(
    ['kode_barang' => $deliveryPartCode],
    [
        'nama'        => $partName ?? $deliveryPartCode,
        'supplier_id' => null,
    ]
);

                // Jika barang sudah ada tapi nama kosong, update nama
                if ($barang->wasRecentlyCreated === false && empty($barang->nama) && !empty($partName)) {
                    $barang->update(['nama' => $partName]);
                }

                // Skip jika child_part_code kosong
                if (empty($childPartCode)) {
                    $skipped++;
                    continue;
                }

                // Cari Part berdasarkan kode_part
                $partId = $partsCache[$childPartCode] ?? null;

                if (!$partId) {
                    // Part tidak ditemukan di DB
                    if (!in_array($childPartCode, $notFound)) {
                        $notFound[] = $childPartCode;
                    }
                    $skipped++;
                    continue;
                }

                // Cek apakah detail sudah ada (berdasarkan barang_id + part_id + process_no)
                $existingDetail = DetailBarang::where('barang_id', $barang->id)
                    ->where('part_id', $partId)
                    ->where('process_no', $processNo)
                    ->first();

                if ($existingDetail) {
                    // Update jika sudah ada
                    $existingDetail->update([
                        'process_name' => $processName,
                    ]);
                } else {
                    // Buat detail baru
                    DetailBarang::create([
                        'barang_id'    => $barang->id,
                        'part_id'      => $partId,
                        'quantity'     => 1,
                        'process_name' => $processName,
                        'process_no'   => $processNo,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            $message = "Import selesai! {$imported} detail berhasil diimport.";
            if ($skipped > 0) {
                $message .= " {$skipped} baris dilewati.";
            }

            return response()->json([
                'success'   => true,
                'message'   => $message,
                'imported'  => $imported,
                'skipped'   => $skipped,
                'not_found' => $notFound, // Part codes yang tidak ada di DB
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Barang Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cell value by row and column number (1-based)
     */
    private function getCellValue($sheet, int $row, int $col): ?string
    {
        $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
        return $value !== null ? trim((string) $value) : null;
    }

    /**
     * Preview data sebelum import (opsional, 10 rows pertama)
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $file        = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();

            $preview = [];
            $count   = 0;

            for ($row = 7; $row <= $sheet->getHighestRow(); $row++) {
                $deliveryPartCode = $this->getCellValue($sheet, $row, 2);
                if (empty($deliveryPartCode)) continue;

                $preview[] = [
                    'delivery_part_code' => $deliveryPartCode,
                    'child_part_code'    => $this->getCellValue($sheet, $row, 3),
                    'part_name'          => $this->getCellValue($sheet, $row, 4),
                    'customer'           => $this->getCellValue($sheet, $row, 5),
                    'model'              => $this->getCellValue($sheet, $row, 6),
                    'process_name'       => $this->getCellValue($sheet, $row, 7),
                    'process_no'         => $this->getCellValue($sheet, $row, 8),
                ];

                if (++$count >= 10) break;
            }

            return response()->json([
                'success' => true,
                'data'    => $preview,
                'total'   => $sheet->getHighestRow() - 6, // estimasi
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ], 500);
        }
    }
}