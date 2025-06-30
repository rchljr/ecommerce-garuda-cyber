<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // PERBAIKAN: Mengubah '->' menjadi '\'
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id'); // Foreign key ke tabel products (UUID)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('path'); // Jalur gambar relatif (misal: 'product_gallery/image1.jpg')
            $table->integer('order')->default(0); // Untuk mengurutkan gambar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
