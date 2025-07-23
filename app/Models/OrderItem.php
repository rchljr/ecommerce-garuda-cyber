<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Ini sudah benar

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
    public function order(): BelongsTo // IDE mungkin memperingatkan di sini
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Product
    public function product(): BelongsTo // IDE mungkin memperingatkan di sini
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Varian
    public function variant(): BelongsTo // IDE mungkin memperingatkan di sini
    {
        return $this->belongsTo(Varian::class, 'product_variant_id');
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->price;
    }
}