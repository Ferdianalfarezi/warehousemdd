<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            // Add order_id reference
            $table->unsignedBigInteger('order_id')->nullable()->after('id_pud');
            
            // Add request_status
            $table->enum('request_status', [
                'available',      // Belum direquest / Ready (default)
                'pending',        // Menunggu approval Kadiv
                'approved_kadiv', // Approved by Kadiv, menunggu Kagud
                'approved_kagud', // Approved by Kagud, sedang disiapkan
                'ready',          // Siap diambil
                'rejected'        // Ditolak
            ])->default('available')->after('order_id');
            
            // Add request quantity
            $table->integer('request_quantity')->nullable()->after('request_status');
            
            // Add timestamps untuk tracking
            $table->timestamp('requested_at')->nullable()->after('request_quantity');
            $table->unsignedBigInteger('requested_by')->nullable()->after('requested_at');
            
            // Add foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['requested_by']);
            $table->dropColumn([
                'order_id', 
                'request_status', 
                'request_quantity',
                'requested_at', 
                'requested_by'
            ]);
        });
    }
};