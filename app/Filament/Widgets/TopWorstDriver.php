<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Widgets\ChartWidget;

class TopWorstDriver extends ChartWidget
{
    protected static ?string $heading = 'Worst 5 Driver';

    private array $colors = [
        'rgb(255, 99, 132)',  // Merah muda
        'rgb(54, 162, 235)',  // Biru muda
        'rgb(255, 205, 86)',  // Kuning
        'rgb(75, 192, 192)',  // Hijau muda
        'rgb(153, 102, 255)', // Ungu
    ];

    protected function getData(): array
    {
        $drivers = Driver::query()
            ->withAvg('survey_answers', 'value')
            ->orderBy('survey_answers_avg_value', 'asc')
            ->limit(5)
            ->get();

    return [
        'datasets' => [
            [
                'label' => 'Questions',
                'data' => $drivers->pluck('survey_answers_avg_value')->toArray(),
                'backgroundColor' => $this->colors,
            ]
        ],
        'labels' => $drivers->pluck('name')->toArray(),
    ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
