<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Nonaktifkan auto-increment karena kita mungkin tidak menggunakan integer ID
    public $incrementing = false;
    protected $keyType = 'string'; // Sesuaikan jika Anda menggunakan UUID untuk OrderItem

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',        
    ];

    /**
     * Relasi ke model Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke model Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke model Varian.
     */
    public function variant()
    {
        return $this->belongsTo(Varian::class, 'product_variant_id');
    }
}
