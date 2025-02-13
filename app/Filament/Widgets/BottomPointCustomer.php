<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class BottomPointCustomer extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Bottom 5 Poin Customer';

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        $customers = Customer::query()
            ->withCount([
                'surveys' => fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('surveys.created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('surveys.created_at', '<=', $end))
            ])
            ->limit(10)
            ->orderBy('surveys_count', 'asc')
            ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Poin',
                        'data' => $customers->pluck('surveys_count')->toArray(),
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
