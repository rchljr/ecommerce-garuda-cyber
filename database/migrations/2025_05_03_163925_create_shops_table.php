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
        Schema::create('shops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('shop_name');
            $table->date('year_founded')->nullable();
            $table->text('shop_address');
            $table->string('product_categories');
            $table->text('shop_photo');
            $table->text('ktp');
            $table->text('sku')->nullable();
            $table->text('npwp')->nullable();
            $table->text('nib')->nullable();
            $table->text('iumk')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_models');
    }
};
