<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'voucher_code' => $this->voucher_code,
            'description' => $this->description,
            'discount' => $this->discount,
            'start_date' => $this->start_date,
            'expired_date' => $this->expired_date,
            'min_spending' => $this->min_spending,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
