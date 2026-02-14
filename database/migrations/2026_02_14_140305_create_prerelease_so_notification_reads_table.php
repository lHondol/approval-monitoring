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
        Schema::create('prerelease_so_notification_reads', function (Blueprint $table) {
            $table->foreignUuid('prerelease_so_transaction_id')
            ->constrained('prerelease_so_transactions', 'id', 'pst_notification_read_fk')
            ->cascadeOnDelete();
            $table->foreignUuid('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
            $table->dateTime('read_at');
            $table->timestamps();

            
            $table->primary(['prerelease_so_transaction_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prerelease_so_notification_reads');
    }
};
