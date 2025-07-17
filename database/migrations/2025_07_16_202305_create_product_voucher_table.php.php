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
        // Tabel ini hanya berisi 2 kolom untuk menghubungkan produk dan voucher
        Schema::create('product_voucher', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('voucher_id');

            // Foreign key ke tabel products (pastikan tabel products menggunakan UUID juga)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // Foreign key ke tabel vouchers yang baru kita perbaiki
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');

            // Primary key gabungan untuk mencegah duplikasi
            $table->primary(['product_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_voucher');
    }
};
