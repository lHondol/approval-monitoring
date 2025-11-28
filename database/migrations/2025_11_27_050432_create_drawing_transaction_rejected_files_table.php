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
        Schema::create('drawing_transaction_rejected_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('drawing_transaction_id')
                ->constrained('drawing_transactions', 'id', 'dt_rejected_dt_fk')
                ->cascadeOnDelete();
            $table->foreignUuid('drawing_transaction_step_id')
                ->constrained('drawing_transaction_steps', 'id', 'dt_rejected_dts_fk')
                ->cascadeOnDelete();
            $table->string('filepath');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawing_transaction_images');
    }
};
