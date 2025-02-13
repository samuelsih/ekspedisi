<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopPointCustomer extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top 5 Poin Customer';

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
            ->orderByDesc('surveys_count')
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

    protected function getType(): string
    {
        return 'bar';
    }
}
