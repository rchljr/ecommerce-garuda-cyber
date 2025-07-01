<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // PERBAIKAN: Mengubah '->' menjadi '\'
use Illuminate\Database\Eloquent\Relations\BelongsTo; // PERBAIKAN: Mengubah '->' menjadi '\'
use Illuminate\Database\Eloquent\Relations\HasMany; // PERBAIKAN: Mengubah '->' menjadi '\'

use App\Models\ProductOptionValue; // Pastikan ini sudah benar

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'order'];

    /**
     * Relasi Many-to-One dengan model Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi One-to-Many dengan model ProductOptionValue.
     */
    public function optionValues(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class);
    }
}
