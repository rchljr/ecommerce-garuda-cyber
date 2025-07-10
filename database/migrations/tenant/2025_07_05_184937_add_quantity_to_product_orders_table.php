<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ... (kode lainnya)
    public function up(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            // Tambahkan baris ini
            $table->integer('quantity')->default(1)->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            // Tambahkan baris ini
            $table->dropColumn('quantity');
        });
    }
    // ... (kode lainnya)
};
