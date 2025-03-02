<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Exports\SurveyExporter;
use App\Filament\Resources\SurveyResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSurveys extends ListRecords
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(SurveyExporter::class)
                ->color('primary')
                ->visible(auth()->user()->can('export_survey')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Surveys'),
            'sus_img' => Tab::make('Only Suspicious Images')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('face_detected', false)),
        ];
    }
}
