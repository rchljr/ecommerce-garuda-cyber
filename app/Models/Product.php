<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ProductOption;
use App\Models\ActualVarian;
use App\Models\ProductImage; 
class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false; // Menggunakan UUID
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'product_discount',
        'status',
        'rating_product', 
        'image', 
        'slug', 
    ];

    /**
     * Metode boot() akan dieksekusi saat model pertama kali di-load.
     * Digunakan untuk otomatis mengisi UUID saat membuat produk baru.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    /**
     * Relasi Many-to-One dengan model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi Many-to-One dengan model Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Relasi One-to-Many dengan model ProductOption.
     */
    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }

    /**
     * Relasi One-to-Many dengan model ActualVarian.
     */
    public function actualVarians(): HasMany
    {
        return $this->hasMany(ActualVarian::class, 'product_id', 'id');
    }

    /**
     * Relasi One-to-Many dengan model ProductImage (untuk gambar tambahan).
     */
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    /**
     * Aksesor: Mendapatkan URL lengkap untuk gambar utama produk.
     * Mengubah dari getThumbnailUrlAttribute ke getImageUrlAttribute.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/product_primary_images/' . $this->image) : null;
        // Catatan: Saya mengubah folder penyimpanan dari 'thumbnails' menjadi 'product_primary_images'
        // untuk membedakannya dari gambar galeri produk. Anda bisa sesuaikan.
    }
}
