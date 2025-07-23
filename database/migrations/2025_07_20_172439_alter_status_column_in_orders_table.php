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
        Schema::table('orders', function (Blueprint $table) {
            // Mengubah kolom 'status' menjadi string dengan panjang yang lebih besar
            // Contoh: string(20) atau string(50) untuk lebih aman
            $table->string('status', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mengembalikan kolom 'status' ke panjang semula jika diperlukan
            // Sesuaikan dengan definisi asli kolom 'status' Anda
            $table->string('status', 10)->change(); // Contoh: kembalikan ke 10 karakter
        });
    }
};