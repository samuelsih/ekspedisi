<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebConfigResource\Pages;
use App\Models\WebConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebConfigResource extends Resource
{
    protected static ?string $model = WebConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Website';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->minLength(5)
                    ->maxLength(100),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->minLength(5)
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return WebConfig::query()->select(['id', 'name', 'value', 'created_at']);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('name')->label('Setting'),
                Tables\Columns\TextColumn::make('value')->label('Value'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebConfigs::route('/'),
            'edit' => Pages\EditWebConfig::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
