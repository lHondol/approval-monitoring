<?php

use App\Enums\StatusEnum;
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
            $table->string('so_number')->nullable();
            $table->string('po_number');
            $table->string('status');
            $table->timestamp('distributed_at')->nullable();
            $table->text('description')->nullable();
            $table->boolean('as_additional_data')->nullable();
            $table->boolean('additional_data_note')->nullable();
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
        Schema::dropIfExists('drawing_transactions');
    }
};
