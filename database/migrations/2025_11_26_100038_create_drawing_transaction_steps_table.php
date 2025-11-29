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
        Schema::create('drawing_transaction_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('drawing_transaction_id')
            ->references('id')
            ->on('drawing_transactions')
            ->cascadeOnDelete();
            $table->foreignUuid('done_by_user')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
            $table->dateTime('done_at');
            $table->string('action_done');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawing_transaction_steps');
    }
};
