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

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'sub_category_id', // Changed from category_id to sub_category_id
        'name',
        'slug',
        'short_description',
        'description',
        'price',
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
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_hot_sale' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
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
