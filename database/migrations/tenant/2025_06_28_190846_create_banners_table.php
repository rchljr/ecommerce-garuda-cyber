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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Judul banner (opsional)
            $table->string('subtitle')->nullable(); // Subjudul banner (opsional)
            $table->string('image'); // Path gambar banner (wajib)
            $table->string('link_url')->nullable(); // URL tujuan saat banner diklik (opsional)
            $table->string('button_text')->nullable(); // Teks tombol (opsional)
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->integer('order')->default(0); // Urutan tampilan
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
