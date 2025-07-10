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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('address_line1')->nullable(); // Alamat Baris 1
            $table->string('address_line2')->nullable(); // Alamat Baris 2 (opsional)
            $table->string('city')->nullable();
            $table->string('state')->nullable(); // Provinsi
            $table->string('postal_code')->nullable(); // Kode Pos
            $table->string('phone')->nullable(); // Nomor Telepon Utama
            $table->string('email')->nullable(); // Email Utama
            $table->string('website')->nullable(); // Website (opsional)
            $table->string('facebook_url')->nullable(); // Link Facebook
            $table->string('twitter_url')->nullable(); // Link Twitter
            $table->string('instagram_url')->nullable(); // Link Instagram
            $table->string('pinterest_url')->nullable(); // Link Pinterest
            $table->text('map_embed_code')->nullable(); // Kode embed peta (misal dari Google Maps)
            $table->text('working_hours')->nullable(); // Jam kerja (misal: "Senin-Jumat: 09:00 - 17:00")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
