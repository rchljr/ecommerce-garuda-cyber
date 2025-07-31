<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // TAMBAHKAN INI
use Illuminate\Support\Facades\Storage; // TAMBAHKAN INI untuk accessor gambar

class Varian extends Model
{
    use HasFactory, HasUuids; // TAMBAHKAN HasFactory dan HasUuids di sini

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
        'price', // Ini adalah kolom untuk harga jual yang sudah dihitung
        'stock',
        'options_data',
        'modal_price', // BARU: Tambahkan ke fillable
        'profit_percentage', // BARU: Tambahkan ke fillable
        'image_path', // Field: Path gambar varian
    ];

    protected $casts = [
        'options_data' => 'array', // PENTING: Laravel akan otomatis mengonversi JSON ke array/objek PHP
        'modal_price' => 'decimal:2', // BARU: Casting untuk modal_price
        'profit_percentage' => 'decimal:2', // BARU: Casting untuk profit_percentage
        'price' => 'decimal:2', // Pastikan harga jual juga di-cast
    ];

    // HAPUS BLOK boot() INI karena HasUuids sudah menanganinya secara otomatis
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    // }

    /**
     * Mendefinisikan relasi Many-to-One dengan model Product.
     * Sebuah Varian dimiliki oleh satu Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Accessor: Mendapatkan harga jual varian yang dihitung.
     */
    public function getSellingPriceAttribute()
    {
        // Pastikan modal_price dan profit_percentage adalah numerik sebelum perhitungan
        $modalPrice = $this->modal_price;
        $profitPercentage = $this->profit_percentage;

        // Lakukan pengecekan ketat untuk memastikan keduanya adalah angka valid
        if (is_numeric($modalPrice) && is_numeric($profitPercentage)) {
            return (float) $modalPrice * (1 + ((float) $profitPercentage / 100));
        }

        // Fallback: Jika perhitungan tidak memungkinkan, gunakan kolom 'price' langsung
        // Atau kembalikan 0 jika 'price' juga bisa null/tidak valid
        return (float) $this->price ?? 0.00;
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