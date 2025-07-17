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
        // Dalam file migrasi
        Schema::table('subdomains', function (Blueprint $table) {
            // 'setup_in_progress', 'published'
            $table->string('publication_status')->default('setup_in_progress')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subdomains', function (Blueprint $table) {
            //
        });
    }
};
