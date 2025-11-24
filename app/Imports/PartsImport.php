<?php

namespace App\Imports;

use App\Models\Part;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\File;

class PartsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;
    
    private $results = [
        'total' => 0,
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];

    /**
     * Specify the row number where headings are located
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $this->results['total']++;
        
        try {
            // Handle different possible key formats
            $kodePart = $row['kode_part'] ?? $row['kodepart'] ?? null;
            $nama = $row['nama'] ?? null;
            $stock = $row['stock'] ?? 0;
            $minStock = $row['min_stock'] ?? $row['minstock'] ?? 0;
            $maxStock = $row['max_stock'] ?? $row['maxstock'] ?? 0;
            $satuan = $row['satuan'] ?? 'pcs';
            $address = $row['address'] ?? null;
            $line = $row['line'] ?? null;
            $supplierName = $row['supplier'] ?? 'supplier';
            $gambar = $row['gambar'] ?? null;

            // Validasi manual untuk field required
            if (empty($kodePart)) {
                $this->results['failed']++;
                $this->results['errors'][] = "Row {$this->results['total']}: Kode Part tidak boleh kosong";
                return null;
            }

            if (empty($nama)) {
                $this->results['failed']++;
                $this->results['errors'][] = "Row {$this->results['total']}: Nama Part tidak boleh kosong";
                return null;
            }

            // Cari atau buat supplier berdasarkan nama
            $supplier = Supplier::firstOrCreate(
                ['nama' => $supplierName],
                [
                    'nama' => $supplierName,
                    'alamat' => '-',
                    'telepon' => '-',
                    'email' => null
                ]
            );

            // Cek apakah kode_part sudah ada
            if (Part::where('kode_part', $kodePart)->exists()) {
                $this->results['failed']++;
                $this->results['errors'][] = "Row {$this->results['total']}: Kode Part '{$kodePart}' sudah ada";
                return null;
            }

            // Process gambar - cari file yang cocok dan simpan nama filenya
            $imageName = null;
            if (!empty($gambar)) {
                $imageName = $this->findMatchingImage($gambar);
                
                if (!$imageName) {
                    // Jika gambar tidak ditemukan, catat warning tapi tetap lanjut import
                    $this->results['errors'][] = "Row {$this->results['total']}: Warning - Image '{$gambar}' not found, part created without image";
                }
            }

            $part = Part::create([
                'kode_part' => $kodePart,
                'nama' => $nama,
                'stock' => (int)$stock,
                'min_stock' => (int)$minStock,
                'max_stock' => (int)$maxStock,
                'satuan' => $satuan,
                'address' => $address,
                'line' => $line,
                'supplier_id' => $supplier->id,
                'gambar' => $imageName,
                'gambar_source' => $imageName ? 'import' : null // Flag sumber gambar
            ]);

            $this->results['success']++;
            return $part;
            
        } catch (\Exception $e) {
            $this->results['failed']++;
            $this->results['errors'][] = "Row {$this->results['total']}: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Cari gambar yang cocok di folder public/images/parts
     * 
     * @param string $fileName - Nama file gambar dari Excel
     * @return string|null - Nama file yang ditemukan atau null
     */
    private function findMatchingImage($fileName)
    {
        $sourceDirectory = public_path('images/parts'); // 🔥 GANTI JADI 'parts'

        if (!File::exists($sourceDirectory)) {
            \Log::error("Directory not found: {$sourceDirectory}");
            return null;
        }

        // Bersihkan nama file dari Excel
        $cleanFileName = strtolower(trim($fileName));
        
        // Hilangkan extension jika ada
        $cleanFileName = preg_replace('/\.(png|jpg|jpeg|gif|webp)$/i', '', $cleanFileName);
        
        // Hilangkan spasi, underscore, dash
        $cleanFileName = str_replace([' ', '_', '-'], '', $cleanFileName);

        // Ambil semua file di folder
        $files = File::files($sourceDirectory);

        foreach ($files as $file) {
            $actualFileName = $file->getFilename();
            $actualFileBaseName = pathinfo($actualFileName, PATHINFO_FILENAME);
            
            // Bersihkan nama file asli juga
            $cleanActualFileName = strtolower(str_replace([' ', '_', '-'], '', $actualFileBaseName));
            
            // Exact match
            if ($cleanFileName === $cleanActualFileName) {
                return $actualFileName;
            }
        }

        return null;
    }

    public function rules(): array
    {
        return [];
    }

    public function getResults()
    {
        return $this->results;
    }
}