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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('image_path'); // Path ke file gambar slide
            $table->string('title')->nullable(); // Judul slide
            $table->text('content')->nullable(); // Isi/deskripsi slide
            $table->string('text_position')->nullable(); // Posisi teks (misal: 'left', 'center', 'right')
            $table->string('text_color')->nullable(); // Warna huruf (misal: '#FFFFFF', 'white', 'text-white')
            $table->string('button_text')->nullable(); // Judul tombol (misal: 'Belanja Sekarang')
            $table->string('button_url')->nullable(); // URL link tombol
            $table->integer('order')->default(0); // Urutan slide
            $table->boolean('is_active')->default(true); // Status aktif/non-aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};