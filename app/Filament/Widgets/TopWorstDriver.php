<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasExtraJSBar;
use App\Models\Driver;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopWorstDriver extends ApexChartWidget
{
    use HasExtraJSBar, InteractsWithPageFilters;

    protected static ?string $heading = 'Worst 5 Driver (Avg Star Rating Only)';

    protected function getOptions(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $drivers = Driver::query()
            ->select(['name'])
            ->whereHas('surveys', fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
                ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId))
            )
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
            ], 'value')
            ->orderBy('survey_answers_avg_value', 'asc')
            ->limit(5)
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => $drivers->pluck('survey_answers_avg_value')->map(fn ($v) => round($v, 2))->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $drivers->pluck('name')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
