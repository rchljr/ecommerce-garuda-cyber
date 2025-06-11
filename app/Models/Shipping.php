<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'order_id',
        'delivery_service',
        'status',
        'estimated_delivery',
        'receipt_number',
        'shipping_cost'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
