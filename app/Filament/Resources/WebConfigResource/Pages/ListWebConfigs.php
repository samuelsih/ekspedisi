<?php

namespace App\Filament\Resources\WebConfigResource\Pages;

use App\Filament\Resources\WebConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebConfigs extends ListRecords
{
    protected static string $resource = WebConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
