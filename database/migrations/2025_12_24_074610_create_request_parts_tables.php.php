<?php
// database/migrations/xxxx_create_request_parts_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Request Parts (Header)
        Schema::create('request_parts', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('requester_name');
            
            $table->enum('status', ['pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed', 'rejected', 'verified'])->default('pending');
            $table->integer('warehouse_order_id')->nullable();
            $table->text('catatan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_request')->useCurrent();
            $table->timestamp('tanggal_approve_kadiv')->nullable();
            $table->timestamp('tanggal_approve_kagud')->nullable();
            $table->timestamp('tanggal_verified')->nullable();
            $table->foreignId('approved_by_kadiv')->nullable()->constrained('users');
            $table->foreignId('approved_by_kagud')->nullable()->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Tabel Request Parts Items (Detail)
        Schema::create('request_part_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_part_id')->constrained('request_parts')->onDelete('cascade');
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('quantity_approved')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('item_status', ['pending', 'on_request', 'completed', 'verified'])->default('pending');
            $table->timestamps();
        });

        // Tabel History Request Parts (Header)
        Schema::create('history_request_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_part_id')->constrained('request_parts')->onDelete('cascade');
            $table->string('request_number');
            $table->foreignId('user_id')->constrained();
            $table->string('requester_name');
            
            $table->string('status');
            $table->integer('warehouse_order_id')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_request');
            $table->timestamp('tanggal_completed')->nullable();
            $table->timestamp('tanggal_verified')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Tabel History Request Parts Items (Detail)
        Schema::create('history_request_part_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_request_part_id')->constrained('history_request_parts')->onDelete('cascade');
            $table->foreignId('part_id')->constrained();
            $table->string('part_code');
            $table->string('part_name');
            $table->integer('quantity');
            $table->integer('quantity_approved')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_request_part_items');
        Schema::dropIfExists('history_request_parts');
        Schema::dropIfExists('request_part_items');
        Schema::dropIfExists('request_parts');
    }
};