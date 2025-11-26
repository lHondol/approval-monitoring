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
            $table->dateTime('created_at');
            $table->string('status')->default('Waiting 1st Approval');
            $table->dateTime('distributed_at')->nullable();
            $table->text('revise_reason')->nullable();
            $table->string('filepath');
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
