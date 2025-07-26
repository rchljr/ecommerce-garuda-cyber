<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            // Menambahkan kolom 'refund_method' setelah kolom 'reason'
            // Tipe ENUM membatasi nilai hanya untuk 'bri', 'bca', atau 'gopay'
            // nullable() berarti kolom ini boleh kosong jika diperlukan
            $table->enum('refund_method', ['bri', 'bca', 'gopay'])->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            // Jika migrasi di-rollback, hapus kolom 'refund_method'
            $table->dropColumn('refund_method');
        });
    }
};
