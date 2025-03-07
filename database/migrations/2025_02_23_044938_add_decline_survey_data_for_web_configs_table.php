<?php

use App\Models\WebConfig;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $data = [
        'Sub judul halaman penolakan survey' => 'Form Anti Survey Ekspedisi JTA',
        'Judul halaman penolakan survey' => 'Anti Survey',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->data as $name => $value) {
            WebConfig::query()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->data as $name => $value) {
            WebConfig::query()->where('name', $name)->where('value', $value)->delete();
        }
    }
};
