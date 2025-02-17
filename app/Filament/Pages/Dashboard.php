<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BottomPointCustomer;
use App\Filament\Widgets\ChannelStats;
use App\Filament\Widgets\RatingQuestion;
use App\Filament\Widgets\BottomAvgCustomerSurveySubmit;
use App\Filament\Widgets\TopBestDriver;
use App\Filament\Widgets\TopDriverTable;
use App\Filament\Widgets\TopPointCustomer;
use App\Filament\Widgets\TopWorstDriver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ChannelStats::class,
            TopPointCustomer::class,
            BottomPointCustomer::class,
            BottomAvgCustomerSurveySubmit::class,
            RatingQuestion::class,
            TopBestDriver::class,
            TopWorstDriver::class,
            TopDriverTable::class,
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                    ])
                    ->columns(2),
            ]);
    }
}
