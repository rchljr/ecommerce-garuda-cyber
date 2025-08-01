<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    // Tambahkan status baru
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUND_REQUESTED = 'refund_requested';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'shop_id',
        'order_group_id',
        'subdomain_id',
        'status',
        'total_price',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'voucher_id',
        'order_date',
        'delivery_method',
        'shipping_address',
        'shipping_city',
        'shipping_zip_code',
        'shipping_phone',
        'notes'
    ];
    protected $casts = ['order_date' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'order_id', 'id');
    }
    public function testimonials()
    {
        return $this->hasMany(Testimoni::class, 'order_id', 'id');
    }
    public function refundRequest()
    {
        return $this->hasOne(RefundRequest::class);
    }
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    /**
     * Mendefinisikan relasi hasOneThrough ke UserPackage melalui User.
     */
    public function userPackage()
    {
        return $this->hasOneThrough(
            UserPackage::class,
            User::class,
            'id', // Foreign key di tabel users
            'user_id', // Foreign key di tabel user_packages
            'user_id', // Local key di tabel orders
            'id' // Local key di tabel users
        );
    }
}
