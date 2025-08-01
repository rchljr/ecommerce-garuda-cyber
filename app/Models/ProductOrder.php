<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOrder extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'unit_price',
        'product_variant_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Mendefinisikan relasi ke ProductVariant.
     * Satu item pesanan (ProductOrder) memiliki satu varian (ProductVariant).
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
