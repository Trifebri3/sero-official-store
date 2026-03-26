<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
        'customer_info' => 'array',
        'metadata' => 'array',
        'receipt_settings' => 'array',
        'created_at' => 'datetime',
    ];

    // Scope untuk Laporan (Performance Tracking)
    public function scopeDaily($query) {
        return $query->whereDate('created_at', today());
    }

    public function scopeMonthly($query) {
        return $query->whereMonth('created_at', now()->month);
    }
}
