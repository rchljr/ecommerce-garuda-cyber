<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCart extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'product_carts';
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'user_id', 'id');
    }
    public function variant()
    {
        // Menggunakan model 'Varian' dan foreign key 'product_variant_id'
        return $this->belongsTo(Varian::class, 'product_variant_id');
    }
}
