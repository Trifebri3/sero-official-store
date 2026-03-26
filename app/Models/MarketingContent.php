<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingContent extends Model
{
    protected $fillable = [
        'product_id',
        'content_template_id',
        'title',
        'status',
        'content_data',
        'scheduled_at'
    ];

    /**
     * Casting sangat penting di sini agar data JSON bisa diakses
     * langsung seperti: $content->content_data['Caption']
     */
    protected $casts = [
        'content_data' => 'array',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Relasi balik ke Produk (Product Embedding)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Template yang digunakan
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContentTemplate::class, 'content_template_id');
    }

    /**
     * SCOPE: Memudahkan filter konten berdasarkan status
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * SCOPE: Memudahkan filter konten berdasarkan Platform via Template
     */
    public function scopeOnPlatform($query, $platform)
    {
        return $query->whereHas('template', function ($q) use ($platform) {
            $q->where('platform', $platform);
        });
    }
}
