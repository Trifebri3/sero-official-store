<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_code',
        'start_date',
        'end_date',
        'status',
        'target_quantity',
        'actual_quantity',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relasi ke Produk yang diproduksi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke semua rincian biaya (Bahan, Listrik, Packing, dll)
     */
    public function costs(): HasMany
    {
        return $this->hasMany(ProductionCost::class);
    }

    /**
     * ACCESSOR: Total Modal Seluruh Batch
     * Gunakan: $batch->total_cost
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->costs->sum('amount');
    }

    /**
     * ACCESSOR: HPP per unit (Sangat Akurat)
     * Menghitung total modal dibagi jumlah barang yang JADI (bukan target).
     * Gunakan: $batch->unit_cost
     */
    public function getUnitCostAttribute(): float
    {
        if ($this->actual_quantity <= 0) return 0;
        return $this->total_cost / $this->actual_quantity;
    }

    /**
     * SCOPE: Untuk mempermudah filter batch yang sedang berjalan
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['printing', 'cutting', 'finishing']);
    }
}
