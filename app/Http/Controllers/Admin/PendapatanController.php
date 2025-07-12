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
    public function index(Request $request)
    {
        $search = $request->input('search');

        $payments = Payment::with(['user.userPackage', 'subscriptionPackage'])
            ->whereNotNull('subs_package_id')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->orWhereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('subscriptionPackage', function ($subQuery) use ($search) {
                            $subQuery->where('package_name', 'like', "%{$search}%");
                        })
                        ->orWhereDate('payments.created_at', 'like', "%{$search}%");

                    $searchTerm = strtolower($search);
                    if (str_contains($searchTerm, 'tahunan')) {
                        $q->orWhereHas('user.userPackage', function ($subQuery) {
                            $subQuery->where('plan_type', 'yearly');
                        });
                    } elseif (str_contains($searchTerm, 'bulanan')) {
                        $q->orWhereHas('user.userPackage', function ($subQuery) {
                            $subQuery->where('plan_type', 'monthly');
                        });
                    }

                    if (stripos('lunas', $search) !== false) {
                        $q->orWhereIn('midtrans_transaction_status', ['settlement', 'capture']);
                    } elseif (stripos('belum lunas', $search) !== false) {
                        $q->orWhereNotIn('midtrans_transaction_status', ['settlement', 'capture']);
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        // Kirim data ke view, termasuk variabel search
        return view('dashboard-admin.kelola-pendapatan', compact('payments', 'search'));
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
