<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDynamicData extends Model
{
    // Nama tabel disesuaikan dengan migrasi sebelumnya
    protected $table = 'product_dynamic_data';

    protected $fillable = ['product_id', 'values'];

    // Cast values menjadi array agar bisa dipanggil seperti: $data->values['Warna']
    protected $casts = [
        'values' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
