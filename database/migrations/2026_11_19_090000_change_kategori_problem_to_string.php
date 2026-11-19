<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kategori Problem diganti daftar baru (lihat RequestRepair::KATEGORI_PROBLEM).
 * Kolom enum diubah jadi varchar supaya kategori baru bisa disimpan dan data lama
 * (Dies, Dimensi, Human Error, Accessories) tetap utuh. Pilihan dijaga di validasi.
 */
return new class extends Migration
{
    private array $tables = ['request_repairs', 'request_repair_histories'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `kategori_problem` VARCHAR(100) NULL");
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan ke enum: data dengan kategori baru akan gagal disimpan
    }
};
