<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TopSurveySubmmitedByCustomerTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $model = Customer::class;

    protected static ?string $heading = 'Top Count Survey Submmited by Customers';

    public function table(Table $table): Table
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        return $table
            ->query(fn (Customer $customer) => $customer
                ->select(['id', 'id_customer', 'name'])
                ->withCount(['surveys' => fn (QueryBuilder $q) => $q
                    ->when($start, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '>=', $start))
                    ->when($end, fn (QueryBuilder $q) => $q->whereDate('survey_answers.created_at', '<=', $end)),
                ])
                ->orderByDesc('surveys_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('id_customer')->label('ID Customer')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('surveys_count')->label('Total'),
            ]);
    }
}
