<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Imports\CustomerExcelImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->use(CustomerExcelImport::class)
                ->color('primary')
                ->label('Import toko')
                ->visible(auth()->user()->can('import_customer'))
                ->validateUsing([
                    'id_customer' => ['required', 'min:1', 'max:100'],
                    'name' => ['required', 'min:1', 'max:255'],
                ])
                ->sampleExcel(
                    sampleData: [
                        ['id_customer' => '123123', 'name' => 'Test A'],
                        ['id_customer' => '456456', 'name' => 'Test B'],
                    ],
                    fileName: 'sample-customer.xlsx',
                    sampleButtonLabel: 'Download Template',
                ),

            Actions\CreateAction::make(),
        ];
    }
}
