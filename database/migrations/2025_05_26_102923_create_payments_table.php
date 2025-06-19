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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('order_id')->nullable();
            $table->uuid('subs_package_id')->nullable();
            $table->string('midtrans_order_id')->nullable()->comment('Order ID dari Midtrans');
            $table->string('midtrans_transaction_status', 50)->nullable();
            $table->string('midtrans_payment_type', 50)->nullable();
            $table->string('midtrans_va_number', 50)->nullable();
            $table->string('midtrans_pdf_url', 255)->nullable();
            $table->text('midtrans_response')->nullable();
            $table->decimal('total_payment', 15, 2)->nullable();
            $table->decimal('admin_fee', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('subs_package_id')->references('id')->on('subscription_packages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
