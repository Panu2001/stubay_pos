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
        Schema::create('price_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('old_stock_cost_from', 10, 2)->nullable();
            $table->decimal('old_stock_cost_to', 10, 2)->nullable();
            $table->decimal('old_stock_price_from', 10, 2)->nullable();
            $table->decimal('old_stock_price_to', 10, 2)->nullable();
            
            $table->decimal('new_stock_cost_from', 10, 2)->nullable();
            $table->decimal('new_stock_cost_to', 10, 2)->nullable();
            $table->decimal('new_stock_price_from', 10, 2)->nullable();
            $table->decimal('new_stock_price_to', 10, 2)->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_adjustments');
    }
};
