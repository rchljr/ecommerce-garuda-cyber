<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari user admin untuk dijadikan pemilik voucher. Ganti email jika perlu.
        $admin = User::role('admin')->first();

        // Jika admin tidak ditemukan, hentikan seeder.
        if (!$admin) {
            $this->command->error('User admin tidak ditemukan. Harap buat user admin terlebih dahulu.');
            return;
        }

        $vouchers = [
            [
                'user_id' => $admin->id,
                'voucher_code' => 'HEMAT50PERSEN',
                'description' => 'Diskon spesial sebesar 50% untuk semua paket.',
                'discount' => 50,
                'start_date' => Carbon::now()->startOfMonth(),
                'expired_date' => Carbon::now()->endOfMonth(),
                'min_spending' => 250000,
            ],
            [
                'user_id' => $admin->id,
                'voucher_code' => 'SUPERDEAL',
                'description' => 'Potongan harga sebesar 10% untuk paket tahunan.',
                'discount' => 10,
                'start_date' => Carbon::now()->addMonth()->startOfMonth(), // Akan aktif bulan depan
                'expired_date' => Carbon::now()->addMonth()->endOfMonth(),
                'min_spending' => 1000000,
            ],
            [
                'user_id' => $admin->id,
                'voucher_code' => 'EXPIREDVCR',
                'description' => 'Voucher yang sudah tidak berlaku.',
                'discount' => 25,
                'start_date' => Carbon::now()->subMonths(2)->startOfMonth(), // Sudah kedaluwarsa
                'expired_date' => Carbon::now()->subMonths(1)->endOfMonth(),
                'min_spending' => 0,
            ],
        ];

        // Masukkan atau memperbarui data ke dalam database
        foreach ($vouchers as $voucherData) {
            Voucher::updateOrCreate(
                ['voucher_code' => $voucherData['voucher_code']], // Kunci unik untuk mencari
                $voucherData // Data untuk dibuat atau diperbarui
            );
        }
        
        $this->command->info('Voucher seeder berhasil dijalankan.');
    }
}

