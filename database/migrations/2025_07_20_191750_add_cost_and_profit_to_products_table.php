// database/migrations/YYYY_MM_DD_HHMMSS_add_cost_and_profit_to_products_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('modal_price', 10, 2)->nullable()->after('price'); // Harga modal
            $table->decimal('profit_percentage', 5, 2)->nullable()->after('modal_price'); // Persentase keuntungan (misal: 20.00 untuk 20%)
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('profit_percentage');
            $table->dropColumn('modal_price');
        });
    }
};