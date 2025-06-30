<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * ProductTag Model
 *
 * Ini adalah model untuk tabel pivot 'product_tag'.
 * Model ini tidak wajib ada, tetapi sangat berguna jika Anda ingin menambahkan
 * fungsionalitas atau relasi tambahan pada tabel pivot itu sendiri di masa depan.
 */
class ProductTag extends Pivot
{
    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'product_tag';

    // Karena tabel pivot ini tidak memiliki kolom 'created_at' dan 'updated_at'
    // kita set properti ini ke false untuk menonaktifkan fitur timestamp otomatis.
    public $timestamps = false;
}
