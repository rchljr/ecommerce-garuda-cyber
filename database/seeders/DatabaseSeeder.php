<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SubscriptionPackageSeeder::class,
            InitialDataSeeder::class,
            TestimonialSeeder::class,
            VoucherSeeder::class,
            PointRewardSeeder::class,
            TemplateSeeder::class,
        ]);

        $this->call([
            MitraTokoSeeder::class,
            TenantProductTestimonialSeeder::class,
        ]);
    }
}
