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
        Schema::table('drivers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
