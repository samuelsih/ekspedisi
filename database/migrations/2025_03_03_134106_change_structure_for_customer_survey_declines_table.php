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
        Schema::table('customer_survey_declines', function (Blueprint $table) {
            $table->foreignUlid('customer_survey_decline_answer_id')->constrained();
            $table->dropColumn('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_survey_declines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_survey_decline_answer_id');
            $table->string('reason', 101)->nullable();
        });
    }
};
