<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->boolean('is_best_seller')->default(false)->after('status');
        $table->boolean('is_new_arrival')->default(false)->after('is_best_seller');
        $table->boolean('is_hot_sale')->default(false)->after('is_new_arrival');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['is_best_seller', 'is_new_arrival', 'is_hot_sale']);
    });
}
};
