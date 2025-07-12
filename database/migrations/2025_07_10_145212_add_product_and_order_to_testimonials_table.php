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
        Schema::table('testimonis', function (Blueprint $table) {
            // Tambahkan kolom untuk menautkan ke produk dan order
            // Ini akan membedakan ulasan produk dari ulasan umum (paket langganan)
            $table->foreignUuid('product_id')->nullable()->after('user_id')->constrained('products')->onDelete('set null');
            $table->foreignUuid('order_id')->nullable()->after('product_id')->constrained('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonis', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
