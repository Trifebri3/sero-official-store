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
Schema::create('marketing_contents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('content_template_id')->constrained();

    $table->string('title'); // Judul Konten/Campaign
    $table->enum('status', ['draft', 'scheduled', 'posted'])->default('draft');

    // DATA BEBAS/RANDOM DISIMPAN DI SINI
    // Apapun yang kamu input di template, masuk ke sini dalam bentuk Key-Value
    $table->json('content_data');

    $table->timestamp('scheduled_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_contents');
    }
};
