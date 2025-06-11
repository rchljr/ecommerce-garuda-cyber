<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['product_id', 'cart_id', 'quantity'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }
}
