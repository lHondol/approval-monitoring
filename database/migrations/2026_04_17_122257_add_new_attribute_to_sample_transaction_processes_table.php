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
        Schema::table('sample_transaction_processes', function (Blueprint $table) {
            $table->string('start_note')->nullable();
            $table->string('finish_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_transaction_processes', function (Blueprint $table) {
            $table->dropColumn('start_note');
            $table->dropColumn('finish_note');
        });
    }
};
