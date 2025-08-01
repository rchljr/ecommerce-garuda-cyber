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
            ->whereNull('subdomain_id')
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
            ->paginate(5)->appends($request->query());
    }

    public function getVoucherById($id)
    {
        return Voucher::findOrFail($id);
    }

    /**
     * Mengambil voucher yang aktif dan dapat digunakan berdasarkan subtotal belanja dan pemilik toko.
     *
     * @param float $subtotal
     * @param int $shopOwnerId 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicableVouchers(float $subtotal, int $shopOwnerId)
    {
        return Voucher::where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->where('min_spending', '<=', $subtotal)
            ->where('user_id', $shopOwnerId)
            ->orderBy('min_spending', 'desc')
            ->get();
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
}
