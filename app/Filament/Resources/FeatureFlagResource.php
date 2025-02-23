<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureFlagResource\Pages;
use App\Models\Feature;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FeatureFlagResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Access Control';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(fn (Feature $record) => str($record->name)->title()->replace('-', ' '))
                    ->label('Name'),
            ])
            ->actions([
                Tables\Actions\Action::make('is_active')
                    ->hidden(auth()->user()->can('update_features'))
                    ->requiresConfirmation()
                    ->icon(fn (Feature $record) => $record->is_active ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check')
                    ->label(fn (Feature $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->color(fn (Feature $record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (Feature $record) {
                        $active = $record->is_active;
                        return $record->update(['is_active' => $active ? false : true]);
                    })
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
            'index' => Pages\ListFeatureFlags::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'update',
        ];
    }
}
