<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTemplate extends Model
{
    protected $fillable = ['category_id', 'schema'];

    // Cast kolom schema otomatis menjadi array agar mudah di-loop di Blade/Livewire
    protected $casts = [
        'schema' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
