<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    // use UsesLandlordConnection;
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'subdomain_id',
        'template_id',
        'user_id',
        'db_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }
    public function getDatabaseName(): string
    {
        return $this->db_name;
    }
}
