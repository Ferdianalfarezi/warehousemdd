<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penanda preventive yang ditarik LEBIH AWAL (sebelum jatuh tempo schedule) oleh superadmin.
 * Saat selesai, service berikutnya = jatuh tempo asli + 1 periode (bukan tanggal selesai + periode).
 * Aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['general_checkups', 'history_checkups'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'is_early')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->boolean('is_early')->default(false)->after('status');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['general_checkups', 'history_checkups'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'is_early')) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn('is_early'));
            }
        }
    }
};
