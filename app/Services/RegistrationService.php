<?php

namespace App\Services;

use Exception;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\Subdomain;
use App\Traits\UploadFile;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PartnerActivatedNotification;

class RegistrationService
{
    use UploadFile;

    protected $multiStep;


    public function __construct(MultiStepRegistrationService $multiStep)
    {
        $this->multiStep = $multiStep;
    }

    public function processRegistration()
    {
        return DB::transaction(function () {
            $allData = $this->multiStep->getAllData();
            //dd($allData);

            // Validasi data penting
            if (empty($allData['user']) || empty($allData['plan']) || empty($allData['template']) || empty($allData['subdomain']) || empty($allData['shop'])) {
                throw new Exception('Data registrasi tidak lengkap. Sesi mungkin telah berakhir.');
            }

            // 1. Buat User baru
            $user = User::create([
                'name' => $allData['user']['name'],
                'email' => $allData['user']['email'],
                'password' => Hash::make($allData['user']['password']),
                'phone' => $allData['user']['phone'],
                'position' => $allData['user']['position']
            ]);

            // 2. Berikan role 'calon-mitra' ke user baru
            $user->assignRole('calon-mitra');

            /// 3. Simpan Data Toko
            $shop = Shop::create(array_merge($allData['shop'], ['user_id' => $user->id]));

            // 4. Simpan Subdomain
            $subdomain = Subdomain::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'subdomain_name' => $allData['subdomain']['subdomain'],
                'status' => 'pending',
            ]);

            // 5. BUAT TENANT BARU
            Tenant::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'subdomain_id' => $subdomain->id, 
                'template_id' => $allData['template']['template_id'], // Ambil dari session
            ]);

            // 6. Ambil detail paket dan harga
            $package = SubscriptionPackage::findOrFail($allData['plan']['plan_id']);
            $price = $allData['plan']['plan_type'] === 'yearly' ? $package->yearly_price : $package->monthly_price;

            // 7. Buat Order
            $order = Order::create([
                'user_id' => $user->id,
                'voucher_id' => null, // Voucher diterapkan nanti di halaman pembayaran
                'status' => 'pending',
                'order_date' => now(),
                'total_price' => $price,
            ]);

            // 8. Simpan Paket Langganan User
            UserPackage::create([
                'user_id' => $user->id,
                'subs_package_id' => $package->id,
                'plan_type' => $allData['plan']['plan_type'],
                'price_paid' => $price, // Harga asli sebelum diskon
                'status' => 'pending',
            ]); 

            return $user;
        });
    }

    /**
     * Mengaktifkan akun trial untuk pengguna.
     *
     * @param User $user Pengguna yang akan diaktifkan.
     * @param SubscriptionPackage $package Paket trial yang dipilih.
     * @return void
     */
    public function activateTrialPackage(User $user, SubscriptionPackage $package, Subdomain $subdomain)
    {
        DB::transaction(function () use ($user, $package, $subdomain)  {
            // 1. Dapatkan data UserPackage yang dibuat saat registrasi
            $userPackage = $user->userPackage;
            if (!$userPackage) {
                throw new Exception("UserPackage tidak ditemukan untuk user ID: {$user->id}");
            }

            // 2. Update UserPackage untuk trial
            $userPackage->update([
                'status' => 'active',
                'price_paid' => 0, // Harga 0 untuk trial
                'active_date' => now(),
                'expired_date' => now()->addDays($package->trial_days ?? 14), // Gunakan data dari DB atau default 14 hari
            ]);

            $subdomain->update(['status' => 'active']);

            // 3. Update User menjadi Mitra Aktif
            $user->status = 'active';
            $user->removeRole('calon-mitra');
            $user->assignRole('mitra');
            $user->save();

            // 5. Kirim notifikasi aktivasi
            Notification::send($user, new PartnerActivatedNotification($user));
        });
    }
}