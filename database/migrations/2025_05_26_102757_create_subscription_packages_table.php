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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('package_name', 100);
            $table->text('description')->nullable();
            $table->decimal('monthly-price', 15, 2);
            $table->decimal('yearly-price', 15, 2);
            $table->decimal('discount_month', 15, 2)->nullable();
            $table->decimal('discount_year', 15, 2)->nullable();
            $table->text('features')->nullable();
            $table->boolean('is_trial')->default(false);
            $table->integer('trial_days')->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
