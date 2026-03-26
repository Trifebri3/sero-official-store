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
Schema::create('production_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('batch_code')->unique(); // Contoh: PROD-2026-001

    // Penjadwalan
    $table->date('start_date');
    $table->date('end_date')->nullable();

    // Progres & Fleksibilitas Aktivitas
    $table->enum('status', ['pending', 'printing', 'cutting', 'finishing', 'completed', 'cancelled'])->default('pending');
    $table->integer('target_quantity');
    $table->integer('actual_quantity')->default(0); // Untuk tracking defect/rusak

    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};
