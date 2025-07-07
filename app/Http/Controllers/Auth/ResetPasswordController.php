<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    */

    use ResetsPasswords;

    /**
     * Ke mana harus mengarahkan pengguna setelah password mereka di-reset.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard'; // Arahkan ke dashboard admin/mitra

    /**
     * Menampilkan view form reset password.
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Pastikan path view ini benar
        return view('landing-page.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
