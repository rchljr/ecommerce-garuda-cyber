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
            $table->unsignedBigInteger('monthly_price')->nullable();
            $table->unsignedBigInteger('yearly_price')->nullable();
            $table->unsignedTinyInteger('discount_year')->default(0);

            $table->boolean('is_trial')->default(false);
            $table->unsignedSmallInteger('trial_days')->nullable();
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
