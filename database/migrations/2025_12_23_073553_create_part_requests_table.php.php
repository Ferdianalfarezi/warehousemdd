<?php
// database/migrations/2024_12_23_create_part_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->comment('ID order dari warehouse system');
            $table->enum('status', [
                'pending',
                'approved_kadiv', 
                'approved_kagud',
                'rejected',
                'ready',
                'completed'
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            
            // Closed/completed tracking
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->text('receive_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_requests');
    }
};