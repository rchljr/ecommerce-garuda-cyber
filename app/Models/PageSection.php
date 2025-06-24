<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'section_type', 'order', 'is_active', 'content'];

    protected $casts = [
        'content' => 'array', // Otomatis meng-cast kolom 'content' ke array PHP
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}