<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_transaction_processes', function (Blueprint $table) {
            $table->dropColumn('start_filepath');
            $table->renameColumn('finish_filepath', 'filepath');
        });
    }

    public function down(): void
    {
        Schema::table('sample_transaction_processes', function (Blueprint $table) {
            $table->renameColumn('filepath', 'finish_filepath');
            $table->string('start_filepath')->nullable();
        });
    }
};