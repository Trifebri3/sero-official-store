<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('business_settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // contoh: 'revenue_goal_2026'
        $table->decimal('target_amount', 15, 2);
        $table->decimal('avg_price', 15, 2);
        $table->decimal('avg_cost', 15, 2);
        $table->json('allocations'); // Disini kita simpan [ {"name": "Gaji", "pct": 20}, ... ]
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
