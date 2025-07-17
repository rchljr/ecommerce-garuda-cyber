<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_shop_settings_table.php

    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();

            // GANTI INI:
            // $table->foreignId('shop_id')->constrained()->onDelete('cascade');

            // MENJADI INI:
            $table->uuid('shop_id'); // Pastikan tipenya sama (uuid)
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['shop_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
