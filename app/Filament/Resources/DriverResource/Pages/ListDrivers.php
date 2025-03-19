<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Exports\DriverExporter;
use App\Filament\Imports\DriverImporter;
use App\Filament\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->color('primary')
                ->label('Import delivery man')
                ->importer(DriverImporter::class)
                ->visible(auth()->user()->can('import_driver')),

            Actions\ExportAction::make()
                ->color('primary')
                ->label('Export delivery man')
                ->exporter(DriverExporter::class)
                ->visible(auth()->user()->can('export_driver')),

            Actions\CreateAction::make(),
        ];
    }
}
