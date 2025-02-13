<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopAvgCustomerSurveySubmit extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top 5 Highest Average Customer Survey Answers';

    protected int | string | array $columnSpan = 'full';

    private array $colors = [
        'rgb(255, 99, 132)',  // Merah muda
        'rgb(54, 162, 235)',  // Biru muda
        'rgb(255, 205, 86)',  // Kuning
        'rgb(75, 192, 192)',  // Hijau muda
        'rgb(153, 102, 255)', // Ungu
    ];

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        $customers = Customer::query()
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end))
            ], 'value')
            ->limit(10)
            ->orderByDesc('survey_answers_avg_value')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Customer',
                    'data' => $customers->pluck('survey_answers_avg_value')->toArray(),
                ],
            ],
            'labels' => $customers->map(fn ($customer) => "{$customer->name}")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
