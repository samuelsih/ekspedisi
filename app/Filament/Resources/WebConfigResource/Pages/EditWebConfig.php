<?php

namespace App\Filament\Resources\WebConfigResource\Pages;

use App\Filament\Resources\WebConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebConfig extends EditRecord
{
    protected static string $resource = WebConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
