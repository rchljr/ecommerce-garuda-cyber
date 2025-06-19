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
        Schema::create('testimonis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name')->comment('Nama pemberi testimoni jika ditambahkan oleh admin');
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5)->comment('Rating dari 1 sampai 5');
            $table->enum('status', ['published', 'pending'])->default('pending')->comment('Status testimoni: ditampilkan atau menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonis');
    }
};
