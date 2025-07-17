<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    // Izinkan mass assignment untuk kolom-kolom ini
    protected $fillable = ['shop_id', 'key', 'value'];

    // Sebuah pengaturan pasti dimiliki oleh satu toko
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}