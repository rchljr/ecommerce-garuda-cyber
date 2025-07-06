<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\SubscriptionPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPackage extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'subs_package_id',
        'plan_type',
        'price_paid',
        'active_date',
        'expired_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subs_package_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'user_id', 'user_id');
    }

}
