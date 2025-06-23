<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherService
{
    public function getPaginatedVouchers(Request $request)
    {
        $search = $request->input('search');

        return Voucher::query()
            ->select('*')
            ->selectRaw("
                CASE
                    WHEN NOW() BETWEEN start_date AND expired_date THEN 1
                    WHEN NOW() < start_date THEN 2
                    ELSE 3
                END AS status_order
            ")
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    // Cari berdasarkan kode voucher atau deskripsi
                    $q->where('voucher_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('status_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)->appends($request->query());
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
