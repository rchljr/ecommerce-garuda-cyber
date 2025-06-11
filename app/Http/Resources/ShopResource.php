<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'shop_name' => $this->shop_name,
            'year_founded' => $this->year_founded,
            'shop_address' => $this->shop_address,
            'product_categories' => $this->product_categories,
            'sku' => $this->sku,
            'shop_photo' => $this->shop_photo,
            'npwp' => $this->npwp,
            'ktp' => $this->ktp,
            'nib' => $this->nib,
            'iumk' => $this->iumk,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
