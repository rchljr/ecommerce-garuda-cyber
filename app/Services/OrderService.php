<?php
namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Order;

class OrderService
{
    public function createSubscriptionOrder(User $user): Order
    {
        $userPackage = $user->userPackage;
        if (!$userPackage) {
            throw new Exception('User tidak memiliki paket langganan.');
        }

        // Cek jika sudah ada order pending untuk user ini
        $existingOrder = Order::where('user_id', $user->id)
                                ->where('status', 'pending')
                                ->first();
        if ($existingOrder) {
            return $existingOrder;
        }

        // Buat order baru
        return Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'order_date' => now(),
            'total_price' => $userPackage->price_paid,
        ]);
    }
}