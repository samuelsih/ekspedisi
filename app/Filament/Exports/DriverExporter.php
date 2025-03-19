<?php

namespace App\Filament\Exports;

use App\Models\Driver;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class DriverExporter extends Exporter
{
    protected static ?string $model = Driver::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->withCount('surveys')
            ->withCount('customer_survey_declines');
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nik')->label('NIK'),
            ExportColumn::make('name')->label('Name'),
            ExportColumn::make('surveys_count')->label('Survey Submitted'),
            ExportColumn::make('customer_survey_declines_count')->label('Survey Declined'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your delivery man export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
