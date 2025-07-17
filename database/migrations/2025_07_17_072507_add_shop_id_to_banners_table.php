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
        Schema::table('banners', function (Blueprint $table) {
            // Tambahkan kolom 'shop_id' dengan tipe data UUID
            // Letakkan setelah kolom lain, misalnya 'id'
            $table->uuid('shop_id')->nullable()->after('id');

            // Buat foreign key constraint ke tabel 'shops'
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika migrasi di-rollback
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};