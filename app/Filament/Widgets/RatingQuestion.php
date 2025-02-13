<?php

namespace App\Filament\Widgets;

use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RatingQuestion extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Rating By Question';

    private array $colors = [
        'rgb(255, 99, 132)',  // Merah muda
        'rgb(54, 162, 235)',  // Biru muda
        'rgb(255, 205, 86)',  // Kuning
        'rgb(75, 192, 192)',  // Hijau muda
        'rgb(153, 102, 255)', // Ungu
        'rgb(255, 159, 64)',  // Oranye
        'rgb(201, 203, 207)', // Abu-abu
        'rgb(255, 99, 71)',   // Tomat
        'rgb(0, 128, 128)',   // Teal
        'rgb(128, 0, 128)'    // Ungu tua
    ];

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        $questions = Question::query()
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end))
            ], 'value')
            ->orderByDesc('survey_answers_avg_value')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Questions',
                    'data' => $questions->pluck('survey_answers_avg_value')->toArray(),
                    'backgroundColor' => $this->colors,
                ]
            ],
            'labels' => $questions->pluck('title')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
