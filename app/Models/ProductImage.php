<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'order',
    ];

    /**
     * Relasi Many-to-One dengan model Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Aksesor: Mendapatkan URL lengkap untuk gambar tambahan.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->path ? asset('storage/' . $this->path) : null;
        // Asumsi path di sini sudah lengkap, misal: 'product_gallery/image1.jpg'
        // Jika Anda ingin subfolder khusus, sesuaikan: asset('storage/product_gallery/' . $this->path)
    }
}
