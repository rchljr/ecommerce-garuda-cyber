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
            // Hanya tambahkan kolom JSON untuk menyimpan opsi varian fleksibel.
            // Tanpa 'after()' akan menempatkannya di akhir tabel.
            // Jika Anda ingin di posisi tertentu dan tahu kolom apa yang ada,
            // contoh: $table->json('options_data')->nullable()->after('stock');
            $table->json('options_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varians', function (Blueprint $table) {
            // Hanya hapus kolom 'options_data' saat rollback.
            // Jangan mencoba menambahkan kembali 'size' dan 'color' di sini
            // karena migrasi ini tidak menghapusnya.
            $table->dropColumn('options_data');
        });
    }
};