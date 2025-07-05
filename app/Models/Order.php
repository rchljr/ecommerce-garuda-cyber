<?php

namespace App\Models;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Order extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['user_id', 'subdomain_id', 'voucher_id', 'status', 'order_date', 'total_price'];
    protected $casts = ['order_date' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userPackage(): HasOneThrough
    {
        return $this->hasOneThrough(
            UserPackage::class, // Model tujuan
            User::class,        // Model perantara
            'id',               // Foreign key di tabel User
            'user_id',          // Foreign key di tabel UserPackage
            'user_id',          // Local key di tabel Order
            'id'                // Local key di tabel User
        );
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    } // Toko tempat order dibuat
    public function items()
    {
        return $this->hasMany(ProductOrder::class);
    }
}
