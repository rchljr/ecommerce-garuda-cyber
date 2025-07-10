<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            // Kolom Dasar
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();

            // Kolom Deskripsi
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Kolom Harga & Stok
            $table->decimal('price', 15, 2); // Mendukung harga dengan desimal
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit

            // Kolom Gambar
            $table->string('main_image')->nullable();

            // Kolom Relasi & Status
            // $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_id');
            $table->index('user_id');
            // $table->foreignUuid('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
            $table->uuid('sub_category_id'); // Biarkan sebagai kolom biasa tanpa relasi database
            $table->index('sub_category_id');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');

            // Kolom untuk fitur tambahan (opsional tapi disarankan)
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
