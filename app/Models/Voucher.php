<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     * Disesuaikan dengan skema database baru Anda.
     */
    protected $fillable = [
        'user_id',
        'voucher_code',
        'description',
        'discount', 
        'min_spending',
        'start_date',
        'expired_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'discount' => 'float',
        'min_spending' => 'float',
        'start_date' => 'date:Y-m-d',
        'expired_date' => 'date:Y-m-d',
    ];
}

