<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('category_templates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->unique()->constrained()->onDelete('cascade');
    // Menyimpan struktur kolom dalam JSON, contoh: [{"name": "Material", "type": "text"}, {"name": "Weight", "type": "number"}]
    $table->json('schema');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_templates');
    }
};
