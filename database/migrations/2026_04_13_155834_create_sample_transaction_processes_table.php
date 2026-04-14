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
        Schema::create('sample_transaction_processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sample_transaction_id')
            ->references('id')
            ->on('sample_transactions')
            ->cascadeOnDelete();
            $table->string('process_name');
            $table->dateTime('start_at');
            $table->dateTime('finish_at');
            $table->string('filepath');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_transaction_processes');
    }
};
