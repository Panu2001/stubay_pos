<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });

        Schema::table('lends', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });

        Schema::table('lends', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });
    }
};
