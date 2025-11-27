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
        Schema::create('drawing_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_name');
            $table->string('so_number');
            $table->string('po_number');
            $table->string('status')->default('Waiting 1st Approval');
            $table->timestamp('distributed_at')->nullable();
            $table->text('description')->nullable();
            $table->text('revise_reason')->nullable(); // take from step reject_reason
            $table->string('filepath')->nullable(); // filepath will created using job
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawing_transactions');
    }
};
