<?php

namespace App\Filament\Resources\DriverResource\Pages;

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
                ->importer(DriverImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
