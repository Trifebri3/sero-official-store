<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model {
    protected $guarded = [];
    protected $casts = [
        'cost_breakdown' => 'array',
        'started_at' => 'date',
    ];

    // Hitung Estimasi Kapan Harus Produksi Ulang
    public function getProductionUrgencyAttribute() {
        $usageRate = $this->initial_stock - $this->current_stock;
        $daysActive = $this->started_at->diffInDays(now()) ?: 1;
        $avgSalesPerDay = $usageRate / $daysActive;

        if ($avgSalesPerDay <= 0) return 'No Movement';

        $daysRemaining = $this->current_stock / $avgSalesPerDay;
        return round($daysRemaining) . " Days Left";
    }

    // Hitung Modal vs Hasil Penjualan (Matching)
    public function calculateProfitMargin($totalSalesInPeriod) {
        $totalCost = $this->initial_stock * $this->production_cost_per_unit;
        return $totalSalesInPeriod - $totalCost;
    }

public function user() {
    return $this->belongsTo(User::class);
}
public function product() {
    return $this->belongsTo(Product::class);
}
}
