<?php

use App\Models\Feature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $feature = 'survey-face-detection';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Feature::query()
            ->create([
                'name' => $this->feature,
                'is_active' => false,
            ]);

        Schema::table('surveys', function (Blueprint $table) {
            $table->boolean('face_detected')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Feature::query()
            ->where('name', $this->feature)
            ->delete();

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('face_detected');
        });
    }
};
