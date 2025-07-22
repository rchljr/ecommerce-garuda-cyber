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
        Schema::table('varians', function (Blueprint $table) {
            // Tambahkan kolom 'image_path' sebagai string yang bisa null
            // Anda bisa menempatkannya setelah kolom 'options_data' atau 'stock'
            $table->string('image_path')->nullable()->after('options_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varians', function (Blueprint $table) {
            // Hapus kolom 'image_path' saat rollback migrasi ini
            $table->dropColumn('image_path');
        });
    }
};