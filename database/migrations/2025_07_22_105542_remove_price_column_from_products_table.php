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
            // Pastikan kolom 'price' ada sebelum menghapusnya
            // Jika kolom 'price' Anda adalah 'decimal', sesuaikan tipenya
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Tambahkan kembali kolom 'price' jika rollback
            // Sesuaikan tipe data dan atribut (misal: decimal, nullable, default)
            // dengan yang seharusnya ada di tabel 'products' Anda sebelumnya.
            $table->decimal('price', 15, 2)->nullable()->after('slug'); // Contoh posisi dan tipe data
        });
    }
};