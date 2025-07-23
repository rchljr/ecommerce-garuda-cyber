<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subdomains', function (Blueprint $table) {
            // Menambahkan kolom untuk status publikasi yang diatur oleh mitra
            // Tipe kolom diubah menjadi ENUM untuk konsistensi data
            $table->enum('publication_status', ['published', 'pending'])->default('pending')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subdomains', function (Blueprint $table) {
            $table->dropColumn('publication_status');
        });
    }
};
