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
    use HasFactory, HasUuids;

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
        'sku',
        'main_image',
        'gallery_image_paths', 
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
        // HAPUS casting untuk 'price', 'modal_price', 'profit_percentage' dari sini
        // 'price' => 'float',
        // 'modal_price' => 'float',
        // 'profit_percentage' => 'float',
        'gallery_image_paths' => 'array', // TAMBAHKAN casting untuk gallery_image_paths (penting!)
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_hot_sale' => 'boolean',
        'created_at' => 'datetime', // Opsional: jika ingin Carbon object
        'updated_at' => 'datetime', // Opsional: jika ingin Carbon object
    ];
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

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // Ini adalah relasi yang benar ke model Varian (nama sesuai convention Laravel)
    public function varians(): HasMany
    {
        return $this->hasMany(Varian::class, 'product_id', 'id'); // Pastikan ini menunjuk ke model Varian yang benar
    }

    // Relasi ke ProductGallery (jika Anda punya model terpisah untuk gambar galeri)
    public function gallery(): HasMany
    {
        return $this->hasMany(ProductGallery::class, 'product_id'); // Pastikan ini menunjuk ke model Gallery yang benar
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

    /**
     * Accessor untuk mendapatkan URL gambar utama produk.
     */
    public function getImageUrlAttribute(): string
    {
        $path = $this->main_image;
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        $legacyPath = 'product_primary_images/' . $this->main_image; // Contoh path lama
        if ($this->main_image && Storage::disk('public')->exists($legacyPath)) {
            return asset('storage/' . $legacyPath);
        }

        return asset('images/default-product.png'); // Gambar default jika tidak ada gambar
    }

    /**
     * Accessor untuk mendapatkan harga produk yang ditampilkan.
     * Ini akan mengambil harga jual terendah dari semua variannya.
     * Jika tidak ada varian, default ke 0.
     */
    public function getPriceAttribute()
    {
        // Ini akan mengambil harga jual terendah dari varian yang dimuat
        if ($this->relationLoaded('varians') && $this->varians->isNotEmpty()) {
            return $this->varians->min('selling_price');
        }
        return 0.00; // Default jika tidak ada varian atau belum dimuat
    }

    // Jika Anda juga memiliki accessor untuk gallery_image_paths, tambahkan di sini
    public function getGalleryImagesAttribute(): array
    {
        // Jika gallery_image_paths disimpan sebagai JSON array di DB
        if (is_array($this->gallery_image_paths)) {
            return array_map(function ($path) {
                return Storage::url($path);
            }, $this->gallery_image_paths);
        }
        return [];
    }
}
