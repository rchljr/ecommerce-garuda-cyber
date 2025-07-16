<?php

namespace App\Http\Controllers\Auth;

use Throwable;
use App\Models\User;
use App\Models\Order;
use App\Models\Template;
use App\Models\Subdomain;
use App\Traits\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\RegistrationService;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
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
     * Menampilkan form registrasi dengan alur baru.
     */
    public function showRegisterForm(Request $request)
    {
        $step = $request->query('step', session('register_step', 0));
        $isBusinessPlan = false;
        // Cek jika ada pengguna yang sudah terdaftar dan menunggu verifikasi di session.
        // Ini untuk pengguna yang baru saja menyelesaikan form.
        $userIdFromSession = session('newly_registered_user_id');
        if ($userIdFromSession) {
            $user = User::with('userPackage.subscriptionPackage')->find($userIdFromSession); // Eager load relasi

            // Pemeriksaan aman untuk mencegah error 'property on null'
            if ($user && $user->userPackage && $user->userPackage->subscriptionPackage) {
                $isBusinessPlan = $user->userPackage->subscriptionPackage->package_name !== 'Starter Plan';
                $verifStep = $isBusinessPlan ? 5 : 4;

                // Paksa pengguna untuk tetap di halaman verifikasi
                if ($step != $verifStep) {
                    return redirect()->route('register.form', ['step' => $verifStep]);
                }
            } else {
                // Jika data user tidak lengkap karena suatu hal, kembalikan ke awal untuk mencegah error.
                $this->clearRegistration();
                return redirect()->route('register.form', ['step' => 0])->with('error', 'Terjadi kesalahan pada sesi Anda. Silakan mulai lagi.');
            }
        } else {
            // Jika belum ada user yang terdaftar, gunakan data dari session.
            // Pastikan data dari step sebelumnya ada untuk mencegah error.
            if ($step > 0 && !session()->has('register.step_0')) {
                return redirect()->route('register.form', ['step' => 0])->with('error', 'Silakan pilih paket terlebih dahulu.');
            }

            $planData = session('register.step_0');
            if ($planData) {
                $package = SubscriptionPackage::find($planData['plan_id']);
                if ($package && $package->package_name !== 'Starter Plan') {
                    $isBusinessPlan = true;
                }
            }
        }

        // Jika pengguna kembali ke step 0, bersihkan session untuk memulai dari awal.
        if ($step == '0' || !$request->has('step')) {
            // Pengecualian: jangan clear jika user sedang menunggu verifikasi
            if (!$userIdFromSession) {
                $this->multiStep->clear();
                session()->forget(['newly_registered_user_id', 'register_step']);
            }
        }

        $step = $request->query('step', session('register_step', 0));
        session(['register_step' => $step]);

        // Cek apakah paket Business yang dipilih dari session
        $isBusinessPlan = false;
        $planData = session('register.step_0');
        if ($planData) {
            $package = SubscriptionPackage::find($planData['plan_id']);
            if ($package && $package->package_name !== 'Starter Plan') {
                $isBusinessPlan = true;
            }
        }

        // Definisikan progress bar secara dinamis
        $steps = [0 => 'Pilih Paket', 1 => 'Alamat Toko', 2 => 'Data Diri', 3 => 'Data Toko'];
        if ($isBusinessPlan) {
            $steps[4] = 'Pilih Template';
            $steps[5] = 'Verifikasi';
        } else {
            $steps[4] = 'Verifikasi';
        }

        // Logika Tombol Kembali
        $previousStepUrl = null;
        if ($step >= 1 && $step <= ($isBusinessPlan ? 4 : 3)) {
            $previousStepUrl = route('register.form', ['step' => $step - 1]);
        }

        $data = [
            'step' => $step,
            'steps' => $steps,
            'isBusinessPlan' => $isBusinessPlan,
            'previousStepUrl' => $previousStepUrl,
        ];

        // Memuat data yang diperlukan untuk setiap step
        if ($step == 0) {
            $packages = $this->subscriptionPackageService->getAllPackages();
            $data['packages'] = $packages->sortBy(fn($p) => $p->is_trial ? 0 : ($p->monthly_price === null ? 2 : 1));
        }
        if ($step == 3) {
            $data['categories'] = $this->categoryService->getAllCategories();
        }
        if ($step == 4 && $isBusinessPlan) {
            $data['templates'] = Template::all();
            $shopData = session('register.step_3');
            $data['recommendedCategorySlug'] = $shopData['product_categories'] ?? null;
        }

        // Untuk halaman verifikasi (step 4 untuk Starter, 5 untuk Business)
        $verificationStepForBusiness = 5;
        $verificationStepForStarter = 4;
        if ($step == $verificationStepForBusiness || ($step == $verificationStepForStarter && !$isBusinessPlan)) {
            $userId = session('newly_registered_user_id'); // Ambil ID dari session
            if ($userId) {
                $data['statusUser'] = User::find($userId);
            }
        }

        return view('landing-page.auth.register', $data);
    }

    // Step 0: Pilih Paket
    public function submitPackage(Request $request)
    {
        $request->validate(['plan' => 'required', 'billing_period' => 'required|in:monthly,yearly']);
        $this->multiStep->setStepData(0, ['plan_id' => $request->plan, 'plan_type' => $request->billing_period]);
        return redirect()->route('register.form', ['step' => 1]);
    }
    // Step 1: Pilih Subdomain
    public function submitSubdomain(Request $request)
    {
        // Validasi bahwa subdomain yang dipilih ada di session
        $request->validate(['chosen_subdomain' => 'required|string']);

        $subdomainToSave = session('register.subdomain_normalized');

        // Pastikan subdomain yang akan disimpan sama dengan yang ada di form
        if ($request->chosen_subdomain !== $subdomainToSave) {
            return back()->with('error', 'Terjadi kesalahan. Silakan cek ulang subdomain Anda.');
        }

        $this->multiStep->setStepData(1, ['subdomain' => $subdomainToSave]);
        session(['register_step' => 2]);
        return redirect()->route('register.form', ['step' => 2]);
    }

    /**
     * Method untuk menangani pengecekan ketersediaan via AJAX.
     */
    public function checkSubdomain(Request $request)
    {
        $originalInput = $request->input('subdomain');
        $normalizedSubdomain = Str::slug(substr($originalInput, 0, 40));

        $validator = Validator::make(['subdomain' => $normalizedSubdomain], [
            'subdomain' => 'required|string|max:40|alpha_dash|unique:subdomains,subdomain_name',
        ], [
            'subdomain.unique' => 'Subdomain ini sudah digunakan. Coba nama lain.'
        ]);

        if ($validator->fails()) {
            // Jika subdomain sudah terpakai, generate beberapa saran
            $suggestions = $this->generateSubdomainSuggestions($normalizedSubdomain);

            return response()->json([
                'available' => false,
                // 'message' => $validator->errors()->first('subdomain'),
                'suggestions' => $suggestions // Kirim saran ke frontend
            ]);
        }

        // Jika validasi lolos, berarti subdomain tersedia
        session([
            'register.original_subdomain_input' => $originalInput,
            'register.subdomain_normalized' => $normalizedSubdomain,
        ]);

        return response()->json(['available' => true]);
    }

    private function generateSubdomainSuggestions(string $baseName, int $limit = 3): array
    {
        // Batasi panjang nama dasar untuk menjaga URL tetap pendek
        $baseName = Str::limit(Str::slug($baseName), 20, '');
        $suggestions = [];
        $attempts = 0;

        // Coba hingga 20 kali untuk menemukan jumlah saran yang diinginkan
        while (count($suggestions) < $limit && $attempts < 20) {
            // Buat saran baru dengan angka acak
            $newSuggestion = $baseName . '-' . rand(100, 999);

            // Pastikan saran ini belum ada di dalam list sementara
            if (!in_array($newSuggestion, $suggestions)) {
                $suggestions[] = $newSuggestion;
            }
            $attempts++;
        }

        // Jika tidak ada saran yang bisa dibuat, kembalikan array kosong
        if (empty($suggestions)) {
            return [];
        }

        // Cek ke database untuk melihat mana saja yang sudah ada
        $existing = Subdomain::whereIn('subdomain_name', $suggestions)->pluck('subdomain_name')->toArray();

        // Ambil hanya saran yang belum ada di database menggunakan array_diff
        $availableSuggestions = array_diff($suggestions, $existing);

        // Kembalikan jumlah saran sesuai limit
        return array_slice(array_values($availableSuggestions), 0, $limit);
    }

    // Step 2: Data Diri
    public function submitUser(Request $request)
    {
        $phone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . ltrim($phone, '0');
        }
        $request->merge(['phone' => $phone]);
        $request->validate(['name' => 'required|string|max:255', 'position' => 'nullable|string|max:255', 'email' => 'required|string|email|max:255|unique:users,email', 'phone' => ['nullable', 'string', 'max:30', 'regex:/^62[0-9]{8,15}$/'], 'password' => 'required|string|min:8|confirmed',]);
        $data = $request->only(['name', 'position', 'email', 'password']);
        $data['phone'] = $phone;
        $this->multiStep->setStepData(2, $data);
        return redirect()->route('register.form', ['step' => 3]);
    }

    // Step 3: Data Toko 
    public function submitShop(Request $request)
    {
        $validatedShop = $request->validate(['shop_name' => 'required|string|max:255', 'year_founded' => 'nullable|date', 'shop_address' => 'required|string', 'postal_code' => 'required|string|digits:5','product_categories' => 'required|string', 'shop_photo' => 'required|image|max:2048', 'ktp' => 'required|image|max:2048', 'sku' => 'nullable|file|max:2048', 'npwp' => 'nullable|file|max:2048', 'nib' => 'nullable|file|max:2048', 'iumk' => 'nullable|file|max:2048',]);
        foreach (['shop_photo', 'ktp', 'sku', 'nib', 'npwp', 'iumk'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $validatedShop[$fileKey] = $this->uploadFile($request->file($fileKey), $fileKey);
            }
        }
        $this->multiStep->setStepData(3, $validatedShop);
        $planData = session('register.step_0');
        $package = SubscriptionPackage::find($planData['plan_id']);

        if ($package->package_name !== 'Starter Plan') {
            return redirect()->route('register.form', ['step' => 4]); // Lanjut ke pilih template
        }

        // Untuk Starter Plan, set template default dan langsung proses registrasi
        $defaultTemplate = Template::where('path', 'template1')->first();
        $this->multiStep->setStepData(4, ['template_id' => $defaultTemplate->id]);

        try {
            $user = $this->registrationService->processRegistration();
            session(['newly_registered_user_id' => $user->id]); // Simpan ID user untuk halaman verifikasi
            Notification::send(User::role('admin')->get(), new NewPartnerRegistration($user));

            // Arahkan ke halaman verifikasi untuk Starter Plan (step 4)
            return redirect()->route('register.form', ['step' => 4]);
        } catch (\Exception $e) {
            $this->multiStep->clear();
            return redirect()->route('register.form', ['step' => 0])->with('error', 'Sesi Anda berakhir atau data tidak lengkap. Silakan coba lagi.');
        }
    }
    // Step 4: Pilih Template (Hanya untuk Business Plan)
    public function submitTemplate(Request $request)
    {
        $request->validate(['template_id' => 'required|exists:templates,id']);
        $this->multiStep->setStepData(4, $request->only('template_id'));

        try {
            $user = $this->registrationService->processRegistration();
            session(['newly_registered_user_id' => $user->id]); // Simpan ID user untuk halaman verifikasi
            Notification::send(User::role('admin')->get(), new NewPartnerRegistration($user));

            // Arahkan ke halaman verifikasi untuk Business Plan (step 5)
            return redirect()->route('register.form', ['step' => 5]);
        } catch (\Exception $e) {
            $this->multiStep->clear();
            return redirect()->route('register.form', ['step' => 0])->with('error', 'Sesi Anda berakhir atau data tidak lengkap. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menangani proses login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required'],]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')->with('success', 'Anda berhasil masuk sebagai admin.');
            }
            if ($user->hasRole('mitra')) {
                return redirect()->route('mitra.dashboard')->with('success', 'Anda berhasil masuk ke Dashboard Mitra.');
            }

            // --- LOGIKA KUNCI ---
            // Jika user adalah 'calon-mitra' dan statusnya 'active' (artinya disetujui admin)
            if ($user->hasRole('calon-mitra') && $user->status === 'active') {
                $hasPendingOrder = Order::where('user_id', $user->id)->where('status', 'pending')->exists();
                // Jika punya order pending, arahkan ke halaman pembayaran khusus.
                if ($hasPendingOrder) {
                    return redirect()->route('payment.show');
                }
                // Jika tidak, mungkin sudah bayar, arahkan ke dashboard.
                return redirect()->route('mitra.dashboard');
            }

            // Jika statusnya masih pending, logout dan beri pesan.
            if ($user->status === 'pending') {
                Auth::guard('web')->logout();
                return back()->withErrors(['email' => 'Akun Anda sedang menunggu verifikasi admin.'])->withInput();
            }

            // Jika tidak cocok dengan role manapun, logout.
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Akun ini tidak memiliki akses yang sesuai.'])->withInput();
        }

        // Handle jika kredensial salah
        return back()->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])->withInput();
    }

    /**
     * Method baru untuk membersihkan session dan memulai registrasi baru.
     */
    public function clearRegistration()
    {
        $this->multiStep->clear();
        session()->forget(['register_step', 'newly_registered_user_id']);
        return redirect()->route('register.form', ['step' => 0])->with('success', 'Anda dapat memulai pendaftaran baru.');
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

