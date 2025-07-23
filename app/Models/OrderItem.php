<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id', // Pastikan ini ada di $fillable
        'quantity',
        'price', // Ini adalah harga jual saat order dibuat
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // BARU: Relasi ke Varian
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Varian::class, 'product_variant_id');
    }

    // Jika Anda ingin accessor untuk total harga per item, contoh:
    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->price;
    }
}