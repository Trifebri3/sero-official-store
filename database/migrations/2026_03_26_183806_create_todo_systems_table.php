<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kategori To-Do (Master Category)
        Schema::create('todo_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('schema'); // Menyimpan struktur kolom bebas (e.g., Deadline, Status, Level)
            $table->timestamps();
        });

        // 2. Tabel To-Do Items (Isi Tugas)
        Schema::create('todo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_category_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->boolean('is_completed')->default(false);
            $table->json('dynamic_values'); // Menyimpan nilai berdasarkan schema kategori
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_items');
        Schema::dropIfExists('todo_categories');
    }
};
