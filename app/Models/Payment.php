<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'subs_package_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'midtrans_payment_type',
        'total_payment',
        'midtrans_response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'midtrans_response' => 'array', // Membuat respons Midtrans mudah diakses
    ];
    /**
     * Mendefinisikan bahwa satu Payment dimiliki oleh satu Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mendefinisikan bahwa satu Payment dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan bahwa satu Payment merujuk ke satu SubscriptionPackage.
     * 'subs_package_id' adalah foreign key di tabel 'payments'.
     */
    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subs_package_id');
    }
}
