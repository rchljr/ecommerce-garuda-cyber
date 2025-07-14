<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPriceFromOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cek dulu apakah kolom 'price' ada sebelum mencoba menghapusnya
        // untuk mencegah error jika migrasi dijalankan ulang.
        if (Schema::hasColumn('order_items', 'price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Jika migrasi di-rollback, tambahkan kembali kolom 'price'
        // agar proses bisa dibalikkan dengan aman.
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2);
        });
    }
}
