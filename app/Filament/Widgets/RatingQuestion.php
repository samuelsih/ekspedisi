<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasExtraJSBar;
use App\Models\Question;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RatingQuestion extends ApexChartWidget
{
    use InteractsWithPageFilters, HasExtraJSBar;

    protected static ?string $heading = 'Rating By Question';

    protected function getOptions(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $questions = Question::query()
            ->select(['title'])
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->whereHas('survey', fn (QueryBuilder $q) => empty($channelId) ? $q : $q->where('channel_id', $channelId))
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
            ], 'value')
            ->orderByDesc('survey_answers_avg_value')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => $questions->pluck('survey_answers_avg_value')->map(fn ($v) => round($v, 2))->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $questions->pluck('title')->toArray(),
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
