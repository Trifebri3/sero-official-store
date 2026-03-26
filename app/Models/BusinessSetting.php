<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessSetting extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional jika nama file sudah plural sesuai aturan Laravel)
     */
    protected $table = 'business_settings';

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'key',
        'target_amount',
        'avg_price',
        'avg_cost',
        'allocations',
    ];

    /**
     * Casting otomatis: JSON di database jadi Array di PHP
     */
    protected $casts = [
        'allocations' => 'array',
        'target_amount' => 'decimal:2',
        'avg_price' => 'decimal:2',
        'avg_cost' => 'decimal:2',
    ];
}
