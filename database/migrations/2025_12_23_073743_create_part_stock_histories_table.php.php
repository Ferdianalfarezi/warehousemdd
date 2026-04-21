<?php
// database/migrations/2024_12_23_create_part_stock_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_request_item_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['in', 'out', 'adjustment'])->comment('in=masuk, out=keluar, adjustment=koreksi');
            $table->integer('quantity');
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['part_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_stock_histories');
    }
};