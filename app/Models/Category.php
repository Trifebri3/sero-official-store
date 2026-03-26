<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;



class Category extends Model
{
    protected $fillable = [
        'name',
        'default_specs',    // Simpan: ["Material", "Dimensi", "Finishing"]
        'default_marketing' // Simpan: ["Target IG", "Canva Link"]
    ];

    protected $casts = [
        'default_specs' => 'array',
        'default_marketing' => 'array',
    ];

    /**
     * Relasi ke Product
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


    public function template(): HasOne
    {
        return $this->hasOne(CategoryTemplate::class);
    }
}
