<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StorePublicationController extends Controller
{
    /**
     * Mempublikasikan toko oleh mitra yang sedang login.
     */
    public function publish(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            // Validasi penting: Pastikan langganan mitra masih aktif
            if ($user->userPackage?->status !== 'active') {
                return back()->with('error', 'Tidak bisa mempublikasikan toko. Langganan Anda tidak aktif.');
            }
            
            // Perbarui status publikasi subdomain/toko
            if ($user->subdomain) {
                $user->subdomain->update(['publication_status' => 'published']);
                return back()->with('success', 'Selamat! Toko Anda berhasil dipublikasikan dan kini bisa diakses publik.');
            }

            return back()->with('error', 'Data toko tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Mitra Publish Store Exception: ' . $e->getMessage(), ['mitra_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    }

    /**
     * Menyembunyikan (unpublish) toko oleh mitra yang sedang login.
     */
    public function unpublish(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            if ($user->subdomain) {
                $user->subdomain->update(['publication_status' => 'setup_in_progress']);
                return back()->with('success', 'Toko Anda berhasil disembunyikan dan kini tidak bisa diakses publik.');
            }

            return back()->with('error', 'Data toko tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Mitra Unpublish Store Exception: ' . $e->getMessage(), ['mitra_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    }
}