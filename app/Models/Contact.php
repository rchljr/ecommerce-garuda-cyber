<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'phone',
        'email',
        'website',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'pinterest_url',
        'map_embed_code',
        'working_hours',
    ];
}
