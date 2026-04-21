<?php
// database/migrations/2024_12_23_create_part_request_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->comment('Jumlah yang diminta');
            $table->integer('approved_quantity')->nullable()->comment('Jumlah yang disetujui (bisa beda dari request)');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->index(['part_request_id', 'part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_request_items');
    }
};