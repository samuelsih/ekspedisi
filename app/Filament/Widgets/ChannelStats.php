<?php

namespace App\Filament\Widgets;

use App\Models\Channel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChannelStats extends BaseWidget
{
    protected function getStats(): array
    {
        $channels = Channel::query()
            ->withCount('surveys')
            ->get();

        $stats = [];
        $channels->each(function ($channel) use (&$stats) {
            $stats[] = Stat::make($channel->name, (int) $channel->surveys_count);

        });

        return $stats;
    }
}
