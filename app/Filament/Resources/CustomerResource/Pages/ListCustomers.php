<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->validateUsing([
                    'id_customer' => ['required', 'min:5', 'max:100'],
                    'name' => ['required', 'min:5', 'max:100'],
                ]),
            Actions\CreateAction::make(),
        ];
    }
}
