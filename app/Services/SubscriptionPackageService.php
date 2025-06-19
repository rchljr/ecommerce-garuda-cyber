<?php

namespace App\Services;

use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\DB;

class SubscriptionPackageService
{
    public function getAllPackages()
    {
        return SubscriptionPackage::with('features')->get();
    }

    public function getPackageById($id)
    {
        return SubscriptionPackage::with('features')->findOrFail($id);
    }

    private function preparePackageData(array &$data)
    {
        // Pastikan discount_year adalah 0 jika tidak diisi (null)
        if (!isset($data['discount_year']) || is_null($data['discount_year'])) {
            $data['discount_year'] = 0;
        }

        // Handle nullable prices
        if (empty($data['monthly_price'])) {
            $data['monthly_price'] = null;
            $data['yearly_price'] = null;
        } else {
            // Calculate yearly price based on monthly price and discount
            $monthlyPrice = $data['monthly_price'];
            $discountPercentage = $data['discount_year']; // Ambil nilai yang sudah pasti ada

            $yearlyPriceBeforeDiscount = $monthlyPrice * 12;
            $discountAmount = ($yearlyPriceBeforeDiscount * $discountPercentage) / 100;

            $data['yearly_price'] = $yearlyPriceBeforeDiscount - $discountAmount;
        }

        // Ensure trial days is null if not a trial package
        if (empty($data['is_trial'])) {
            $data['trial_days'] = null;
        }

        // Filter out any empty feature strings
        if (isset($data['features'])) {
            $data['features'] = array_filter($data['features'], function ($value) {
                return !is_null($value) && $value !== '';
            });
        }
    }

    public function createPackage(array $data)
    {
        $this->preparePackageData($data);

        return DB::transaction(function () use ($data) {
            $package = SubscriptionPackage::create($data);

            if (!empty($data['features'])) {
                foreach ($data['features'] as $featureText) {
                    $package->features()->create(['feature' => $featureText]);
                }
            }
            return $package;
        });
    }

    public function updatePackage($id, array $data)
    {
        $this->preparePackageData($data);

        return DB::transaction(function () use ($id, $data) {
            $package = SubscriptionPackage::findOrFail($id);
            $package->update($data);

            $package->features()->delete();
            if (!empty($data['features'])) {
                foreach ($data['features'] as $featureText) {
                    $package->features()->create(['feature' => $featureText]);
                }
            }
            return $package;
        });
    }

    public function deletePackage($id)
    {
        $package = SubscriptionPackage::findOrFail($id);
        return $package->delete();
    }
}
