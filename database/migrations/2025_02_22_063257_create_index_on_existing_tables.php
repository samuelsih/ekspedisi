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
        Schema::table('customers', function (Blueprint $table) {
            $table->index('id_customer');
            $table->index('name');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->index('nik');
            $table->index('name');
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['created_at', 'channel_id']);
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['id_customer']);
            $table->dropIndex(['name']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropIndex(['name']);
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['created_at', 'channel_id']);
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
