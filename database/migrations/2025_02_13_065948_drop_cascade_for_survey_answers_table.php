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
        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropForeign(['survey_id']);
            $table->dropForeign(['question_id']);

            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('question_id')->references('id')->on('questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropForeign(['survey_id']);
            $table->dropForeign(['question_id']);

            $table->foreign('survey_id')->references('id')->on('surveys')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
        });
    }
};
