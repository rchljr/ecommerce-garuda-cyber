<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            // Menambahkan kolom baru untuk menyimpan ID varian
            $table->uuid('product_variant_id')->nullable()->after('product_id');

            // Menambahkan foreign key constraint ke tabel product_variants
            // Pastikan tabel 'product_variants' Anda sudah ada
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('varians')
                ->onDelete('set null'); // Jika varian dihapus, kolom ini akan menjadi NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
