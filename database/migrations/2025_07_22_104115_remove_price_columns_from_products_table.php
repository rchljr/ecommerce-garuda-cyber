<?php

// database/migrations/xxxx_xx_xx_remove_price_columns_from_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Pastikan kolom ini ada sebelum dihapus
            $table->dropColumn(['modal_price', 'profit_percentage']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Tambahkan kembali kolom jika rollback
            $table->decimal('modal_price', 15, 2)->default(0.00)->after('description');
            $table->decimal('profit_percentage', 5, 2)->default(0.00)->after('modal_price');
        });
    }
};