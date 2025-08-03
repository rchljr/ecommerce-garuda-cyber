<?php

// File 1: database/migrations/xxxx_create_vouchers_table.php (Versi Perbaikan)

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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // ID Mitra Pembuat
            $table->uuid('subdomain_id')->nullable(); // ID Toko
            $table->string('voucher_code', 100)->unique();
            $table->text('description')->nullable();
            $table->decimal('min_spending', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('expired_date');

            $table->decimal('discount', 15, 2)->comment('Diskon harus dalam bentuk persen'); 
            $table->integer('max_uses')->nullable()->comment('Berapa kali voucher ini bisa digunakan secara total');
            $table->integer('max_uses_per_customer')->nullable()->comment('Berapa kali satu customer bisa pakai voucher ini');
            $table->boolean('is_for_new_customer')->default(false)->comment('Tandai jika ini voucher khusus customer baru');
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status voucher

            $table->timestamps();

            // --- FOREIGN KEY LAMA ANDA ---
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subdomain_id')->references('id')->on('subdomains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};