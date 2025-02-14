<?php

namespace App\Filament\Widgets;

use App\Models\Question;
use Filament\Support\RawJs;
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
        'rgb(128, 0, 128)',    // Ungu tua
    ];

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        $questions = Question::query()
            ->select(['title'])
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
            ], 'value')
            ->orderByDesc('survey_answers_avg_value')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Questions',
                    'data' => $questions->pluck('survey_answers_avg_value')->toArray(),
                    'backgroundColor' => $this->colors,
                ],
            ],
            'labels' => $questions->pluck('title')->toArray(),
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const text = tooltipItem.label;
                                const value = tooltipItem.raw;
                                const limit = 20;

                                let result = [];

                                for(let i = 0; i < text.length; i += limit) {
                                    result.push(text.substring(i, i + limit));
                                }

                                function formatNumber(number) {
                                    let rounded = Number(number.toFixed(3));
                                    return Number.isInteger(rounded) ? rounded : rounded;
                                }

                                result[result.length - 1] += ' (' + formatNumber(value) + ')';
                                return result;
                            }
                        }
                    },

                    legend: {
                        display: true,
                        labels: {
                            generateLabels: function (chart) {
                                const limit = 5;
                                const data = chart.data;
                                if(data.labels.length && data.datasets.length) {
                                    return data.labels.map(function(label, i) {
                                        const formattedLabel = label.length > limit ? label.slice(0, limit) + '...' : label;
                                        return {
                                            text: formattedLabel,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                        }
                                    })
                                }

                            }
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
