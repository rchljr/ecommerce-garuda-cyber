<?php

// database/migrations/xxxx_xx_xx_add_price_columns_to_varians_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('varians', function (Blueprint $table) {
            // Tambahkan kolom modal_price dan profit_percentage ke tabel varians
            // Sesuaikan posisi 'after()' jika perlu
            $table->decimal('modal_price', 15, 2)->default(0.00)->after('options_data'); // Setelah options_data
            $table->decimal('profit_percentage', 5, 2)->default(0.00)->after('modal_price');
        });
    }

    public function down(): void
    {
        Schema::table('varians', function (Blueprint $table) {
            $table->dropColumn(['modal_price', 'profit_percentage']);
        });
    }
};