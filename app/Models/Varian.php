<?php

namespace App\Models;

use App\Models\Product; // Pastikan Product model di-import
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Varian extends Model
{
    // Menggunakan UUID sebagai primary key
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Mendefinisikan field yang dapat diisi (fillable)
    protected $fillable = [
        'product_id',
        'name',
        'description',
        'status', // Contoh: 'active', 'inactive'
        'price',
        'stock',
        // 'size',       // Field baru: Ukuran Varian (misal: 'S', 'M', 'L', 'XL')
        // 'color', 
        'options_data',     
        'image_path', // Field baru: Path gambar varian
    ];

    protected $casts = [
        'options_data' => 'array', // PENTING: Laravel akan otomatis mengonversi JSON ke array/objek PHP
    ];

    /**
     * Metode boot() akan dieksekusi saat model pertama kali di-load.
     * Digunakan untuk otomatis mengisi UUID saat membuat varian baru.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    /**
     * Mendefinisikan relasi Many-to-One dengan model Product.
     * Sebuah Varian dimiliki oleh satu Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Atribut Aksesor: Mendapatkan URL lengkap untuk gambar varian.
     * Ini akan memudahkan Anda menampilkan gambar di tampilan.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
