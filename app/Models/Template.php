<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'path',
        'description',
        'image_preview',
    ];

    /**
     * Jika setiap template bisa digunakan oleh banyak tenant.
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
