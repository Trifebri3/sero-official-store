<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Product extends Model
{
    // Gunakan guarded kosong agar bebas input kolom apa saja ke fillable
    protected $guarded = [];

    protected $casts = [
        'specifications' => 'array',    // Key-Value hasil input dynamic
        'marketing_assets' => 'array',  // Key-Value hasil input dynamic
        'production_costs' => 'array',  // Detail biaya produksi
        'media' => 'array',             // Array link foto-foto luxury
        'price' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    /**
     * Relasi balik ke Category untuk ambil template
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Helper untuk ambil value spesifikasi tertentu dengan aman
     */
    public function getSpec($key, $default = '-')
    {
        return $this->specifications[$key] ?? $default;
    }

public function batches() {
    return $this->hasMany(InventoryBatch::class);
}
public function logs() {
    return $this->hasMany(InventoryLog::class);
}
public function inventoryBatches()
{
    return $this->hasMany(InventoryBatch::class);
}
public function inventoryLogs()
{
    return $this->hasMany(InventoryLog::class);
}

/**
 * Melihat sejarah produksi produk ini
 */
public function productionBatches(): HasMany
{
    return $this->hasMany(ProductionBatch::class)->latest();
}

/**
 * Mendapatkan HPP rata-rata dari 3 batch terakhir
 * (Penting untuk menentukan harga jual yang aman)
 */
public function getAverageHppAttribute(): float
{
    return (float) $this->productionBatches()
        ->where('status', 'completed')
        ->take(3)
        ->get()
        ->avg('unit_cost') ?? 0;
}
/**
 * Mendapatkan semua aset marketing/konten terkait produk ini
 */
public function marketingAssets(): HasMany
{
    return $this->hasMany(MarketingContent::class)->with('template');
}

/**
 * Cek apakah produk ini sudah punya konten di platform tertentu
 * Contoh penggunaan: $product->hasContentOn('Shopee')
 */
public function hasContentOn($platform): bool
{
    return $this->marketingAssets()
        ->whereHas('template', fn($q) => $q->where('platform', $platform))
        ->exists();
}


// Di Model Product.php
/**
     * Relasi ke data spreadsheet dinamis
     */
    public function dynamic(): HasOne
    {
        return $this->hasOne(ProductDynamicData::class);
    }

    /**
     * Helper untuk mengambil value spesifik dengan cepat
     * Contoh: $product->getDynamic('Material')
     */
    public function getDynamic(string $key, $default = '—')
    {
        return $this->dynamic->values[$key] ?? $default;
    }
}
