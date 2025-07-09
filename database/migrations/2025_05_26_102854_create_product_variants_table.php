<?php
// File: database/migrations/xxxx_create_product_variants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->string('color');
            $table->string('size');
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};