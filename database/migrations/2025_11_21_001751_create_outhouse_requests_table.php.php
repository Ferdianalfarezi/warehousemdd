<?php
// database/migrations/xxxx_create_outhouse_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outhouse_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_checkup_id')->constrained()->onDelete('cascade');
            $table->foreignId('checkup_detail_id')->constrained()->onDelete('cascade');
            $table->text('problem');
            $table->string('mesin');
            $table->string('supplier');
            $table->enum('status', ['pending', 'confirmed', 'on_process', 'completed'])->default('pending');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outhouse_requests');
    }
};