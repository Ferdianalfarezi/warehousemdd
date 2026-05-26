<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom baru ke tabel barangs
        Schema::table('barangs', function (Blueprint $table) {
            // Cek dan tambah kolom yang belum ada
            if (!Schema::hasColumn('barangs', 'cust')) {
                $table->string('cust')->nullable()->after('line');
            }
            if (!Schema::hasColumn('barangs', 'model')) {
                $table->string('model')->nullable()->after('cust');
            }
        });

        // Buat tabel dies_details untuk child part per delivery part
        Schema::create('dies_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('child_part_code')->nullable();
            $table->string('part_name')->nullable();
            $table->string('cust')->nullable();
            $table->string('model')->nullable();
            $table->string('process_name')->nullable();
            $table->string('process_no')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dies_details');

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['cust', 'model']);
        });
    }
};