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
       

        // Tabel untuk history request part items (detail)
        Schema::create('history_request_part_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('history_request_part_id');
            $table->unsignedBigInteger('part_id');
            $table->string('part_code');
            $table->string('part_name');
            $table->integer('quantity');
            $table->integer('quantity_approved')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('item_status')->default('verified');
            $table->timestamps();
            
            // Indexes
            $table->index('history_request_part_id');
            $table->index('part_id');
            
            // Foreign key
            $table->foreign('history_request_part_id')
                ->references('id')
                ->on('history_request_parts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_request_part_items');
    
    }
};