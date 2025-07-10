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
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable(); // Misal: "Summer Collection"
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Path gambar latar belakang hero
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->boolean('is_active')->default(true); // Untuk mengaktifkan/menonaktifkan slide
            $table->integer('order')->default(0); // Untuk mengurutkan slide
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
