<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LandingPage extends Model
{
    use HasUuids;

    protected $table = 'landing_pages';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'total_users',
        'total_shops',
        'total_visitors',
        'total_transactions',
    ];
}
