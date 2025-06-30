<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActualVarian extends Model
{
    use HasFactory;

    // Menentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'product_id',
        'name', // Contoh: "Medium / Black"
        'price',
        'stock',
        'image', // Path gambar varian
        'options_data', // JSON: {"size": "Medium", "color": "Black"}
    ];

    // Kolom yang harus di-cast ke tipe data tertentu
    // 'options_data' akan diubah menjadi array PHP secara otomatis
    protected $casts = [
        'options_data' => 'array',
    ];

    /**
     * Mendefinisikan relasi Many-to-One dengan model Product.
     * Sebuah ActualVarian dimiliki oleh satu Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Mendapatkan URL lengkap untuk gambar varian.
     * Ini akan memudahkan Anda menampilkan gambar di tampilan.
     */
    public function getImageUrlAttribute(): ?string
    {
        // Pastikan 'image' berisi path yang relatif ke 'storage/'
        // Misalnya: 'variant_images/nama_gambar.jpg'
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
