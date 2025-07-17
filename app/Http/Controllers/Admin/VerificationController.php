<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\OrderService;
use App\Services\VerificationService;
use App\Http\Controllers\BaseController;

class VerificationController extends BaseController
{
    protected $verificationService;
    protected $orderService; 

    public function __construct(VerificationService $verificationService, OrderService $orderService)
    {
        $this->verificationService = $verificationService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        return view('dashboard-admin.verifikasi-mitra', [
            'pendingPartners' => $this->verificationService->getPendingPartners(),
        ]);
    }

    public function approve(User $user)
    {
        $this->verificationService->approvePartner($user);
        $this->orderService->createSubscriptionOrder($user); 
        return back()->with('success', 'Mitra berhasil disetujui. Notifikasi pembayaran telah dikirim.');
    }

    public function reject(User $user)
    {
        $this->verificationService->rejectPartner($user);
        return back()->with('success', 'Pengajuan mitra berhasil ditolak.');
    }
}