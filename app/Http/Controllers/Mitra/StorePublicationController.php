<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StorePublicationController extends Controller
{
    /**
     * Mengubah status subdomain menjadi 'publish' (dipublikasikan).
     */
    public function publish()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subdomain = $user->shop->subdomain;

        try {
            // Validasi penting: Pastikan langganan mitra masih aktif
            if ($user->userPackage?->status !== 'active') {
                return back()->with('error', 'Tidak bisa mempublikasikan toko. Langganan Anda tidak aktif.');
            }

            if ($subdomain) {
                $subdomain->update(['publication_status' => 'published']);
                return back()->with('success', 'Selamat! Toko Anda berhasil dipublikasikan dan kini bisa diakses publik.');
            }

            return back()->with('error', 'Data subdomain toko tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Mitra Publish Store Exception: ' . $e->getMessage(), ['user_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    }

    /**
     * Mengubah status subdomain menjadi 'pending' (disembunyikan).
     */
    public function unpublish()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subdomain = $user->shop->subdomain;

        try {
            if ($subdomain) {
                $subdomain->update(['publication_status' => 'pending']);
                return back()->with('success', 'Toko Anda berhasil disembunyikan dan kini tidak bisa diakses publik.');
            }

            return back()->with('error', 'Data subdomain toko tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Mitra Unpublish Store Exception: ' . $e->getMessage(), ['user_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    }
}
