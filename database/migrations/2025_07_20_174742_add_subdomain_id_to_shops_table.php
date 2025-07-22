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
        Schema::table('shops', function (Blueprint $table) {
            // Tambahkan kolom 'subdomain_id' sebagai foreign key
            // Pastikan tipe data UUID cocok dengan ID di tabel 'subdomains'
            $table->uuid('subdomain_id')->nullable()->after('user_id');

            // Tambahkan foreign key constraint (opsional, tapi disarankan)
            // Pastikan tabel 'subdomains' sudah ada sebelum menjalankan migrasi ini
            $table->foreign('subdomain_id')->references('id')->on('subdomains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu jika ada
            $table->dropForeign(['subdomain_id']);
            // Kemudian hapus kolom
            $table->dropColumn('subdomain_id');
        });
    }
};