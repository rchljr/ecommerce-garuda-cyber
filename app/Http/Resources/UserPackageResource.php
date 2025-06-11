<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPackageResource extends JsonResource
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
            'user_id' => $this->user_id,
            'subs_package_id' => $this->subs_package_id,
            'active_date' => $this->active_date,
            'expired_date' => $this->expired_date,
            'status' => $this->status,
            'price_paid' => $this->price_paid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
