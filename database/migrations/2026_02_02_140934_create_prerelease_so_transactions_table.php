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
        Schema::create('prerelease_so_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')
            ->references('id')
            ->on('customers')
            ->cascadeOnDelete();
            $table->foreignUuid('area_id')
            ->references('id')
            ->on('areas')
            ->cascadeOnDelete();
            $table->string('so_number');
            $table->string('po_number');
            $table->string('status');
            $table->timestamp('finalized_at')->nullable();
            $table->text('description')->nullable();
            $table->boolean('done_revised')->nullable();
            $table->boolean('as_additional_data')->nullable();
            $table->text('additional_data_note')->nullable();
            $table->boolean('as_revision_data')->nullable();
            $table->text('revision_data_note')->nullable();
            $table->text('revised_note')->nullable();
            $table->text('need_revise_note')->nullable();
            $table->string('filepath')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prerelease_so_transactions');
    }
};
