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
        Schema::create('history_request_parts', function (Blueprint $table) {
    $table->id();

    $table->string('request_no')->unique();

    $table->foreignId('user_id')->nullable();
    $table->string('requester_name');
    $table->unsignedBigInteger('department_id')->nullable();

    $table->enum('status', [
        'complete',
        'rejected'
    ]);

    $table->unsignedInteger('total_items');

    $table->unsignedBigInteger('warehouse_order_id')->nullable();

    $table->timestamp('completed_at')->nullable();

    $table->text('note')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_request_parts');
    }
};
