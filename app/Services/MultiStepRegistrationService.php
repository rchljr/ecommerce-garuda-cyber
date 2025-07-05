<?php

namespace App\Services;

use Illuminate\Http\Request;

class MultiStepRegistrationService
{
    public function setStepData($step, $data)
    {
        session(['register.step_' . $step => $data]);
    }

    /**
     * Mendefinisikan ulang urutan data sesuai alur baru.
     */
    public function getAllData()
    {
        return [
            'plan'      => session('register.step_0'), // Paket
            'subdomain' => session('register.step_1'), // Subdomain
            'user'      => session('register.step_2'), // Data Diri
            'shop'      => session('register.step_3'), // Data Toko
            'template'  => session('register.step_4'), // Template (terakhir)
        ];
    }

    public function clear()
    {
        session()->forget([
            'register.step_0',
            'register.step_1',
            'register.step_2',
            'register.step_3',
            'register.step_4',
        ]);
    }
}
