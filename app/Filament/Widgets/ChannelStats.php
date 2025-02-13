<?php

namespace App\Filament\Widgets;

use App\Models\Channel;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ChannelStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        $channels = Channel::query()
            ->withCount([
                'surveys' => fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
            ])
            ->get();

        $stats = [];
        $channels->each(function ($channel) use (&$stats) {
            $stats[] = Stat::make($channel->name, (int) $channel->surveys_count ?? 0);
        });

        return $stats;
    }
}
