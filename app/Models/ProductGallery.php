<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductGallery extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_path',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Product.
     * Setiap gambar galeri pasti milik satu produk.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor untuk mendapatkan URL lengkap dari gambar galeri.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        // Mengecek apakah image_path ada dan file-nya benar-benar ada di storage
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            // PERBAIKAN: Menggunakan helper 'asset' dengan path storage
            // Ini adalah cara yang lebih umum dan andal, dengan asumsi Anda sudah menjalankan 'php artisan storage:link'
            return asset('storage/' . $this->image_path);
        }

        // Mengembalikan URL gambar default jika tidak ada gambar
        // Ganti 'images/default_product.png' dengan path gambar default Anda di dalam folder /public
        return asset('images/default_product.png'); 
    }
}
