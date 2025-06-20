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

    public function subscriptionPackage(): BelongsTo
    {
        // 'subs_package_id' adalah foreign key di tabel ini.
        // 'id' adalah primary key di tabel subscription_packages.
        return $this->belongsTo(SubscriptionPackage::class, 'subs_package_id', 'id');
    }

    /**
     * Relasi ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
