<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class BottomAvgCustomerSurveySubmit extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Bottom Avg Customer Survey Answers';

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
            ->select(['name'])
            ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
            ], 'value')
            ->limit(10)
            ->orderBy('survey_answers_avg_value', 'asc')
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

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    x: {
                        ticks: {
                            callback: function(value, index, ticks) {
                                const limit = 5;
                                const v = this.getLabelForValue(value);

                                if (v.length > limit) return v.slice(0, limit) + '...';
                                return v;
                            }
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
