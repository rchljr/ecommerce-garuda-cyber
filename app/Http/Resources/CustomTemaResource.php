<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomTemaResource extends JsonResource
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
            'subdomain_id' => $this->subdomain_id,
            'shop_name' => $this->shop_name,
            'shop_logo' => $this->shop_logo,
            'shop_description' => $this->shop_description,
            'shop_image' => $this->shop_image,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
