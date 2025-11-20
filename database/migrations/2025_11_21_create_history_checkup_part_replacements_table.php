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
        Schema::create('history_checkup_part_replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_checkup_id')->constrained('history_checkups')->onDelete('cascade');
            $table->unsignedBigInteger('history_checkup_detail_id')->nullable();
            $table->unsignedBigInteger('part_id');
            $table->string('kode_part');
            $table->string('nama_part');
            $table->integer('quantity_used')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('history_checkup_id');
            $table->index('part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_checkup_part_replacements');
    }
};