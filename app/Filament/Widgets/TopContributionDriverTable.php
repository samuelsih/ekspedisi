<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopContributionDriverTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $model = Driver::class;

    protected static ?string $heading = 'Top Contribution Driver';

    public function table(Table $table): Table
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        return $table
            ->query(fn(Driver $driver) => $driver->query()
                ->select(['id', 'nik', 'name'])
                ->whereHas('surveys', fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
                    ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId))
                )
                ->whereHas('customer_survey_declines', fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('created_at', '<=', $end))
                    ->when($channelId, fn (QueryBuilder $q) => $q->where('channel_id', $channelId))
                )
                ->withAvg(['survey_answers' => fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
                ], 'value')
                ->withCount('surveys')
                ->withCount('customer_survey_declines')
                ->orderByDesc('contribution')
            )
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('nik')->label('NIK Supir')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama Supir')->searchable(),
                Tables\Columns\TextColumn::make('contribution')->label('Contribution'),
            ]);
    }
}
