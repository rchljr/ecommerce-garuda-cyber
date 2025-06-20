<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasUuids;

    protected $table = 'subscription_packages';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'package_name',
        'description',
        'monthly_price',
        'yearly_price',
        'discount_year',
        'is_trial',
        'trial_days',
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'monthly_price' => 'integer',
        'yearly_price' => 'float',
        'discount_year' => 'integer',
        'trial_days' => 'integer',
    ];

    public function features()
    {
        return $this->hasMany(SubscriptionPackageFeature::class, 'subscription_package_id', 'id');
    }
}
