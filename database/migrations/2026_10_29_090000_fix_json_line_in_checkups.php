<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan data: kolom `line` di general_checkups / history_checkups sempat terisi
 * objek Line dalam bentuk JSON, mis. {"id":12,"nama_line":"PR",...}
 * (bug ScheduleObserver: menyimpan relasi $barang->line, bukan $barang->line->nama_line).
 *
 * Ubah jadi nama line-nya saja. Aman dijalankan ulang (hanya menyentuh nilai berbentuk JSON objek).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['general_checkups', 'history_checkups'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'line')) {
                continue;
            }

            // Ambil semua dulu (jangan chunk: baris yang sudah diperbaiki keluar dari filter -> offset bergeser)
            $rows = DB::table($tableName)->where('line', 'like', '{%')->get(['id', 'line']);

            foreach ($rows as $row) {
                $data = json_decode($row->line, true);
                if (!is_array($data)) {
                    continue; // bukan JSON valid, biarkan
                }

                DB::table($tableName)->where('id', $row->id)->update([
                    'line' => $data['nama_line'] ?? null,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan ke JSON (data JSON itu memang salah)
    }
};
