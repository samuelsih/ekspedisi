<?php

namespace App\Filament\Imports;

use App\Models\Driver;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DriverImporter extends Importer
{
    protected static ?string $model = Driver::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules(['required', 'min:5', 'max:100']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'min:5', 'max:100']),
        ];
    }

    public function resolveRecord(): ?Driver
    {
        $driver = Driver::query()->where('nik', $this->data['nik'])->first();
        if(empty($driver)) {
            return new Driver;
        }

        $driver->name = $this->data['name'];
        $driver->save();

        return $driver;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your driver import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
