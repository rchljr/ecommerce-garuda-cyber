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
        Schema::create('actual_varians', function (Blueprint $table) {
            $table->id();
            $table->string('product_id'); // Foreign key ke tabel products
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('name'); // Nama kombinasi varian (misal: "Small / Red")
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('image')->nullable(); // Path gambar varian (bisa null)
            $table->json('options_data')->nullable(); // JSON: {"size": "S", "color": "Red"}
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_varians');
    }
};
