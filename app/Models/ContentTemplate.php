<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentTemplate extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'fields'
    ];

    /**
     * Casting 'fields' menjadi array.
     * Jadi saat kamu simpan ['Caption', 'Link'], Laravel otomatis mengubahnya ke JSON.
     */
    protected $casts = [
        'fields' => 'array',
    ];

    /**
     * Relasi ke semua konten yang menggunakan template ini.
     */
    public function marketingContents(): HasMany
    {
        return $this->hasMany(MarketingContent::class);
    }
}
