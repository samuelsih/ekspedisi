<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Jobs\ImportCustomerJob;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import-toko')
                ->label('Import Toko')
                ->color('primary')
                ->requiresConfirmation()
                ->form([
                    FileUpload::make('file')
                        ->disk('s3')
                        ->visibility('private'),
                ])
                ->action(function (array $data) {
                    $user = auth()->user();

                    ImportCustomerJob::dispatch($user, $data['file']);

                    Notification::make()
                        ->title('Upload Success')
                        ->success()
                        ->body('This will be processed in background. Check notifications when import is successful.')
                        ->send()
                        ->sendToDatabase($user);

                    event(new DatabaseNotificationsSent($user));
                }),
            Actions\CreateAction::make(),
        ];
    }
}
