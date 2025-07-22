<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Import HasUuids

class Product extends Model
{
    use HasFactory, HasUuids; // Tambahkan HasUuids di sini

    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'shop_id',
        'sub_category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'modal_price',
        'profit_percentage',
        'sku',
        'main_image',
        'status',
        'is_best_seller',
        'is_new_arrival',
        'is_hot_sale',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'modal_price' => 'float', // Tambahkan casting untuk modal_price
        'profit_percentage' => 'float', // Tambahkan casting untuk profit_percentage
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_hot_sale' => 'boolean',
    ];

    // HAPUS ATAU KOMENTARI BLOK boot() INI
    // protected static function boot()
    // {
    //     parent::boot();
    //
    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) Str::uuid();
    //         }
    //     });
    // }

    public function getSellingPriceAttribute()
    {
        if ($this->modal_price !== null && $this->profit_percentage !== null) {
            return $this->modal_price * (1 + ($this->profit_percentage / 100));
        }
        return $this->price;
    }

    public function setModalPriceAttribute($value)
    {
        $this->attributes['modal_price'] = $value;
        // Pastikan profit_percentage sudah ada sebelum menghitung price
        if (isset($this->attributes['profit_percentage']) && $this->attributes['profit_percentage'] !== null) {
            $this->attributes['price'] = $value * (1 + ($this->attributes['profit_percentage'] / 100));
        }
    }

    public function setProfitPercentageAttribute($value)
    {
        $this->attributes['profit_percentage'] = $value;
        // Pastikan modal_price sudah ada sebelum menghitung price
        if (isset($this->attributes['modal_price']) && $this->attributes['modal_price'] !== null) {
            $this->attributes['price'] = $this->attributes['modal_price'] * (1 + ($value / 100));
        }
    }

    // --- QUERY SCOPES ---

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function scopeBestSellers(Builder $query): void
    {
        $query->where('is_best_seller', true);
    }

    public function scopeNewArrivals(Builder $query): void
    {
        $query->where('is_new_arrival', true);
    }

    public function scopeHotSales(Builder $query): void
    {
        $query->where('is_hot_sale', true);
    }

    // --- RELATIONS ---

    public function shopOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop(): BelongsTo // Tambahkan relasi shop jika belum ada
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function varians()
    {
        return $this->hasMany(Varian::class);
    }

    /**
     * Get the tags for the product.
     */

    // --- ACCESSORS ---

    public function getImageUrlAttribute(): string
    {
        $path = $this->main_image;
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        $legacyPath = 'product_primary_images/' . $this->main_image;
        if ($this->main_image && Storage::disk('public')->exists($legacyPath)) {
            return asset('storage/' . $legacyPath);
        }

        return asset('images/default-product.png');
    }
}
