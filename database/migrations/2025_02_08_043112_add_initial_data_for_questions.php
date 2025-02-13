<?php

use App\Enum\QuestionCategory;
use App\Models\Question;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $supirData = [
        'Sopir datang dengan keadaan rapi (memakai seragam dan sepatu tertutup) !',
        'Sopir tidak meminta atau menerima imbalan dalam bentuk apapun dari customer',
    ];

    private array $tokoData = [
        'Sopir memberikan senyum, salam, dan sapa',
        'Sopir mengirim jumlah barang sesuai dengan Faktur pengiriman',
        'Sopir memberikan barang dengan kualitas yang baik dan tidak rusak',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->supirData as $title) {
            Question::query()->create([
                'title' => $title,
                'is_active' => true,
                'category' => QuestionCategory::SUPIR,
            ]);
        }

        foreach ($this->tokoData as $title) {
            Question::query()->create([
                'title' => $title,
                'is_active' => true,
                'category' => QuestionCategory::TOKO,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tokoData as $title) {
            Question::query()->where('title', $title)->delete();
        }

        foreach ($this->supirData as $title) {
            Question::query()->where('title', $title)->delete();
        }
    }
};
