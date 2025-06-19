<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherService
{
    public function getAllVouchers()
    {
        // Ambil voucher milik user admin yang login, atau sesuaikan kebutuhan
        return Voucher::orderByDesc('created_at')->get();
    }

    public function getVoucherById($id)
    {
        return Voucher::findOrFail($id);
    }

    public function createVoucher(array $data)
    {
        $data['user_id'] = Auth::id();
        return Voucher::create($data);
    }

    public function updateVoucher($id, array $data)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update($data);
        return $voucher;
    }

    public function deleteVoucher($id)
    {
        $voucher = Voucher::findOrFail($id);
        return $voucher->delete();
    }

    public function apply(string $code, Order $order)
    {
        $voucher = Voucher::where('voucher_code', strtolower($code))
            ->where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->first();

        if (!$voucher) {
            return ['error' => 'Voucher tidak valid atau sudah kedaluwarsa.'];
        }

        if ($order->total_price < $voucher->min_spending) {
            return ['error' => 'Total belanja tidak memenuhi minimum ' . format_rupiah($voucher->min_spending)];
        }

        $originalPrice = $order->total_price;
        $discountAmount = $voucher->discount;
        $newPrice = $originalPrice - $discountAmount;

        return [
            'success' => true,
            'original_price_formatted' => format_rupiah($originalPrice),
            'discount_formatted' => '- ' . format_rupiah($discountAmount),
            'new_total_formatted' => format_rupiah($newPrice < 0 ? 0 : $newPrice),
            'new_total' => $newPrice < 0 ? 0 : $newPrice
        ];
    }
}
