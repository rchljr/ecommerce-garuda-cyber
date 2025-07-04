<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'subdomain',
        'template_id',
        'user_id',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }
}
