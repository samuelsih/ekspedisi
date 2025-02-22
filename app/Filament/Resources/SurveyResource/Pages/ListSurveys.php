<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Exports\SurveyExporter;
use App\Filament\Resources\SurveyResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListSurveys extends ListRecords
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(SurveyExporter::class)
                ->color('primary')
                ->visible(auth()->user()->can('export_survey')),
        ];
    }
}
