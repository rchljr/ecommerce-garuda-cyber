<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package_name' => $this->package_name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_month' => $this->discount_month,
            'discount_year' => $this->discount_year,
            'features' => $this->features,
            'is_trial' => $this->is_trial,
            'trial_days' => $this->trial_days,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
