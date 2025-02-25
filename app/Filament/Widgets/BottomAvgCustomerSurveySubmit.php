<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasExtraJSBar;
use App\Models\Survey;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BottomAvgCustomerSurveySubmit extends ApexChartWidget
{
    use HasExtraJSBar, InteractsWithPageFilters;

    protected static ?string $heading = 'Bottom Avg Customer Survey Answers';

    protected function getOptions(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $surveys = Survey::query()
            ->select(['customer_id'])
            ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
            ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId))
            ->with('customer:id,name')
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
            ], 'value')
            ->limit(10)
            ->orderBy('survey_answers_avg_value', 'asc')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => $surveys->pluck('survey_answers_avg_value')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $surveys->map(fn ($survey) => "{$survey->customer->name}")->toArray(),
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
