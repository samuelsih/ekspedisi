<?php

namespace App\Filament\Resources\CustomerSurveyDeclineAnswerResource\Pages;

use App\Filament\Resources\CustomerSurveyDeclineAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerSurveyDeclineAnswers extends ListRecords
{
    protected static string $resource = CustomerSurveyDeclineAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
