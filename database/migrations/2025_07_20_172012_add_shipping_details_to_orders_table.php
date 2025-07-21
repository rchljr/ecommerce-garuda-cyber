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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address')->nullable()->after('status');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_zip_code')->nullable()->after('shipping_city');
            $table->string('shipping_phone')->nullable()->after('shipping_zip_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
            $table->dropColumn('shipping_city');
            $table->dropColumn('shipping_zip_code');
            $table->dropColumn('shipping_phone');
        });
    }
};