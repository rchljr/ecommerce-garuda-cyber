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
        Schema::table('orders', function (Blueprint $table) {
            // Ubah enum untuk menambahkan 'cancelled'
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kembalikan ke state semula jika di-rollback
            $table->enum('status', ['pending', 'completed'])->default('pending')->change();
        });

    }
};
