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
        Schema::create('checkup_part_replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_checkup_id')->constrained('general_checkups')->onDelete('cascade');
            $table->foreignId('checkup_detail_id')->nullable()->constrained('checkup_details')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->integer('quantity_used')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('general_checkup_id');
            $table->index('checkup_detail_id');
            $table->index('part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkup_part_replacements');
    }
};