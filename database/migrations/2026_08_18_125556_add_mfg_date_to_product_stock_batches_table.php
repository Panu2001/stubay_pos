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
        Schema::table('product_stock_batches', function (Blueprint $table) {
            $table->date('mfg_date')->nullable()->after('cost_price');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->date('new_mfg_date')->nullable()->after('new_cost_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn('new_mfg_date');
        });

        Schema::table('product_stock_batches', function (Blueprint $table) {
            $table->dropColumn('mfg_date');
        });
    }
};
