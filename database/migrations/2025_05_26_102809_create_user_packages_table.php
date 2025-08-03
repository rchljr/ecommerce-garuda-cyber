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
        Schema::create('user_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('subs_package_id');
            $table->string('plan_type'); // 'monthly' atau 'yearly'
            $table->decimal('price_paid', 15, 2)->nullable();
            $table->date('active_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->enum('status', ['active', 'pending', 'expired'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subs_package_id')->references('id')->on('subscription_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_packages');
    }
};
