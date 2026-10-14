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
        Schema::table('general_checkups', function (Blueprint $table) {
            $table->unsignedInteger('durasi_manual_menit')->nullable()->after('waktu_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('general_checkups', function (Blueprint $table) {
            $table->dropColumn('durasi_manual_menit');
        });
    }
};
