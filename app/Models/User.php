<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'position',
        'status',
        'photo',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id', 'id');
    }
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
    public function subdomain()
    {
        return $this->hasOne(Subdomain::class);
    }
    public function userPackage()
    {
        return $this->hasOne(UserPackage::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }
    /**
     * Mendefinisikan bahwa satu User memiliki satu Cart.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
     public function vouchers()
    {
        return $this->hasMany(Voucher::class);

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'user_id');
    }

    /**
     * Relasi untuk mengambil voucher yang sedang aktif saja.
     */
    public function activeVouchers()
    {
        return $this->hasMany(Voucher::class, 'user_id')
                    ->where('start_date', '<=', now())
                    ->where('expired_date', '>=', now())
                    ->orderBy('discount', 'desc'); // Urutkan dari diskon terbesar
    }
}
