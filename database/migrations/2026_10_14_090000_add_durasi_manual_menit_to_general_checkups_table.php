<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Durasi preventive manual (menit). NULL = durasi otomatis (mulai_perbaikan -> waktu_selesai).
     */
    public function up(): void
    {
        // Aman dijalankan ulang kalau kolom sudah ada di server
        if (Schema::hasColumn('general_checkups', 'durasi_manual_menit')) {
            return;
        }

        Schema::table('general_checkups', function (Blueprint $table) {
            $table->unsignedInteger('durasi_manual_menit')->nullable()->after('waktu_selesai');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('general_checkups', 'durasi_manual_menit')) {
            return;
        }

        Schema::table('general_checkups', function (Blueprint $table) {
            $table->dropColumn('durasi_manual_menit');
        });
    }
};
