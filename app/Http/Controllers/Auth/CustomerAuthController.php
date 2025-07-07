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
    public function showLoginForm(Request $request)
    {
        $tenant = $request->get('tenant');
        return view('customer.auth.login', compact('tenant'));
    }

    /**
     * Menangani proses login untuk customer.
     */
    public function login(Request $request, CartService $cartService)
    {
        $tenant = $request->get('tenant');
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customers')->attempt($credentials)) {
            $user = Auth::guard('customers')->user();

            if ($user && $user->hasRole('customer')) {
                $request->session()->regenerate();
                $cartService->mergeSessionCart();
                // Arahkan kembali ke halaman utama tenant
                return redirect()->route('tenant.home', ['subdomain' => $tenant->subdomain->subdomain_name]);
            }
            Auth::guard('customers')->logout();
            return back()->withErrors(['email' => 'Akun Anda belum terdaftar sebagai customer.'])->withInput();
        }
        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    /**
     * Menampilkan halaman registrasi untuk customer.
     */
    public function showRegisterForm(Request $request)
    {
        $tenant = $request->get('tenant');
        return view('customer.auth.register', compact('tenant'));
    }

    /**
     * Menangani proses registrasi untuk customer baru.
     */
    public function register(Request $request)
    {
        $tenant = $request->get('tenant');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);
        $user->assignRole('customer');
        Auth::guard('customers')->login($user);

        // Arahkan kembali ke halaman utama tenant
        return redirect()->route('tenant.home', ['subdomain' => $tenant->subdomain->subdomain_name]);
    }

    /**
     * Menangani proses logout untuk customer.
     */
    public function logout(Request $request)
    {
        $tenant = $request->get('tenant');

        Auth::guard('customers')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login tenant
        return redirect()->route('tenant.home', ['subdomain' => $tenant->subdomain->subdomain_name]);
    }
}
