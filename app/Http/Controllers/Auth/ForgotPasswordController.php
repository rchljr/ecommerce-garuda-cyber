<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani permintaan reset
    | password melalui email dan menyertakan trait sederhana untuk
    | menyertakan perilaku ini. Anda bebas untuk menjelajahi trait ini.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Menampilkan view setelah link reset dikirim.
     * Override method ini untuk menggunakan view kustom Anda.
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', trans($response));
    }
    
    /**
     * Menampilkan view form permintaan link reset.
     * Override method ini untuk menggunakan view kustom Anda.
     */
    public function showLinkRequestForm()
    {
        // Pastikan path view ini benar
        return view('landing-page.auth.passwords.email');
    }
}