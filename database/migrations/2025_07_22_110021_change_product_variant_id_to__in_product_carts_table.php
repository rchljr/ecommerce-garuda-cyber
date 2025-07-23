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
    public function up()
    {
        Schema::table('product_carts', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama terlebih dahulu.
            // Nama constraint default Laravel adalah: table_column_foreign
            $table->dropForeign('product_carts_product_variant_id_foreign');

            // 2. Ubah tipe kolom menjadi UUID.
            $table->uuid('product_variant_id')->change();

            $table->foreign('product_variant_id')->references('id')->on('varians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_carts', function (Blueprint $table) {
            // Logika untuk mengembalikan perubahan jika diperlukan
            $table->dropForeign('product_carts_product_variant_id_foreign');
            $table->unsignedBigInteger('product_variant_id')->change();
            // Sesuaikan nama tabel referensi jika berbeda
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }
};
