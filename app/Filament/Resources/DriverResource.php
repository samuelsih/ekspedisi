<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Uri;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DriverResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Delivery Man';

    protected static ?string $pluralLabel = 'Delivery Man';

    protected static ?string $modelLabel = 'delivery man';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->minLength(5)
                    ->maxLength(100),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->minLength(5)
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Driver::query()
                    ->select(['id', 'nik', 'name', 'created_at', 'deleted_at'])
                    ->withAvg('survey_answers', 'value')
                    ->withCount('surveys')
                    ->withCount('customer_survey_declines');
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('nik')->label('NIK')->searchable(),
                Tables\Columns\TextColumn::make('name')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('surveys_count')
                    ->label('Survey Submitted')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_survey_declines_count')
                    ->label('Survey Declined')
                    ->sortable(),
                Tables\Columns\TextColumn::make('survey_answers_avg_value')
                    ->default(0)
                    ->numeric(decimalPlaces: 3)
                    ->label('Avg Rating')
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver_contribution')
                    ->getStateUsing(function (Driver $record) {
                        return round(
                            ($record->surveys_count / max($record->surveys_count + $record->customer_survey_declines_count, 1))
                            * ($record->survey_answers_avg_value ?? 0),
                            3
                        );
                    })
                    ->label('Contribution'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('qr')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->action(function (Driver $driver) {
                        $nik = $driver->nik;
                        $name = $driver->name;

                        $uri = (string) Uri::of(env('APP_URL'))
                            ->withQuery(['nik' => $nik]);

                        return response()->streamDownload(function () use ($uri) {
                            echo QrCode::format('png')->size(360)->generate($uri);
                        },
                            "{$nik}-{$name}.png"
                        );
                    }),

                Tables\Actions\DeleteAction::make()
                    ->after(function (Driver $record) {
                        $record->customer_survey_declines()->delete();
                        $record->survey_answers()->delete();
                        $record->survey()->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Driver $record) {
                            $record->customer_survey_declines()->delete();
                            $record->survey_answers()->delete();
                            $record->survey()->delete();
                        }),
                ]),
            ])
            ->headerActions([
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
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
            'import',
        ];
    }
}
