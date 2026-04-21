<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_parts_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('request_part_id')
        ->constrained('request_parts')
        ->cascadeOnDelete();

    $table->foreignId('part_id')
        ->constrained('parts')
        ->restrictOnDelete();

    $table->unsignedBigInteger('barang_id'); 
    // = id_pud (ID barang warehouse)

    $table->integer('quantity')->unsigned();
    $table->string('satuan', 50);

    $table->string('kode_part');
    $table->string('nama_part');

    $table->text('keterangan')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_parts_items');
    }
};
