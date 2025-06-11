<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageResource extends JsonResource
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
            'total_users' => $this->total_users,
            'total_shops' => $this->total_shops,
            'total_visitors' => $this->total_visitors,
            'total_transactions' => $this->total_transactions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
