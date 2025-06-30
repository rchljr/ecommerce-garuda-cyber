<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Product.
     * Setiap item wishlist milik satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Setiap item wishlist milik satu pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
