<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Traits\UploadFile; // Import trait upload file

class CustomerProfileController extends Controller
{
    use UploadFile; // Gunakan trait di dalam controller

    /**
     * Menampilkan halaman profil pengguna.
     */
    public function show()
    {
        // Ambil data pengguna yang sedang login
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $maxBirthDate = now()->subYears(10)->format('Y-m-d');

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik, tapi abaikan email user saat ini
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $maxBirthDate],
            'jenis_kelamin' => ['nullable', 'string', Rule::in(['pria', 'wanita'])],
            'alamat' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk foto profil
            // Password hanya divalidasi jika diisi
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'tanggal_lahir.before_or_equal' => 'Anda harus berusia minimal 10 tahun untuk menggunakan layanan ini.',
            'tanggal_lahir.date_format' => 'Format tanggal lahir tidak valid.',
        ]);

        // Siapkan data untuk diupdate
        $dataToUpdate = $request->only(['name', 'email', 'phone', 'tanggal_lahir', 'jenis_kelamin', 'alamat']);

        // Handle upload foto profil jika ada file baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo) {
                $this->deleteFile($user->photo);
            }
            // Upload foto baru dan simpan path-nya
            $dataToUpdate['photo'] = $this->uploadFile($request->file('photo'), 'avatars');
        }

        // Handle update password jika diisi
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // Update data pengguna
        $user->update($dataToUpdate);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
