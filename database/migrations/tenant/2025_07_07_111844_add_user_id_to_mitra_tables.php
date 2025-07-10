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
        Schema::table('heroes', function (Blueprint $table) {
            // $table->foreignUuid('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->uuid('user_id');
            $table->index('user_id');
        });

        Schema::table('banners', function (Blueprint $table) {
            // $table->foreignUuid('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->uuid('user_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
