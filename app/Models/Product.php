<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'sku',
        'main_image',
        'status',
        'is_best_seller', // <-- Ditambahkan
        'is_new_arrival', // <-- Ditambahkan
        'is_hot_sale',    // <-- Ditambahkan
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id ?? (string) Str::uuid();
        });
    }

    /**
     * Relasi dengan user (pemilik produk)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan kategori produk
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi dengan varian produk (ProductVariant)
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi dengan galeri gambar produk
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    /**
     * Relasi dengan tag (many-to-many)
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    /**
     * Aksesor untuk mendapatkan URL gambar utama produk
     */
    public function getImageUrlAttribute(): string // Selalu mengembalikan string
    {
        $path = $this->main_image;

        // Cek jika path ada dan file-nya benar-benar ada di storage
        if ($path && Storage::disk('public')->exists($path)) {
            // PERBAIKAN: Menggunakan helper 'asset' dengan path storage.
            // Ini adalah cara yang lebih umum dan andal.
            return asset('storage/' . $path);
        }

        // Jika gagal, coba path lain (untuk kompatibilitas dengan controller lama)
        $legacyPath = 'product_primary_images/' . $this->main_image;
        if ($this->main_image && Storage::disk('public')->exists($legacyPath)) {
             return asset('storage/' . $legacyPath);
        }

        // Jika semua gagal, kembalikan gambar default
        return asset('images/default-product.png'); // Pastikan Anda punya gambar default ini di /public/images
    }
}
