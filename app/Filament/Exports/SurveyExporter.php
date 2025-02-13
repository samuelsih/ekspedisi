<?php

namespace App\Filament\Exports;

use App\Models\Question;
use App\Models\Survey;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SurveyExporter extends Exporter
{
    protected static ?string $model = Survey::class;

    public static function getColumns(): array
    {
        $questions = Question::all();

        $stats = [];

        $questions->each(function (Question $question) use(&$stats) {
            $name = $question->name;

            $stats[] = ExportColumn::make("question_target_{$name}")
                ->getStateUsing(function (Survey $survey) use ($question) {
                    return $survey->survey_answers()->where('question_id', $question->id)->pluck('value')->first();
                })
                ->label($question->name);
        });

        $starters = [
            ExportColumn::make('customer.id_customer')->label('ID Customer'),
            ExportColumn::make('customer.name')->label('Nama Customer'),
            ExportColumn::make('channel.name')->label('Channel'),
            ExportColumn::make('driver.nik')->label('NIK Supir'),
            ExportColumn::make('driver.name')->label('Nama Supir'),
        ];

        foreach ($stats as $stat) {
            $starters[] = $stat;
        }

        return $starters;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your survey export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addDay();
    }

    public function getFileDisk(): string
    {
        return 'local';
    }
}
