<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_indicator_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_indicator_id')->constrained('check_indicators')->onDelete('cascade');
            $table->string('poin');
            $table->string('metode');
            $table->text('standar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_indicator_standards');
    }
};
