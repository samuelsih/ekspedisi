<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerSurveyDeclineAnswerResource\Pages;
use App\Models\CustomerSurveyDeclineAnswer;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerSurveyDeclineAnswerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CustomerSurveyDeclineAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Anti Survey Answer';

    protected static ?string $pluralLabel = 'Anti Survey Answer';

    protected static ?string $modelLabel = 'anti survey answer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('answer')
                    ->required()
                    ->minLength(5)
                    ->maxLength(255),
                Forms\Components\Select::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Not Active',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return CustomerSurveyDeclineAnswer::query()->select(['id', 'answer', 'is_active', 'created_at', 'deleted_at']);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('answer'),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Not Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (CustomerSurveyDeclineAnswer $record) {
                        $record->customer_survey_declines()->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (CustomerSurveyDeclineAnswer $record) {
                            $record->customer_survey_declines()->delete();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerSurveyDeclineAnswers::route('/'),
            'create' => Pages\CreateCustomerSurveyDeclineAnswer::route('/create'),
            'edit' => Pages\EditCustomerSurveyDeclineAnswer::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
