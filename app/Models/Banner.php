<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'user_id',
        'title',
        'subtitle',
        'image',
        'link_url',
        'button_text',
        'is_active',
        'order',
    ];

    // PENTING: Tambahkan ini untuk memastikan Laravel mengelola konversi boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Aksesor: Mendapatkan URL lengkap untuk gambar banner.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
