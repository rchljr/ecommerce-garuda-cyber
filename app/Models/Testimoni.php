<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimoni extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'testimonis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'content',
        'rating',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi bahwa testimoni ini milik seorang User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
