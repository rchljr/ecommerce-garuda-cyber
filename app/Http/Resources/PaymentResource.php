<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'subs_package_id' => $this->subs_package_id,
            'midtrans_order_id' => $this->midtrans_order_id,
            'midtrans_transaction_status' => $this->midtrans_transaction_status,
            'midtrans_payment_type' => $this->midtrans_payment_type,
            'midtrans_va_number' => $this->midtrans_va_number,
            'midtrans_pdf_url' => $this->midtrans_pdf_url,
            'midtrans_response' => $this->midtrans_response,
            'total_payment' => $this->total_payment,
            'admin_fee' => $this->admin_fee,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
