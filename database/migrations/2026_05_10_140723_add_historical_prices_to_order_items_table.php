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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_cost_price', 10, 2)->after('quantity')->default(0);
            $table->decimal('profit_per_unit', 10, 2)->after('unit_price')->default(0);
            $table->decimal('total_profit', 10, 2)->after('profit_per_unit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_cost_price', 'profit_per_unit', 'total_profit']);
        });
    }
};
