<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class CustomBanner extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['user_id', 'banner_image', 'status'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
