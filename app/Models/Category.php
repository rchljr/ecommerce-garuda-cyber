<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class Category extends Model
{

    use HasFactory, HasUuids;
    use UsesLandlordConnection;
    protected $connection = 'mysql';

    protected $table = 'categories';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'slug'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
}
