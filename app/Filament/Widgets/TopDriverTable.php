<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopDriverTable extends BaseWidget
{
    protected static ?string $model = Driver::class;

    protected static ?string $heading = 'Top Driver By Rating';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (Driver $driver) => $driver->withAvg('survey_answers', 'value')->orderByDesc('survey_answers_avg_value'))
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('nik')->label('NIK Supir')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama Supir')->searchable(),
                Tables\Columns\TextColumn::make('survey_answers_avg_value')->label('Avg Rating'),
            ]);
    }
}
