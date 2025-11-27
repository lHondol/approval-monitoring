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
            $table->foreignUuid('do_by_user')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
            $table->dateTime('do_at');
            $table->string('status');
            $table->string('reject_reason')->nullable();
            $table->string('filepath')->nullable(); // filepath will created using job
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
