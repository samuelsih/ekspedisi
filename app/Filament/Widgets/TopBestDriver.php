<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopBestDriver extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top 5 Driver';

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

        $drivers = Driver::query()
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end))
            ], 'value')
            ->orderByDesc('survey_answers_avg_value')
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

                                result[result.length - 1] += ' (' + value + ')';
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
