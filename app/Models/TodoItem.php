<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoItem extends Model
{
    protected $fillable = ['todo_category_id', 'task_name', 'is_completed', 'dynamic_values'];
    protected $casts = ['dynamic_values' => 'array'];

    public function category() {
        return $this->belongsTo(TodoCategory::class, 'todo_category_id');
    }
}
