<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_adjustment_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'remaining_quantity']);
            $table->index('expiry_date');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->decimal('new_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('new_cost_price', 10, 2)->nullable()->after('new_price');
            $table->date('new_expiry_date')->nullable()->after('new_cost_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_stock_batch_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_stock_batch_id');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['new_price', 'new_cost_price', 'new_expiry_date']);
        });

        Schema::dropIfExists('product_stock_batches');
    }
};
