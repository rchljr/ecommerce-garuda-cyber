<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\PendapatanExport;
use App\Http\Controllers\Controller;

class PendapatanController extends Controller
{
    /**
     * Menampilkan halaman daftar pendapatan.
     */
    public function index()
    {
        // Ambil semua data pembayaran dengan relasinya untuk ditampilkan
        $payments = Payment::with(['user.userPackage', 'subscriptionPackage'])
            ->latest()
            ->paginate(10); // Tampilkan 15 data per halaman

        return view('dashboard-admin.kelola-pendapatan', compact('payments'));
    }

    /**
     * Menangani permintaan untuk mengekspor data pendapatan ke Excel.
     */
    public function export(Excel $excel)
    {
        // Generate nama file yang unik berdasarkan tanggal
        $fileName = 'laporan-pendapatan-e-commerce-garuda' . now()->format('d-m-Y') . '.xlsx';

        return $excel->download(new PendapatanExport, $fileName);
    }
}
