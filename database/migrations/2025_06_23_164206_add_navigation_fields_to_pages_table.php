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
        Schema::table('pages', function (Blueprint $table) {
            // is_active: Untuk mengaktifkan/menonaktifkan halaman secara keseluruhan
            $table->boolean('is_active')->default(true)->after('meta_description');
            // show_in_nav: Untuk menentukan apakah halaman ini muncul di navigasi header
            $table->boolean('show_in_nav')->default(true)->after('is_active');
            // order: Untuk menentukan urutan halaman di navigasi
            $table->integer('order')->default(0)->after('show_in_nav');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('show_in_nav');
            $table->dropColumn('is_active');
        });
    }
};