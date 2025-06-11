<?php

namespace App\Http\Controllers\Auth;

use App\Models\Shop;
use App\Models\User;
use App\Models\Subdomain;
use App\Traits\UploadFile;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SubscriptionPackage;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Services\MultiStepRegistrationService;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    use UploadFile;
    protected $multiStep;

    public function __construct(MultiStepRegistrationService $multiStep)
    {
        $this->multiStep = $multiStep;
    }

    // Tampilkan form register sesuai step
    public function showRegisterForm()
    {
        $step = session('register_step', 0);
        return view('landing-page.auth.register', compact('step'));
    }

    // Step 0: Pilih Paket
    public function registerStep0(Request $request)
    {
        $request->validate([
            'plan_id' => 'required',         // ID paket
            'plan_type' => 'required|in:monthly,yearly', // Tipe harga
        ]);

        $this->multiStep->setStepData(0, [
            'plan_id' => $request->plan_id,
            'plan_type' => $request->plan_type,
        ]);
        // Saat user memilih paket dan tipe harga
        session([
            'register.plan_id' => $request->plan_id, // ID paket
            'register.plan_type' => $request->plan_type, // 'monthly' atau 'yearly'
            'register_step' => 1
        ]);
        return redirect()->route('register.form');
    }

    // Step 1: Pilih Subdomain
    public function registerStep1(Request $request)
    {
        $request->validate(['subdomain' => 'required']);
        $exists = Subdomain::where('subdomain_name', $request->subdomain)->exists();
        if ($exists) {
            return back()->withErrors(['subdomain' => 'Tidak Tersedia']);
        }
        // TODO: Cek ketersediaan subdomain di database
        $this->multiStep->setStepData(1, $request->subdomain);
        session(['register_step' => 2]);
        return redirect()->route('register.form');
    }

    // Step 2: Data Diri
    public function registerStep2(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $data = $request->only(['username', 'position', 'email', 'phone', 'password']);
        $this->multiStep->setStepData(2, $data);
        session(['register_step' => 3]);
        return redirect()->route('register.form');
    }

    // Step 3: Data Toko & Simpan Semua Data ke Database
    public function registerStep3(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'year_founded' => 'nullable|date',
            'shop_address' => 'required',
            'product_categories' => 'required',
            'sku' => 'nullable|string|max:255',
            'shop_photo' => 'required|image',
            'npwp' => 'nullable|file|max:2048', // Maksimal 2MB
            'ktp' => 'required|file|max:2048',
            'nib' => 'nullable|file|max:2048',
            'iumk' => 'nullable|file|max:2048',
        ]);

        $shopData = $request->only(['shop_name', 'year_founded', 'shop_address', 'product_categories']);
        $shopData['shop_photo'] = $this->uploadFile($request->file('shop_photo'), 'shops');
        $shopData['ktp'] = $this->uploadFile($request->file('ktp'), 'ktp');
        // Upload file opsional jika ada
        if ($request->hasFile('sku')) {
            $shopData['sku'] = $this->uploadFile($request->file('sku'), 'sku');
        }
        if ($request->hasFile('npwp')) {
            $shopData['npwp'] = $this->uploadFile($request->file('npwp'), 'npwp');
        }
        if ($request->hasFile('nib')) {
            $shopData['nib'] = $this->uploadFile($request->file('nib'), 'nib');
        }
        if ($request->hasFile('iumk')) {
            $shopData['iumk'] = $this->uploadFile($request->file('iumk'), 'iumk');
        }

        $this->multiStep->setStepData(3, $shopData);

        // Ambil semua data dari session
        $all = $this->multiStep->getAllData();

        // Simpan ke database
        // Buat user baru
        $user = User::create([
            'id' => (string) Str::uuid(),
            'username' => $all['user']['username'],
            'position' => $all['user']['position'],
            'email' => $all['user']['email'],
            'phone' => $all['user']['phone'] ?? null,
            'password' => Hash::make($all['user']['password']),
            'role' => 'calon-mitra',
            'status' => 'pending',
        ]);

        // Simpan paket langganan
        $planId = session('register.plan_id');
        $planType = session('register.plan_type'); // 'monthly' atau 'yearly'

        $package = SubscriptionPackage::findOrFail($planId);

        // Set harga sesuai tipe
        $price_paid = $planType === 'monthly' ? $package->{'monthly-price'} : $package->{'yearly-price'};

        // Status default pending saat pendaftaran, aktif saat diverifikasi admin
        $status = 'pending';

        $user_package = UserPackage::create([
            'user_id' => $user->id,
            'subs_package_id' => $package->id,
            'plan_type' => $planType, // 'monthly' atau 'yearly'
            'active_date' => null,      // null saat pending, isi saat aktif
            'expired_date' => null,
            'status' => $status,
            'price_paid' => $price_paid,
        ]);

        if ($user_package->status === 'active') {
            $active_date = Carbon::now();
            if ($user_package->plan_type === 'monthly-price') {
                $expired_date = $active_date->copy()->addMonth();
            } else {
                $expired_date = $active_date->copy()->addYear();
            }
            $user_package->update([
                'status' => 'active',
                'active_date' => $active_date,
                'expired_date' => $expired_date,
            ]);
        }

        // Simpan subdomain
        $subdomain = Subdomain::create([
            'user_id' => $user->id,
            'subdomain_name' => $all['subdomain'],
            'status' => 'pending',
        ]);

        // Saat user diverifikasi dan status user jadi 'active'
        if ($user->status === 'active') {
            $subdomain->update([
                'status' => 'active',
            ]);
        }
        
        // Simpan data toko
        $shop = Shop::create([
            'user_id' => $user->id,
            'shop_name' => $all['shop']['shop_name'],
            'year_founded' => $all['shop']['year_founded'] ? Carbon::parse($all['shop']['year_founded']) : null,
            'shop_address' => $all['shop']['shop_address'],
            'product_categories' => $all['shop']['product_categories'],
            'sku' => $all['shop']['sku'] ?? null,
            'shop_photo' => $all['shop']['shop_photo'],
            'npwp' => $all['shop']['npwp'] ?? null,
            'ktp' => $all['shop']['ktp'],
            'nib' => $all['shop']['nib'] ?? null,
            'iumk' => $all['shop']['iumk'] ?? null,
        ]);

        $this->multiStep->clear();
        session(['register_step' => 4]);
        return redirect()->route('register.form');
    }

    // Step 4: Status Verifikasi (View Only)
    // Step 5: Pembayaran (View Only, setelah diverifikasi)

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'role' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:30',
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'username' => $validated['username'],
            'position' => $validated['position'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'mitra',
            'status' => $validated['status'] ?? 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ];

        return $this->sendResponse($data, 'Registrasi Toko Anda Berhasil', 201);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();
        }

        $user = Auth::user();

        // dd($user);
        // if (!$user) {
        //     throw ValidationException::withMessages([
        //         'email' => 'Email tidak ditemukan.',
        //     ]);
        // }

        // Cek role admin
        if ($user->role !== 'admin') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Anda tidak memiliki akses ke dashboard admin.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Anda berhasil masuk ke dashboard admin.');


    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
