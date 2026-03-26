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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        // Kolom Utama (Hard Columns)
        $table->string('sku')->unique();
        $table->string('name');
        $table->string('slug')->unique();
        $table->decimal('price', 15, 2);
        $table->integer('stock')->default(0);
        $table->string('category'); // e.g., 'Living Room', 'Bedroom'

        // The "Magic" JSON Columns
        $table->json('specifications')->nullable(); // Berat, dimensi, material
        $table->json('production_costs')->nullable(); // Bahan, packing, overhead
        $table->json('marketing_assets')->nullable(); // Link Canva, caption, hashtag
        $table->json('media')->nullable(); // Array foto-foto produk premium

        $table->boolean('is_published')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
