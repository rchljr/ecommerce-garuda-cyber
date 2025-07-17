<?php

namespace App\Models;

use App\Models\User;
use App\Traits\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory, HasUuids, UploadFile;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'shop_name',
        'year_founded',
        'shop_address',
        'postal_code',
        'product_categories',
        'shop_photo',
        'ktp',
        'sku',
        'npwp',
        'nib',
        'iumk',
    ];

    protected $casts = ['year_founded' => 'date'];

    /**
     * 3. Daftarkan model event 'deleting' di sini.
     *
     * Metode ini akan dipanggil secara otomatis oleh Laravel
     * TEPAT SEBELUM sebuah record 'Shop' akan dihapus dari database.
     */
    protected static function booted(): void
    {
        static::deleting(function (Shop $shop) {
            Log::info("Menghapus file untuk Shop ID: {$shop->id}");

            // Panggil method deleteFile dari trait untuk setiap kolom yang berisi path file
            $shop->deleteFile($shop->shop_photo);
            $shop->deleteFile($shop->ktp);
            $shop->deleteFile($shop->sku);
            $shop->deleteFile($shop->npwp);
            $shop->deleteFile($shop->nib);
            $shop->deleteFile($shop->iumk);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_categories', 'slug');
    }
    public function settings()
    {
        return $this->hasMany(ShopSetting::class);
    }
    public function subdomain()
    {
        return $this->hasOne(Subdomain::class);
    }
     public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function heroes()
    {
        return $this->hasMany(Hero::class);
    }
    public function banners()
    {
        return $this->hasMany(Banner::class);
    }
}
