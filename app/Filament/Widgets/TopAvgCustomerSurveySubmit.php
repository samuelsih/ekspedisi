<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TopAvgCustomerSurveySubmit extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Highest Average Customer Survey Answers';

    private array $colors = [
        'rgb(255, 99, 132)',  // Merah muda
        'rgb(54, 162, 235)',  // Biru muda
        'rgb(255, 205, 86)',  // Kuning
        'rgb(75, 192, 192)',  // Hijau muda
        'rgb(153, 102, 255)', // Ungu
    ];

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
