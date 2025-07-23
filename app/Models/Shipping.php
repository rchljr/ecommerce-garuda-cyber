<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // PENTING: Tambahkan ini

class Shipping extends Model
{
    use HasUuids; // PENTING: Gunakan HasUuids

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'delivery_service', // ekspedisi pengiriman, jne, jnt, sicepat
        'status', // Status pengiriman (misal: 'delivered')
        'estimated_delivery', // estimasi pengiriman
        'receipt_number', // no resi
        'shipping_cost' // ongkos kirim
    ];

    protected $casts = [
        'estimated_delivery' => 'datetime', // Jika ini tanggal/waktu
        'shipping_cost' => 'decimal:2', // Jika ini biaya
    ];

    // HAPUS BLOK boot() INI karena HasUuids sudah menanganinya
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    // }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}