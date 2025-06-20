<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'title',
        'content',
        'text_position',
        'text_color',
        'button_text',
        'button_url',
        'order',
        'is_active',
    ];

    // Jika Anda ingin casting otomatis untuk is_active
    protected $casts = [
        'is_active' => 'boolean',
    ];
}