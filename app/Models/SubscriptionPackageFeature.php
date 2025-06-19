<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageFeature extends Model
{
    use HasUuids;

    protected $table = 'subscription_package_features';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'subscription_package_id',
        'feature',
    ];

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id', 'id');
    }
}
