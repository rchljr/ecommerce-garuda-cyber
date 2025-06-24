<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PointReward;

class CustomerPointController extends Controller
{
    /**
     * Menampilkan halaman poin saya.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua hadiah yang aktif dan bisa ditukarkan
        $rewards = PointReward::where('is_active', true)
            ->orderBy('points_required', 'asc')
            ->get();

        return view('customer.points', [
            'user' => $user,
            'rewards' => $rewards,
        ]);
    }

    /**
     * Menangani logika penukaran poin.
     */
    public function redeem(Request $request, $rewardId)
    {
        $user = Auth::user();
        $reward = PointReward::findOrFail($rewardId);

        // Cek apakah poin pengguna mencukupi
        if ($user->points < $reward->points_required) {
            return back()->with('error', 'Poin Anda tidak cukup untuk menukarkan hadiah ini.');
        }

        // Kurangi poin pengguna
        $user->points -= $reward->points_required;
        $user->save();

        // Logika selanjutnya (misalnya, membuat voucher, mengurangi stok hadiah, dll.)
        // bisa ditambahkan di sini.

        return back()->with('success', 'Selamat! Anda berhasil menukarkan ' . $reward->name . '.');
    }
}
