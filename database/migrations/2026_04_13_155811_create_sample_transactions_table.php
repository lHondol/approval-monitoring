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
        Schema::create('sample_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('so_number');
            $table->foreignUuid('customer_id')
            ->references('id')
            ->on('customers')
            ->cascadeOnDelete();
            $table->dateTime('so_created_at');
            $table->dateTime('shipment_request');
            $table->dateTime('picture_received_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_transactions');
    }
};
