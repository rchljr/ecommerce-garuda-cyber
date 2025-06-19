<?php

namespace App\Models;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['user_id', 'voucher_id', 'status', 'order_date', 'total_price'];
    protected $casts = ['order_date' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
