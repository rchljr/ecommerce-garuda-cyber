<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            
            // Gunakan foreignId untuk 'tag_id' (ini mengasumsikan tabel 'tags' menggunakan ID auto-increment standar)
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_tag');
    }
};
