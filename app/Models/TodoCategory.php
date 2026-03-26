<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoCategory extends Model
{
    protected $fillable = ['name', 'slug', 'schema'];
    protected $casts = ['schema' => 'array'];

    public function items() {
        return $this->hasMany(TodoItem::class);
    }
}
