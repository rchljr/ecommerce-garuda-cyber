<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointReward extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'point_rewards';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description', 'points_required', 'image', 'is_active'];
}
