<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->validateUsing([
                    'nik' => ['required', 'min:5', 'max:100'],
                    'name' => ['required', 'min:5', 'max:100'],
                ]),
            Actions\CreateAction::make(),
        ];
    }
}
