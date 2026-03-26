<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Tabel Batch/Periode (Untuk melacak modal per angkatan produksi)
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_code')->unique(); // Contoh: PERIODE-A-2026
            $table->integer('initial_stock');
            $table->integer('current_stock');
            $table->decimal('production_cost_per_unit', 15, 2); // Modal per pcs di periode ini
            $table->json('cost_breakdown'); // Biaya bahan baku, tenaga kerja, dsb (JSON)
            $table->date('started_at');
            $table->date('ended_at')->nullable(); // Kapan stok batch ini habis
            $table->enum('status', ['active', 'depleted', 'archived'])->default('active');
            $table->timestamps();
        });

        // 2. Tabel Logs (Tracking keluar masuk & To-Do Update)
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches');
            $table->enum('type', ['in', 'out', 'production_waste', 'adjustment']);
            $table->integer('quantity');
            $table->string('reason'); // Contoh: "Penjualan Terminal POS", "Restock Periode B"
            $table->json('metadata'); // Data tambahan bebas (JSON)
            $table->foreignId('user_id')->constrained(); // Siapa yang update
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventory_batches');
    }
};
