<?php

namespace App\Filament\Resources\CustomerSurveyDeclineAnswerResource\Pages;

use App\Filament\Resources\CustomerSurveyDeclineAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerSurveyDeclineAnswer extends EditRecord
{
    protected static string $resource = CustomerSurveyDeclineAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
