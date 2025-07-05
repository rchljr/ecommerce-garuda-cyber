<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subdomain extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['user_id', 'subdomain_name', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    } // Relasi ke User (Mitra)
    public function orders()
    {
        return $this->hasMany(Order::class);
    } // Subdomain memiliki banyak order
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    } // Subdomain memiliki banyak voucher
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
