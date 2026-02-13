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
        Schema::table('prerelease_so_transactions', function (Blueprint $table) {
            $table->integer('target_shipment_month')->nullable();
            $table->integer('target_shipment_year')->nullable();
            $table->boolean('is_urgent')->nullable();
            $table->boolean('is_margin_confirmed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prerelease_so_transactions', function (Blueprint $table) {
            $table->dropColumn('target_shipment');
            $table->dropColumn('is_urgent');
            $table->dropColumn('is_margin_confirmed');
        });
    }
};
