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
        Schema::create('request_parts', function (Blueprint $table) {
            $table->id();

            $table->string('request_no')->unique(); 
            // contoh: RP-2025-00001

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('requester_name');
            $table->unsignedBigInteger('department_id')->nullable();

            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'on_process',
                'complete',
                'rejected'
            ])->default('pending');

            $table->unsignedInteger('total_items')->default(0);

            // ID dari sistem warehouse (kalau ada)
            $table->unsignedBigInteger('warehouse_order_id')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_parts');
    }
};
