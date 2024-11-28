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
        Schema::table('user_results', function(Blueprint $table)
        {
            $table->renameColumn('question_id', 'attempt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_results', function(Blueprint $table)
        {
            $table->renameColumn('attempt_id', 'question_id');
        });
    }
};
