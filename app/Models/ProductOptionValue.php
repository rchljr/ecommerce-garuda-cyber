<?php

namespace App\Models; // Namespace ini harus App\Models

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Nama kelas ini HARUS sama dengan nama file: ProductOptionValue
class ProductOptionValue extends Model
{
    use HasFactory;

    protected $fillable = ['product_option_id', 'value', 'order'];

    /**
     * Get the product option that owns the value.
     */
    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }
}
