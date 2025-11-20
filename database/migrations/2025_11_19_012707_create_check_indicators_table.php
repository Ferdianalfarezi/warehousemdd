<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('part_id')->nullable()->constrained('parts')->onDelete('set null');
            $table->string('nama_bagian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_indicators');
    }
};