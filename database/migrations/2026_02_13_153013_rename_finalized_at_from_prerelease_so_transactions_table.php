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
            $table->renameColumn('finalized_at', 'released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prerelease_so_transactions', function (Blueprint $table) {
            $table->renameColumn('released_at', 'finalized_at');
        });
    }
};
