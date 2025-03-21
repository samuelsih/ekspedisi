<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BottomAvgCustomerSurveySubmit;
use App\Filament\Widgets\BottomPointCustomer;
use App\Filament\Widgets\ChannelStats;
use App\Filament\Widgets\Placeholder;
use App\Filament\Widgets\RatingQuestion;
use App\Filament\Widgets\TopBestDriver;
use App\Filament\Widgets\TopContributionDriver;
use App\Filament\Widgets\TopDriverTable;
use App\Filament\Widgets\TopPointCustomer;
use App\Filament\Widgets\TopSurveySubmmitedByCustomerTable;
use App\Filament\Widgets\TopWorstDriver;
use App\Filament\Widgets\WorstContributionDriver;
use App\Models\Channel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

            TopContributionDriver::class,
            WorstContributionDriver::class,
            Placeholder::class,

            TopDriverTable::class,
            TopSurveySubmmitedByCustomerTable::class,
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
                        Select::make('channelId')
                            ->label('Channel')
                            ->placeholder('All')
                            ->options(fn () => Channel::pluck('name', 'id')->toArray()),
                    ])
                    ->columns(3),
            ]);
    }
}
