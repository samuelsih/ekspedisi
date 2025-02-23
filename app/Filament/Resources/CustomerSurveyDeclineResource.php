<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerSurveyDeclineResource\Pages;
use App\Models\CustomerSurveyDecline;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomerSurveyDeclineResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CustomerSurveyDecline::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationLabel = 'Anti Survey';

    protected static ?string $pluralLabel = 'Anti Survey';

    protected static ?string $modelLabel = 'anti survey';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('customer.id_customer')->label('ID Customer')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Nama Customer')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('channel.name')->label('Channel'),
                Tables\Columns\TextColumn::make('driver.nik')->label('NIK Supir')->searchable(),
                Tables\Columns\TextColumn::make('driver.name')->label('Nama Supir')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terbuat')
                    ->formatStateUsing(fn (string $state) => Carbon::parse($state)->timezone('Asia/Jakarta')->format('M d Y, H:i:s')),
                Tables\Columns\TextColumn::make('reason')->label('Alasan')->wrap(),
            ])
            ->filters([
                SelectFilter::make('channel_id')
                    ->relationship('channel', 'name')
                    ->label('Channel'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCustomerSurveyDeclines::route('/'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'delete',
            'delete_any',
        ];
    }
}
