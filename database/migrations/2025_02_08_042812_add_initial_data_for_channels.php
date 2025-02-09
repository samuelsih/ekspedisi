<?php

use App\Models\Channel;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $names = [
        "MT",
        "GT"
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->names as $name) {
            Channel::query()->create([
                'name' => $name,
            ]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->names as $name) {
            Channel::query()->where('name', $name)->delete();
        }
    }
};
