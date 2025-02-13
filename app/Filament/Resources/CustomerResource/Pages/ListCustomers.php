<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                    ->color('primary')
                    ->label('Import toko')
                    ->importer(CustomerImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
