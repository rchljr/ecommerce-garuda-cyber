<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    /**
     * Menampilkan halaman login untuk customer.
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Menangani proses login untuk customer.
     */
    public function login(Request $request, CartService $cartService)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek role menggunakan metode Spatie setelah login berhasil
            if ($user && $user->hasRole('customer')) {
                $request->session()->regenerate();

                // Panggil service untuk menggabungkan keranjang
                $cartService->mergeSessionCart();

                return redirect()->route('customer.profile'); // Arahkan ke profil customer
            }

            // Jika role bukan customer, logout dan beri pesan error
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun ini tidak memiliki akses sebagai customer.',
            ])->withInput();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    /**
     * Menampilkan halaman registrasi untuk customer.
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Menangani proses registrasi untuk customer baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active', // Customer langsung aktif
        ]);

        $user->assignRole('customer');

        // Langsung login setelah registrasi berhasil
        Auth::login($user);

        return redirect()->route('customer.profile');
    }

    /**
     * Menangani proses logout untuk customer.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login customer
        return redirect()->route('customer.login.form');
    }
}
