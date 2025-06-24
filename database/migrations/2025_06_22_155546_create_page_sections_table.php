<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained()->onDelete('cascade'); // Link ke tabel 'pages'
            $table->string('section_type'); // e.g., 'hero', 'banner', 'product_grid', 'blog_latest'
            $table->integer('order')->default(0); // Urutan seksi di halaman
            $table->boolean('is_active')->default(true); // Untuk mengaktifkan/menonaktifkan seksi
            $table->json('content')->nullable(); // Ini akan menyimpan data dinamis (teks, gambar, URL) dalam format JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};