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
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: "Sofa Luxury"
        $table->json('default_specs')->nullable(); // Isi: ["Material", "Dimensi", "Garansi"]
        $table->json('default_marketing')->nullable(); // Isi: ["Canva Link", "IG Caption"]
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
