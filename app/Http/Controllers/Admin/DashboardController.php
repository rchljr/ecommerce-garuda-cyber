<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Menyiapkan data dan menampilkan halaman dashboard admin.
     */
    public function index()
    {
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // Mitra Baru Bulan Ini (Mitra yang statusnya menjadi 'active' di bulan ini)
        $jumlahMitraBaruBulanIni = User::role('mitra')
            ->where('status', 'active')
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->count();

        // Menghitung jumlah calon mitra yang statusnya pending
        $jumlahMitraPerluVerifikasi = User::role('calon-mitra')
            ->where('status', 'pending')
            ->count();

        // Total Pendapatan Per Bulan (hanya yang lunas)
        $totalPendapatanPerBulan = Payment::whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total_payment');

        // Jumlah Seluruh Akun Mitra (total sepanjang waktu)
        $jumlahSeluruhAkunMitra = User::role('mitra')->count();

        // Total Pendapatan Per Tahun (hanya yang lunas)
        $totalPendapatanPerTahun = Payment::whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->whereYear('created_at', $currentYear)
            ->sum('total_payment');

        // Jumlah Paket Terjual (transaksi lunas di tahun ini)
        $jumlahPaketTerjual = Payment::whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->whereYear('created_at', $currentYear)
            ->count();

        // Data untuk Chart (Paket Terjual per bulan di tahun ini)
        $salesByMonth = Payment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        $chartData = array_fill(1, 12, 0);
        foreach ($salesByMonth as $month => $count) {
            $chartData[$month] = $count;
        }
        $chartData = array_values($chartData);


        return view('dashboard-admin.dashboard', compact(
            'jumlahMitraBaruBulanIni', 
            'jumlahMitraPerluVerifikasi', // Mengirim variabel baru
            'totalPendapatanPerBulan',
            'jumlahSeluruhAkunMitra',
            'totalPendapatanPerTahun',
            'jumlahPaketTerjual',
            'chartData'
        ));
    }
}
