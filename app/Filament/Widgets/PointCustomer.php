<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;

class PointCustomer extends ChartWidget
{
    protected static ?string $heading = 'Poin Customer';

    protected function getData(): array
    {
        $customers = Customer::query()
            ->withCount('surveys')
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
