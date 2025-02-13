<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SurveyExporter;
use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('customer.id_customer')->label('ID Customer')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<p>" . $record->customer->id_customer . "</p>");
                  }),
                Forms\Components\Placeholder::make('customer.name')->label('Nama Customer')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<p>" . $record->customer->name . "</p>");
                    }),
                Forms\Components\Placeholder::make('channel.name')->label('Channel')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<p>" . $record->channel->name . "</p>");
                    }),
                Forms\Components\Placeholder::make('driver.nik')->label('NIK Supir')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<p>" . $record->driver->nik . "</p>");
                    }),
                Forms\Components\Placeholder::make('driver.name')->label('Nama Supir')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<p>" . $record->driver->name . "</p>");
                    }),
                Forms\Components\Placeholder::make('survey')->label('Rating')
                    ->content(function ($record): HtmlString {
                        $answers = $record
                            ->loadMissing('survey_answers.question')
                            ->survey_answers
                            ->map(function ($answer) {
                                return "<p><strong>{$answer->question->title}: </strong> {$answer->value}</p>";
                        })->implode('');


                        return new HtmlString($answers);
                    }),
                Forms\Components\Placeholder::make('img_url')
                    ->label('Validasi Gambar')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<img src= '" . $record->img_url . "')>");
                  }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('customer.id_customer')->label('ID Customer'),
                Tables\Columns\TextColumn::make('customer.name')->label('Nama Customer'),
                Tables\Columns\TextColumn::make('channel.name')->label('Channel'),
                Tables\Columns\TextColumn::make('driver.nik')->label('NIK Supir'),
                Tables\Columns\TextColumn::make('driver.name')->label('Nama Supir'),
                Tables\Columns\ImageColumn::make('img_url')
                    ->label("Validasi Gambar")
                    ->square()
                    ->size(100),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(SurveyExporter::class),
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
            'index' => Pages\ListSurveys::route('/'),
            'view' => Pages\ViewSurvey::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
