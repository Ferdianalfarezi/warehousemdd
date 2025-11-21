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
            // Debug: uncomment baris ini untuk lihat struktur data
            // \Log::info('Row data:', $row);
            
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

            // Process gambar - copy dari public/images/part ke public/storage/parts
            $imageName = null;
            if (!empty($gambar)) {
                $imageName = $this->copyImageToStorage($gambar, $kodePart);
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
                'gambar' => $imageName
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
     * Copy gambar dari public/images/part ke public/storage/parts
     * 
     * @param string $fileName - Nama file gambar yang ada di kolom Excel
     * @param string $kodePart - Kode part untuk nama file baru
     * @return string|null - Nama file baru atau null jika gagal/tidak ditemukan
     */
    private function copyImageToStorage($fileName, $kodePart)
{
    $sourceDirectory = public_path('images/part');
    $destinationDirectory = public_path('storage/parts');

    if (!File::exists($sourceDirectory)) return null;
    if (!File::exists($destinationDirectory)) {
        File::makeDirectory($destinationDirectory, 0777, true);
    }

    $clean = strtolower(str_replace([' ', '_'], '', $fileName));

    $files = File::files($sourceDirectory);

    $matched = null;

    foreach ($files as $file) {
        $base = strtolower(str_replace([' ', '_'], '', $file->getFilename()));

        // Jika Excel menulis `abc` tapi file aslinya `abc.png`
        if (str_contains($base, $clean)) {
            $matched = $file->getRealPath();
            break;
        }
    }

    if (!$matched) return null;

    $extension = pathinfo($matched, PATHINFO_EXTENSION);
    $newFileName = time().'_'.str_replace(' ', '_', $kodePart).'.'.$extension;

    File::copy($matched, $destinationDirectory.'/'.$newFileName);

    return $newFileName;
}



    public function rules(): array
    {
        return [
            // Removed validation rules, we'll handle it manually in model() method
        ];
    }

    public function getResults()
    {
        return $this->results;
    }
}