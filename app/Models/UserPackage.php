<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\SubscriptionPackage;
use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'subs_package_id',
        'plan_type', // 'monthly' atau 'yearly
        'active_date',
        'expired_date',
        'status',
        'price_paid'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subs_package_id', 'id');
    }
}
