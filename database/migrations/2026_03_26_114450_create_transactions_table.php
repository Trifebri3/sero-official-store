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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->enum('type', ['offline', 'online'])->default('offline');
            $table->string('channel')->nullable();

            // Kolom JSON
            $table->json('items');
            $table->json('customer_info')->nullable();
            $table->json('metadata')->nullable();
            $table->json('receipt_settings')->nullable();

            // Keuangan
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->string('payment_method')->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Index
            $table->index('created_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
