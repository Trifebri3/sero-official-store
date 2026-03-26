<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionCost extends Model
{
    protected $fillable = [
        'production_batch_id',
        'category',
        'item_name',
        'amount'
    ];

    /**
     * Relasi balik ke Batch Produksi
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    /**
     * Membantu format mata uang saat dipanggil di Blade
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
