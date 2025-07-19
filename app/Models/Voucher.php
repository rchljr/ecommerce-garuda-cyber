<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     * Disesuaikan dengan skema database baru Anda.
     */
    protected $fillable = [
        'user_id',
        'subdomain_id', // Relasi ke subdomain utk voucher mitra
        'voucher_code',
        'description',
        'min_spending',
        'start_date',
        'expired_date',
        'discount',
        'max_uses',
        'max_uses_per_customer',
        'is_for_new_customer',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'discount' => 'float',
        'min_spending' => 'float',
        'start_date' => 'date:Y-m-d',
        'expired_date' => 'date:Y-m-d',
    ];

    /**
     * Definisikan Accessor & Mutator untuk voucher_code.
     */
    protected function voucherCode(): Attribute
    {
        return Attribute::make(
            // Accessor: Otomatis mengubah ke uppercase saat data diambil
            get: fn($value) => strtoupper($value),
            // Mutator: Otomatis mengubah ke lowercase sebelum data disimpan ke database
            set: fn($value) => strtolower($value),
        );
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }
    public function products()
    {
        // Mendefinisikan relasi many-to-many ke model Product
        // melalui tabel pivot 'product_voucher'.
        return $this->belongsToMany(Product::class, 'product_voucher', 'voucher_id', 'product_id');
    }
}

