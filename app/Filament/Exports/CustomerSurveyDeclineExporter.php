<?php

namespace App\Filament\Exports;

use App\Models\CustomerSurveyDecline;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class CustomerSurveyDeclineExporter extends Exporter
{
    protected static ?string $model = CustomerSurveyDecline::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->with(['customer', 'channel', 'driver', 'customer_survey_decline_answer']);
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('customer.id_customer')->label('ID Customer'),
            ExportColumn::make('customer.name')->label('Nama Customer'),
            ExportColumn::make('channel.name')->label('Channel'),
            ExportColumn::make('driver.nik')->label('NIK Supir'),
            ExportColumn::make('driver.name')->label('Nama Supir'),
            ExportColumn::make('customer_survey_decline_answer.answer')->label('Alasan'),
            ExportColumn::make('created_at')->label('Waktu Terbuat')->getStateUsing(function (CustomerSurveyDecline $antiSurvey) {
                return $antiSurvey->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
            }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your anti survey export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addDay();
    }

    public function getFileDisk(): string
    {
        return 's3';
    }
}
