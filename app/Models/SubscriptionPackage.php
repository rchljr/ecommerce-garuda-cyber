<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'package_name',
        'description',
        'price',
        'discount_month',
        'discount_year',
        'features',
        'is_trial',
        'trial_days',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }
}
