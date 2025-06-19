<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\SubscriptionPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'user_id',
        'order_id',
        'subs_package_id',
        'midtrans_order_id',
        'midtrans_transaction_status',
        'midtrans_payment_type',
        'midtrans_va_number',
        'midtrans_pdf_url',
        'midtrans_response',
        'total_payment',
        'admin_fee'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subs_package_id', 'id');
    }
}
