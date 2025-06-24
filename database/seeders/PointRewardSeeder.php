<?php

namespace Database\Seeders;

use App\Models\PointReward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PointRewardSeeder extends Seeder
{
        /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('point_rewards')->truncate();

        PointReward::create([
            'name' => 'Voucher Diskon 90%',
            'description' => 'Tukarkan poin Anda untuk diskon besar!',
            'points_required' => 30,
        ]);
        PointReward::create([
            'name' => 'Gantungan Kunci Labubu',
            'description' => 'Dapatkan gantungan kunci edisi terbatas.',
            'points_required' => 100,
        ]);
        PointReward::create([
            'name' => 'Voucher Gratis Ongkir',
            'description' => 'Nikmati gratis ongkir tanpa minimum belanja.',
            'points_required' => 50,
        ]);
        PointReward::create([
            'name' => 'Tote Bag Eksklusif',
            'description' => 'Tote bag eksklusif dengan desain unik.',
            'points_required' => 150,
        ]);
    }
}
