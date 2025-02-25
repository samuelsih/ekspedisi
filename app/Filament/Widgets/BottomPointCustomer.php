<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasExtraJSBar;
use App\Models\Customer;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BottomPointCustomer extends ApexChartWidget
{
    use HasExtraJSBar, InteractsWithPageFilters;

    protected static ?string $heading = 'Bottom 10 Point Customer';

    protected function getOptions(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $customers = Customer::query()
            ->select(['name'])
            ->whereHas('surveys', fn (QueryBuilder $q) => $q
                ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
                ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId))
            )
            ->withCount([
                'surveys' => fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
                    ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId)),
            ])
            ->limit(10)
            ->orderBy('surveys_count', 'asc')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => $customers->pluck('surveys_count')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $customers->map(fn ($customer) => "{$customer->name}")->toArray(),
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
