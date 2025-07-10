<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subdomain;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;


class CustomTema extends Model
{
    use UsesTenantConnection;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'subdomain_id',
        'shop_name',
        'shop_logo',
        'shop_description',
        'shop_image',
        'primary_color',
        'secondary_color'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = $model->id ?? (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class, 'subdomain_id', 'id');
    }
}
