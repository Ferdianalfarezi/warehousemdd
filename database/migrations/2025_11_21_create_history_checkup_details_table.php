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
        Schema::create('history_checkup_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_checkup_id')->constrained('history_checkups')->onDelete('cascade');
            $table->unsignedBigInteger('check_indicator_id');
            $table->unsignedBigInteger('check_indicator_standard_id');
            $table->string('nama_bagian');
            $table->string('poin');
            $table->enum('status', ['ok', 'ng']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('history_checkup_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_checkup_details');
    }
};