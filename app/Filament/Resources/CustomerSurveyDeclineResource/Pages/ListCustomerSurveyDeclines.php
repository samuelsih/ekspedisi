<?php

namespace App\Filament\Resources\CustomerSurveyDeclineResource\Pages;

use App\Filament\Exports\CustomerSurveyDeclineExporter;
use App\Filament\Resources\CustomerSurveyDeclineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerSurveyDeclines extends ListRecords
{
    protected static string $resource = CustomerSurveyDeclineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->color('primary')
                ->label('Export anti survey')
                ->exporter(CustomerSurveyDeclineExporter::class)
                ->visible(auth()->user()->can('export_customer::survey::decline')),
        ];
    }
}
