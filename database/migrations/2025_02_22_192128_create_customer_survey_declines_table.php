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
        Schema::create('customer_survey_declines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained();
            $table->foreignUlid('channel_id')->constrained();
            $table->foreignUlid('driver_id')->constrained();
            $table->string('reason', 101);
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
            $table->index(['created_at', 'channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_survey_declines');
    }
};
