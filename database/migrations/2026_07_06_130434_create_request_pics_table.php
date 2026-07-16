<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_repair_pics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_repair_id')
                ->constrained('request_repairs')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            // 1 user cuma boleh tercatat sekali sebagai PIC per request repair
            $table->unique(['request_repair_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_repair_pics');
    }
};