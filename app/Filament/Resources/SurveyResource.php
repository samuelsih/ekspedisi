<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SurveyExporter;
use App\Filament\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             Infolists\Components\TextEntry::make('customer.id_customer')->label('ID Customer'),
    //             Infolists\Components\TextEntry::make('customer.name')->label('Nama Customer'),
    //             Infolists\Components\TextEntry::make('channel.name')->label('Channel'),
    //             Infolists\Components\TextEntry::make('driver.nik')->label('NIK Supir'),
    //             Infolists\Components\TextEntry::make('driver.name')->label('Nama Supir')
    //                 ->columnSpanFull(),
    //             Infolists\Components\TextEntry::make('survey')
    //                 ->label('Survey')
    //                 ->markdown()
    //                 ->getStateUsing(function (Survey $record): string {
    //                     $answers = $record
    //                         ->loadMissing('survey_answers.question')
    //                         ->survey_answers
    //                         ->map(fn ($answer) => "| **{$answer->question->title}** | {$answer->value} |")
    //                         ->implode("\n");

    //                     return "| **Question** | **Answer** |\n|---|---|\n".$answers;
    //                 }),
    //             Infolists\Components\ImageEntry::make('img_url')->label('Validasi Gambar')
    //                 ->width(400)
    //                 ->height(400),
    //         ])
    //         ->columns(2);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('survey_answers.question');
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('customer.id_customer')->label('ID Customer')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Nama Customer')->searchable(),
                Tables\Columns\TextColumn::make('channel.name')->label('Channel'),
                Tables\Columns\TextColumn::make('driver.nik')->label('NIK Supir')->searchable(),
                Tables\Columns\TextColumn::make('driver.name')->label('Nama Supir')->searchable(),
                Tables\Columns\ImageColumn::make('img_url')
                    ->label('Validasi Gambar')
                    ->square()
                    ->size(100),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_survey_answers')
                    ->color('primary')
                    ->modal()
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function (Survey $record) {
                        $rows = $record->survey_answers->map(fn ($answer) => "
                            <tr class='border-b text-sm'>
                                <td class='px-4 py-2 text-left'>{$answer->question->title}</td>
                                <td class='px-4 py-2 text-center'>
                                    <div class='flex items-center justify-center space-x-1'>
                                        <span>{$answer->value}</span>
                                        <span style='color: gold;'>&#9733;</span>
                                    </div>
                                </td>
                            </tr>
                        ")->implode("");

                        return new HtmlString("
                            <table>
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$rows}
                                </tbody>
                            </table>
                        ");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(SurveyExporter::class)
                    ->color('primary'),
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

    public static function canView(Model $record): bool
    {
        return false;
    }
}
