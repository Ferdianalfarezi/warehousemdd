<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $successCount = 0;
    private $failedCount = 0;
    private $totalCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        $this->totalCount++;

        try {
            // Skip jika nama kosong
            if (empty($row['nama'])) {
                $this->failedCount++;
                $this->errors[] = "Baris {$this->totalCount}: Nama supplier tidak boleh kosong";
                return null;
            }

            // Cek apakah supplier sudah ada (berdasarkan nama)
            $existingSupplier = Supplier::where('nama', $row['nama'])->first();
            
            if ($existingSupplier) {
                // Update supplier yang sudah ada
                $existingSupplier->update([
                    'alamat' => $row['alamat'] ?? $existingSupplier->alamat,
                ]);
                $this->successCount++;
                return null;
            }

            // Buat supplier baru
            $this->successCount++;
            return new Supplier([
                'nama' => $row['nama'],
                'alamat' => $row['alamat'] ?? '',
            ]);

        } catch (\Exception $e) {
            $this->failedCount++;
            $this->errors[] = "Baris {$this->totalCount}: " . $e->getMessage();
            Log::error("Import Supplier Error: " . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama supplier wajib diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
        ];
    }

    public function getResults()
    {
        return [
            'total' => $this->totalCount,
            'success' => $this->successCount,
            'failed' => $this->failedCount,
            'errors' => $this->errors
        ];
    }
}