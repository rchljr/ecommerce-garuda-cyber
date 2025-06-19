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
        Schema::create('subscription_package_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_package_id');
            $table->string('feature');
            $table->timestamps();

            $table->foreign('subscription_package_id')->references('id')->on('subscription_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_package_features');
    }
};
