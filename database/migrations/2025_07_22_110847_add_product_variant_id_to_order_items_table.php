<?php

// database/migrations/xxxx_xx_xx_add_product_variant_id_to_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Asumsi ID varian adalah UUID. Sesuaikan jika Anda menggunakan bigIncrements.
            $table->uuid('product_variant_id')->nullable()->after('product_id');

            // Tambahkan foreign key constraint
            $table->foreign('product_variant_id')->references('id')->on('varians')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};