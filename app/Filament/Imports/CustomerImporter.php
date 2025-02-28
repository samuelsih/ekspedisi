<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id_customer')
                ->requiredMapping()
                ->rules(['required', 'min:1', 'max:100']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'min:1', 'max:100']),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        $customer = Customer::query()->where('id_customer', $this->data['id_customer'])->first();
        if(empty($customer)) {
            return new Customer;
        }

        $customer->name = $this->data['name'];
        $customer->save();

        return $customer;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
