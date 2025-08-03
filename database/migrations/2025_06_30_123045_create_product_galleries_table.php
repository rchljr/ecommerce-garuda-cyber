<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');

            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_galleries');
    }
};
