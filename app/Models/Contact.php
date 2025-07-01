<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
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
