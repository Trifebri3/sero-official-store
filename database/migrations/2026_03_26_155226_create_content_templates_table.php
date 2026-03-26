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
Schema::create('content_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Contoh: "Instagram Feed", "Shopee Product", "TikTok Reels"
    $table->string('platform'); // IG, Shopee, TikTok, Website

    // Simpan struktur field yang diinginkan dalam JSON
    // Contoh: ["Caption", "Hashtag", "Link Canva", "Format Video"]
    $table->json('fields');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_templates');
    }
};
