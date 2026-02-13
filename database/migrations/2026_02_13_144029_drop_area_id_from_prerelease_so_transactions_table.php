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
            $table->dropForeign(["area_id"]);
            $table->dropColumn("area_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prerelease_so_transactions', function (Blueprint $table) {
            $table->foreignUuid('area_id')
            ->references('id')
            ->on('areas')
            ->cascadeOnDelete();
        });
    }
};
