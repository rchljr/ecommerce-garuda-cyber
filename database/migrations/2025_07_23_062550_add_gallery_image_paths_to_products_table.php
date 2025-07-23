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
        Schema::table('products', function (Blueprint $table) {
            // Tambahkan kolom 'gallery_image_paths' sebagai tipe JSON
            // Anda bisa menempatkannya setelah 'main_image' atau kolom lain yang sesuai.
            $table->json('gallery_image_paths')->nullable()->after('main_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom 'gallery_image_paths' saat rollback migrasi ini
            $table->dropColumn('gallery_image_paths');
        });
    }
};