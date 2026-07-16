<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_repair_histories', function (Blueprint $table) {
            // Snapshot nama-nama PIC yang menangani perbaikan ini (dipisah koma).
            // Disimpan sebagai text biasa (bukan relasi) karena history bersifat arsip/read-only.
            $table->text('pic_names')->nullable()->after('operator');
        });
    }

    public function down(): void
    {
        Schema::table('request_repair_histories', function (Blueprint $table) {
            $table->dropColumn('pic_names');
        });
    }
};