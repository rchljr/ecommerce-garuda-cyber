<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles menggunakan firstOrCreate untuk menghindari error duplikasi
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'mitra', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'calon-mitra', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Anda juga bisa melakukan hal yang sama untuk permissions
        // Permission::firstOrCreate(['name' => 'edit articles', 'guard_name' => 'web']);
    }
}