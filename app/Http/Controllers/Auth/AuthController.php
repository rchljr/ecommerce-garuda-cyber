<?php

namespace App\Http\Controllers\Auth;

use Throwable;
use App\Models\User;
use App\Models\Order;
use App\Models\Subdomain;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\RegistrationService;
use App\Http\Controllers\BaseController;
use App\Services\SubscriptionPackageService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewPartnerRegistration;
use App\Services\MultiStepRegistrationService;

class AuthController extends BaseController
{
    use UploadFile;
    protected $multiStep;
    protected $registrationService;
    protected $subscriptionPackageService;
    protected $categoryService;

    public function __construct(MultiStepRegistrationService $multiStep, RegistrationService $registrationService, SubscriptionPackageService $subscriptionPackageService, CategoryService $categoryService)
    {
        $this->multiStep = $multiStep;
        $this->registrationService = $registrationService;
        $this->subscriptionPackageService = $subscriptionPackageService;
        $this->categoryService = $categoryService;
    }

    /**
     * Menampilkan form registrasi dan menangani pengalihan ke pembayaran.
     */
    public function showRegisterForm(Request $request)
    {
        $step = $request->query('step', session('register_step', 0));
        session(['register_step' => $step]);
        $data = ['step' => $step];

        if ($step == 0) {
            $packages = $this->subscriptionPackageService->getAllPackages();
            $sortedPackages = $packages->sortBy(function ($package) {
                if ($package->is_trial)
                    return 0;
                if (is_null($package->monthly_price))
                    return 2;
                return 1;
            });
            $data['packages'] = $sortedPackages;
        }
        if ($step == 3) {
            $data['categories'] = $this->categoryService->getAllCategories();
        }
        if ($step == 4) {
            $userId = session('newly_registered_user_id');
            // Pastikan ada user id di session, lalu ambil data user
            if ($userId) {
                // Kirim objek user ke view dengan nama variabel 'statusUser'
                $data['statusUser'] = User::find($userId);
            }
        }
        if ($step == 5) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login untuk melanjutkan.');
            }
            $data['order'] = Order::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->latest()
                ->first();
        }

        return view('landing-page.auth.register', $data);
    }


    // Step 0: Pilih Paket
    public function registerStep0(Request $request)
    {
        $request->validate([
            'plan' => 'required',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $this->multiStep->setStepData(0, [
            'plan_id' => $request->plan,
            'plan_type' => $request->billing_period,
        ]);

        session(['register_step' => 1]);
        return redirect()->route('register.form', ['step' => 1]);
    }

    // Step 1: Pilih Subdomain
    public function registerStep1(Request $request)
    {
        if ($request->has('choose_subdomain')) {
            $subdomain = session('register.subdomain_normalized');
            $this->multiStep->setStepData(1, ['subdomain' => $subdomain]);
            session(['register_step' => 2]);
            return redirect()->route('register.form', ['step' => 2]);
        }

        $originalInput = $request->input('subdomain');
        $normalizedSubdomain = strtolower(str_replace(' ', '-', trim($originalInput)));
        $request->merge(['subdomain_for_validation' => $normalizedSubdomain]);
        $request->validate(['subdomain_for_validation' => 'required|string|max:63|alpha_dash']);

        $exists = Subdomain::where('subdomain_name', $normalizedSubdomain)->exists();

        session([
            'register.original_subdomain_input' => $originalInput,
            'register.subdomain_normalized' => $normalizedSubdomain,
            'register.subdomain_status' => $exists ? 'Tidak Tersedia' : 'Tersedia',
            'register_step' => 1
        ]);

        return redirect()->route('register.form', ['step' => 1]);
    }

    // Step 2: Data Diri
    public function registerStep2(Request $request)
    {
        $phone = $request->input('phone');
        if ($phone) {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (!str_starts_with($phone, '62')) {
                $phone = '62' . ltrim($phone, '0');
            }
        }
        $request->merge(['phone' => $phone]);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^62[0-9]{8,15}$/'],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'position', 'email', 'password']);
        $data['phone'] = $phone;

        $this->multiStep->setStepData(2, $data);
        return redirect()->route('register.form', ['step' => 3]);
    }

    // Step 3: Data Toko & Simpan Semua Data ke Database
    public function registerStep3(Request $request)
    {
        $validatedShop = $request->validate([
            'shop_name' => 'required|string|max:255',
            'year_founded' => 'nullable|date',
            'shop_address' => 'required|string',
            'product_categories' => 'required|string',
            'shop_photo' => 'required|image|max:2048',
            'ktp' => 'required|image|max:2048',
            'sku' => 'nullable|file|max:2048',
            'npwp' => 'nullable|file|max:2048',
            'nib' => 'nullable|file|max:2048',
            'iumk' => 'nullable|file|max:2048',
        ]);

        foreach (['shop_photo', 'ktp', 'sku', 'nib', 'npwp', 'iumk'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $validatedShop[$fileKey] = $this->uploadFile($request->file($fileKey), $fileKey);
            }
        }

        $this->multiStep->setStepData(3, $validatedShop);

        try {
            $user = $this->registrationService->processRegistration();

            session(['newly_registered_user_id' => $user->id]);
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewPartnerRegistration($user));
            }
            session(['register_step' => 4]);
            return redirect()->route('register.form', ['step' => 4]);
        } catch (\Exception $e) {
            $this->multiStep->clear();
            return redirect()->route('register.form', ['step' => 0])
                ->with('error', 'Sesi Anda telah berakhir. Silakan ulangi proses pendaftaran dari awal.');
        }
    }

    /**
     * Menangani proses login pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi input dasar
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Tambahkan syarat 'status' => 'active' ke dalam kredensial untuk percobaan login
        $credentialsWithStatus = array_merge($credentials, ['status' => 'active']);

        // 3. Coba login dengan kredensial lengkap (email, password, DAN status active)
        if (Auth::attempt($credentialsWithStatus)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')->with('success', 'Anda berhasil masuk sebagai admin.');
            }
            if ($user->hasRole('mitra')) {
                return redirect()->route('mitra.dashboard');
            }
            if ($user->hasRole('calon-mitra')) {
                // Kasus ini menangani 'calon-mitra' yang sudah disetujui (status: active) tapi belum bayar.
                $hasPendingOrder = Order::where('user_id', $user->id)->where('status', 'pending')->exists();
                return $hasPendingOrder
                    ? redirect()->route('register.form', ['step' => 5])
                    : redirect()->route('mitra.dashboard');
            }
            if ($user->hasRole('customer')) {
                return redirect()->route('customer.profile');
            }

            return redirect()->route('landing');
        }

        // 4. Jika login gagal, cari tahu penyebabnya untuk memberikan pesan error yang lebih baik.
        $user = User::where('email', $credentials['email'])->first();

        // Cek apakah user ada dan passwordnya benar, tapi statusnya salah
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->status === 'pending') {
                return back()->withErrors(['email' => 'Akun Anda sedang menunggu verifikasi admin.'])->withInput();
            }
            if ($user->status === 'inactive') {
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.'])->withInput();
            }
        }

        // Jika sampai di sini, berarti email atau passwordnya yang salah.
        return back()->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}

