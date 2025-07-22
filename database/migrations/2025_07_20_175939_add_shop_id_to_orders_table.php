// database/migrations/YYYY_MM_DD_HHMMSS_add_shop_id_to_orders_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom shop_id
            $table->uuid('shop_id')->nullable()->after('user_id'); // Atau setelah kolom lain yang sesuai

            // Tambahkan foreign key constraint (opsional, tapi disarankan)
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

            // Jika Anda ingin menghapus subdomain_id dari tabel orders:
            // $table->dropColumn('subdomain_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['shop_id']);
            // Hapus kolom shop_id
            $table->dropColumn('shop_id');

            // Jika Anda menghapus subdomain_id di up(), tambahkan kembali di down()
            // $table->uuid('subdomain_id')->nullable()->after('user_id'); // Sesuaikan posisi dan properti
        });
    }
};