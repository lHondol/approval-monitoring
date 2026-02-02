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
        Schema::create('area_user', function (Blueprint $table) {
            $table->foreignUuid('area_id')
            ->references('id')
            ->on('areas');
            $table->foreignUuid('user_id')
            ->references('id')
            ->on('users');
            $table->timestamps();

            
            $table->primary(['area_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_user');
    }
};
