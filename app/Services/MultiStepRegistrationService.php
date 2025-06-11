<?php

namespace App\Services;

use Illuminate\Http\Request;

class MultiStepRegistrationService
{
    public function setStepData($step, $data)
    {
        session(['register.step_' . $step => $data]);
    }

    public function getAllData()
    {
        return [
            'plan' => session('register.step_0'),
            'subdomain' => session('register.step_1'),
            'user' => session('register.step_2'),
            'shop' => session('register.step_3'),
        ];
    }

    public function clear()
    {
        session()->forget([
            'register.step_0',
            'register.step_1',
            'register.step_2',
            'register.step_3',
        ]);
    }
}
