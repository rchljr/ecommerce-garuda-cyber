<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->string('product_id'); // Foreign key ke tabel products
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('name'); // Nama opsi, misal: 'Size', 'Color'
            $table->integer('order')->default(0); // Untuk mengurutkan opsi
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
