<?php

use App\Models\Feature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $features = [
        'customer-survey-decline',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->features as $feature) {
            Feature::query()
                ->create([
                    'name' => $feature,
                    'is_active' => true,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->features as $feature) {
            Feature::query()
                ->where('name', $feature)
                ->delete();
        }
    }
};
